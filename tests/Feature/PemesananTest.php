<?php

namespace Tests\Feature;

use App\Models\InstallationService;
use App\Models\Order;
use App\Models\Pembayaran;
use App\Models\ServiceBooking;
use App\Models\Setting;

class PemesananTest extends HitsbahTestCase
{
    public function test_tidak_boleh_menambah_ke_keranjang_melebihi_stok(): void
    {
        $user = $this->buatUser();
        $produk = $this->buatProduk(stok: 3);

        $this->actingAs($user)
            ->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 5])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('cart_item', 0);
    }

    public function test_tambah_keranjang_via_ajax_mengembalikan_json_dan_jumlah_item(): void
    {
        $user = $this->buatUser();
        $produk = $this->buatProduk(stok: 5);

        $this->actingAs($user)
            ->postJson(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 2])
            ->assertOk()
            ->assertJson(['cart_count' => 2]);

        $this->actingAs($user)
            ->postJson(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 9])
            ->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    public function test_checkout_membuat_pesanan_dan_mengurangi_stok(): void
    {
        $user = $this->buatUser();
        $produk = $this->buatProduk(stok: 5, harga: 150000);

        $this->actingAs($user)->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 2]);
        $this->actingAs($user)->post(route('user.checkout.store'))->assertRedirect();

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);
        $this->assertEquals(300000, (float) $order->total_pembayaran);
        $this->assertSame(3, $produk->fresh()->stok);
        $this->assertDatabaseCount('cart_item', 0);
    }

    public function test_checkout_ditolak_bila_stok_berubah_menjadi_tidak_cukup(): void
    {
        $user = $this->buatUser();
        $produk = $this->buatProduk(stok: 5);

        $this->actingAs($user)->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 4]);
        $produk->update(['stok' => 1]); // stok berkurang setelah masuk keranjang

        $this->actingAs($user)->post(route('user.checkout.store'))->assertSessionHasErrors('keranjang');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, $produk->fresh()->stok);
    }

    public function test_alur_pembayaran_diverifikasi_lalu_pesanan_selesai(): void
    {
        $user = $this->buatUser();
        $admin = $this->buatUser('admin');
        $produk = $this->buatProduk(stok: 5);

        $this->actingAs($user)->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 1]);
        $this->actingAs($user)->post(route('user.checkout.store'));
        $order = Order::first();

        $this->actingAs($user)->post(route('user.payment.store', $order->id_order), ['metode' => 'tunai'])
            ->assertSessionHasNoErrors();
        $pembayaran = Pembayaran::first();
        $this->assertSame('pending', $pembayaran->status);

        // Pesanan belum boleh diselesaikan sebelum pembayaran diverifikasi.
        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'diterima']);
        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'diproses'])
            ->assertSessionHas('error');

        $this->actingAs($admin)->patch(route('admin.payments.verify', $pembayaran))->assertSessionHas('success');
        $this->assertSame('diproses', $order->fresh()->status);
        $this->assertSame('diverifikasi', $pembayaran->fresh()->status);

        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'selesai']);
        $this->assertSame('selesai', $order->fresh()->status);
        $this->assertSame('berhasil', $pembayaran->fresh()->status);
    }

    public function test_menolak_pesanan_mengembalikan_stok(): void
    {
        $user = $this->buatUser();
        $admin = $this->buatUser('admin');
        $produk = $this->buatProduk(stok: 5);

        $this->actingAs($user)->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 2]);
        $this->actingAs($user)->post(route('user.checkout.store'));
        $order = Order::first();
        $this->assertSame(3, $produk->fresh()->stok);

        $this->actingAs($admin)->patch(route('admin.orders.status', $order), ['status' => 'ditolak']);

        $this->assertSame('ditolak', $order->fresh()->status);
        $this->assertSame(5, $produk->fresh()->stok);
    }

    public function test_user_tidak_dapat_melihat_pesanan_milik_orang_lain(): void
    {
        $pemilik = $this->buatUser();
        $lain = $this->buatUser();
        $produk = $this->buatProduk();

        $this->actingAs($pemilik)->post(route('user.cart.add'), ['id_produk' => $produk->id_produk, 'jumlah' => 1]);
        $this->actingAs($pemilik)->post(route('user.checkout.store'));
        $order = Order::first();

        $this->actingAs($lain)->get(route('user.orders.show', $order->id_order))->assertNotFound();
    }

    public function test_booking_jasa_menolak_jadwal_yang_kapasitasnya_penuh(): void
    {
        Setting::simpan('kapasitas_pemasangan', '1');
        $service = InstallationService::create(['nama' => 'Jasa Uji', 'deskripsi' => 'x', 'harga' => 100000, 'durasi_pemasangan' => 60]);
        $besok = now()->addDay()->toDateString();

        $this->actingAs($this->buatUser())->post(route('user.services.booking.store'), [
            'id_services' => $service->id_services, 'tanggal' => $besok, 'jam' => '10:00',
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, ServiceBooking::count());

        // Pelanggan lain memilih jam yang sama -> kapasitas (1) sudah penuh.
        $this->actingAs($this->buatUser())->post(route('user.services.booking.store'), [
            'id_services' => $service->id_services, 'tanggal' => $besok, 'jam' => '10:00',
        ])->assertSessionHasErrors('jam');
        $this->assertSame(1, ServiceBooking::count());
    }

    public function test_booking_di_luar_jam_operasional_ditolak(): void
    {
        $service = InstallationService::create(['nama' => 'Jasa Uji', 'deskripsi' => 'x', 'harga' => 100000, 'durasi_pemasangan' => 60]);

        $this->actingAs($this->buatUser())->post(route('user.services.booking.store'), [
            'id_services' => $service->id_services, 'tanggal' => now()->addDay()->toDateString(), 'jam' => '22:00',
        ])->assertSessionHasErrors('jam');
    }
}
