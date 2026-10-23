<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /** Transisi status pesanan yang diizinkan. */
    private const TRANSISI = [
        Order::STATUS_PENDING => [Order::STATUS_DITERIMA, Order::STATUS_DITOLAK],
        Order::STATUS_DITERIMA => [Order::STATUS_DIPROSES, Order::STATUS_DITOLAK],
        Order::STATUS_DIPROSES => [Order::STATUS_SELESAI],
        Order::STATUS_SELESAI => [],
        Order::STATUS_DITOLAK => [],
    ];

    public function __construct(private BookingService $bookingService)
    {
    }

    public function transisiTersedia(Order $order): array
    {
        return self::TRANSISI[$order->status] ?? [];
    }

    /**
     * Checkout keranjang menjadi pesanan. Stok divalidasi ulang dan dikurangi di dalam transaksi database.
     * Harga selalu diambil dari database (bukan dari input pengguna).
     *
     * @param  array{id_services:int, tanggal:string, jam:string}|null  $jasa  Jasa pemasangan opsional
     */
    public function checkoutKeranjang(User $user, ?array $jasa = null): Order
    {
        return DB::transaction(function () use ($user, $jasa) {
            $cart = $user->cart()->first();
            $cartItems = $cart ? $cart->items()->get() : collect();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['keranjang' => 'Keranjang belanja Anda masih kosong.']);
            }

            $order = Order::create([
                'id_user' => $user->id_user,
                'tanggal_order' => now(),
                'total_pembayaran' => 0,
                'status' => Order::STATUS_PENDING,
            ]);

            $total = 0;

            foreach ($cartItems as $cartItem) {
                $produk = Produk::query()->lockForUpdate()->find($cartItem->id_produk);

                if (! $produk) {
                    throw ValidationException::withMessages(['keranjang' => 'Salah satu produk di keranjang sudah tidak tersedia.']);
                }

                if ($cartItem->jumlah < 1) {
                    throw ValidationException::withMessages(['keranjang' => "Jumlah produk {$produk->nama} tidak valid."]);
                }

                if ($produk->stok < $cartItem->jumlah) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Stok produk {$produk->nama} tidak mencukupi (tersisa {$produk->stok}).",
                    ]);
                }

                $subtotal = (float) $produk->harga * $cartItem->jumlah;

                OrderItem::create([
                    'id_order' => $order->id_order,
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $cartItem->jumlah,
                    'harga_satuan' => $produk->harga,
                    'subtotal' => $subtotal,
                ]);

                $produk->decrement('stok', $cartItem->jumlah);
                $total += $subtotal;
            }

            if ($jasa) {
                $booking = $this->bookingService->buat($order, $user, $jasa['id_services'], $jasa['tanggal'], $jasa['jam']);
                $total += (float) $booking->harga;
            }

            $order->update(['total_pembayaran' => $total]);
            $cart->items()->delete();

            return $order;
        });
    }

    /** Pesanan khusus jasa pemasangan (tanpa produk). */
    public function pesanJasa(User $user, int $idServices, string $tanggal, string $jam): Order
    {
        return DB::transaction(function () use ($user, $idServices, $tanggal, $jam) {
            $order = Order::create([
                'id_user' => $user->id_user,
                'tanggal_order' => now(),
                'total_pembayaran' => 0,
                'status' => Order::STATUS_PENDING,
            ]);

            $booking = $this->bookingService->buat($order, $user, $idServices, $tanggal, $jam);
            $order->update(['total_pembayaran' => $booking->harga]);

            return $order;
        });
    }

    /**
     * Ubah status pesanan oleh Admin / Super Admin.
     *
     * @throws DomainException bila transisi tidak diizinkan
     */
    public function ubahStatus(Order $order, string $statusBaru): Order
    {
        return DB::transaction(function () use ($order, $statusBaru) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id_order);

            if (! in_array($statusBaru, self::TRANSISI[$order->status] ?? [], true)) {
                throw new DomainException('Perubahan status pesanan tidak diizinkan dari "'.$order->status.'" ke "'.$statusBaru.'".');
            }

            $order->load('pembayaran');
            $statusBayar = $order->pembayaran?->status;

            if ($statusBaru === Order::STATUS_DIPROSES && $statusBayar !== 'diverifikasi') {
                throw new DomainException('Pesanan belum dapat diproses karena pembayaran belum diverifikasi.');
            }

            if ($statusBaru === Order::STATUS_SELESAI) {
                if ($statusBayar !== 'diverifikasi') {
                    throw new DomainException('Pesanan belum dapat diselesaikan karena pembayaran belum diverifikasi.');
                }
                $order->pembayaran->update(['status' => 'berhasil']);
            }

            if ($statusBaru === Order::STATUS_DITOLAK) {
                $this->kembalikanStok($order);
                $order->bookings()->whereIn('status_pemasangan', ['menunggu', 'dijadwalkan'])->update(['status_pemasangan' => 'dibatalkan']);
                if ($order->pembayaran && $order->pembayaran->status === 'pending') {
                    $order->pembayaran->update(['status' => 'ditolak', 'catatan' => 'Pesanan ditolak oleh admin.']);
                }
            }

            $order->update(['status' => $statusBaru]);

            return $order;
        });
    }

    public function kembalikanStok(Order $order): void
    {
        foreach ($order->items()->get() as $item) {
            Produk::query()->whereKey($item->id_produk)->increment('stok', $item->jumlah);
        }
    }

    /** Pembayaran terkait pesanan (dibuat saat user mengirim pembayaran). */
    public function pembayaranDari(Order $order): ?Pembayaran
    {
        return $order->pembayaran;
    }
}
