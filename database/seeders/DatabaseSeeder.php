<?php

namespace Database\Seeders;

use App\Models\InstallationService;
use App\Models\Kategori;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\Review;
use App\Models\ServiceBooking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Data awal + data demo.
 *
 * Akun demo (password semuanya: "password"):
 *   user@example.com | admin@example.com | superadmin@example.com
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // hasil seeder konsisten setiap dijalankan

        $this->pengaturan();
        $users = $this->akun();
        $kategori = $this->kategori();
        $produk = $this->produk($kategori);
        $jasa = $this->jasa();
        $this->transaksiDemo($users, $produk, $jasa);
    }

    private function pengaturan(): void
    {
        $default = [
            'site_name' => 'HITSBAH Transport',
            'nama_usaha' => 'HITSBAH Transport',
            'alamat' => 'Desa Pengauban, Kecamatan Lelea, Kabupaten Indramayu',
            'kontak' => '0812-0000-0000',
            'email' => 'info@hitsbahtransport.test',
            'deskripsi' => 'HITSBAH Transport menyediakan aksesoris mobil berkualitas dan jasa pemasangan profesional langsung di bengkel kami.',
            'jam_buka' => '08:00',
            'jam_tutup' => '16:00',
            'kapasitas_pemasangan' => '2',
            'info_pembayaran' => "QRIS / DANA / OVO / GoPay: 0812-0000-0000 a.n. HITSBAH Transport.\nUnggah bukti pembayaran setelah melakukan pembayaran.",
        ];

        foreach ($default as $key => $value) {
            Setting::simpan($key, $value);
        }
    }

    /** @return array{user: \Illuminate\Support\Collection, admin: \Illuminate\Support\Collection, super: User} */
    private function akun(): array
    {
        $buat = function (string $nama, string $email, string $role, string $alamat): User {
            $user = new User();
            $user->forceFill([
                'nama' => $nama,
                'email' => $email,
                'password' => Hash::make('password'),
                'alamat' => $alamat,
                'role' => $role,
            ])->save();

            return $user;
        };

        $super = $buat('Super Admin HITSBAH', 'superadmin@example.com', User::ROLE_SUPER_ADMIN, 'Desa Pengauban, Lelea, Indramayu');

        $admin = collect([
            $buat('Admin HITSBAH', 'admin@example.com', User::ROLE_ADMIN, 'Desa Pengauban, Lelea, Indramayu'),
            $buat('Rina Kasir', 'rina.admin@example.com', User::ROLE_ADMIN, 'Kec. Lelea, Indramayu'),
        ]);

        $pelanggan = [
            ['Budi Santoso', 'user@example.com', 'Jl. Raya Lelea No. 12, Lelea, Indramayu'],
            ['Siti Aminah', 'siti@example.com', 'Desa Tamansari, Lelea, Indramayu'],
            ['Agus Setiawan', 'agus@example.com', 'Jl. Jenderal Sudirman No. 45, Indramayu'],
            ['Dewi Lestari', 'dewi@example.com', 'Desa Kedokan Gabus, Indramayu'],
            ['Rudi Hartono', 'rudi@example.com', 'Desa Bongas, Indramayu'],
            ['Nur Hidayah', 'nur@example.com', 'Desa Sukra, Indramayu'],
            ['Eko Prasetyo', 'eko@example.com', 'Desa Jatibarang, Indramayu'],
            ['Yuni Rahmawati', 'yuni@example.com', 'Desa Haurgeulis, Indramayu'],
            ['Dedi Kurniawan', 'dedi@example.com', 'Desa Kandanghaur, Indramayu'],
            ['Fitri Handayani', 'fitri@example.com', 'Desa Losarang, Indramayu'],
        ];

        $users = collect($pelanggan)->map(fn ($p) => $buat($p[0], $p[1], User::ROLE_USER, $p[2]));

        return ['user' => $users, 'admin' => $admin, 'super' => $super];
    }

    private function kategori(): array
    {
        $data = [
            'interior' => ['Interior', 'Sarung jok, karpet, dan perlengkapan kabin agar berkendara lebih nyaman.'],
            'eksterior' => ['Eksterior', 'Variasi dan pelindung bodi untuk tampilan mobil yang lebih gagah.'],
            'audio' => ['Audio & Elektronik', 'Head unit, kamera, dan perangkat elektronik pendukung berkendara.'],
            'lampu' => ['Lampu & Pencahayaan', 'Lampu utama, fog lamp, dan pencahayaan interior.'],
            'keamanan' => ['Keamanan & Perlindungan', 'Alarm, sensor, dan pengaman untuk melindungi kendaraan Anda.'],
        ];

        $hasil = [];
        foreach ($data as $kunci => [$nama, $deskripsi]) {
            $hasil[$kunci] = Kategori::create(['nama' => $nama, 'deskripsi' => $deskripsi]);
        }

        return $hasil;
    }

    private function produk(array $k): \Illuminate\Support\Collection
    {
        $data = [
            ['interior', 'Sarung Jok Kulit Sintetis Premium', 'Sarung jok kulit sintetis tebal dengan jahitan rapi, mudah dibersihkan, dan nyaman untuk perjalanan jauh. Satu set untuk 2 baris jok.', 1250000, 25],
            ['interior', 'Karpet Dasar Mobil 3D Anti Air', 'Karpet 3D dengan tepi tinggi untuk menahan air dan lumpur. Bahan anti slip, mudah dicuci, dan tahan lama.', 450000, 40],
            ['interior', 'Cover Stir Kulit Anti Slip', 'Cover setir berbahan kulit dengan grip nyaman dan tidak licin, cocok untuk setir diameter 36–40 cm.', 120000, 60],
            ['eksterior', 'Spoiler Belakang Universal ABS', 'Spoiler belakang berbahan ABS ringan dan kuat, memberi tampilan sporty. Dilengkapi lem 3M dan baut pengunci.', 650000, 18],
            ['eksterior', 'Karet Lumpur (Mudguard) Set 4 Pcs', 'Pelindung cipratan lumpur dan batu kecil untuk keempat roda. Bahan karet elastis dan awet.', 180000, 35],
            ['eksterior', 'Door Visor Talang Air Slim', 'Talang air jendela model slim berbahan akrilik tahan panas. Mengurangi tampias hujan saat jendela dibuka sedikit.', 350000, 22],
            ['audio', 'Head Unit Android 9 Inci 2/32 GB', 'Head unit layar sentuh 9 inci dengan Android, Bluetooth, GPS, dan mirror link. RAM 2 GB, penyimpanan 32 GB.', 1850000, 12],
            ['audio', 'Kamera Mundur Parkir HD', 'Kamera mundur resolusi HD dengan night vision dan garis bantu parkir. Tahan air dan debu (IP67).', 275000, 30],
            ['audio', 'Dashcam Full HD 1080p Dual Lens', 'Dashcam kamera depan dan kabin, perekaman loop, sensor tabrakan, dan mode parkir.', 725000, 15],
            ['lampu', 'Lampu Utama LED H4 Super Terang', 'Lampu LED H4 dengan pendingin kipas, cahaya putih terang 6000K dan sorot rapi tanpa menyilaukan.', 340000, 28],
            ['lampu', 'Fog Lamp LED Anti Kabut', 'Lampu kabut LED untuk kondisi hujan dan berkabut. Dilengkapi bracket dan kabel siap pasang.', 280000, 26],
            ['lampu', 'Ambient Light Interior RGB', 'Lampu suasana kabin RGB dengan kontrol aplikasi, ratusan pilihan warna, dan mode mengikuti musik.', 220000, 33],
            ['keamanan', 'Alarm Mobil Remote Pintar', 'Alarm dengan remote jarak jauh, sensor getar, dan sirine 120 dB. Dilengkapi fitur central lock otomatis.', 480000, 20],
            ['keamanan', 'Sensor Parkir Belakang 4 Titik', 'Sensor parkir 4 titik dengan buzzer dan indikator jarak untuk membantu parkir mundur lebih aman.', 260000, 24],
            ['keamanan', 'Kunci Pengaman Stir (Steering Lock)', 'Kunci stir berbahan baja tebal, menjadi pengaman tambahan dari pencurian kendaraan.', 185000, 4],
        ];

        return collect($data)->map(fn ($p) => Produk::create([
            'id_kategori' => $k[$p[0]]->id_kategori,
            'nama' => $p[1],
            'deskripsi' => $p[2],
            'harga' => $p[3],
            'stok' => $p[4] + 50, // stok tambahan agar data demo transaksi tidak membuat stok habis
            'gambar' => null, // tampilan memakai placeholder bila gambar kosong
        ]))->each(function (Produk $p) {
            if ($p->nama === 'Kunci Pengaman Stir (Steering Lock)') {
                $p->update(['stok' => 4]); // contoh stok menipis di dashboard
            }
        });
    }

    private function jasa(): \Illuminate\Support\Collection
    {
        $data = [
            ['Pemasangan Head Unit & Audio', 'Pemasangan head unit, speaker, dan perangkat audio lengkap dengan perapian kabel dan pengujian suara.', 250000, 120],
            ['Pemasangan Kamera Mundur & Dashcam', 'Pemasangan kamera mundur atau dashcam dengan jalur kabel tersembunyi dan rapi.', 150000, 90],
            ['Pemasangan Lampu LED & Fog Lamp', 'Pemasangan lampu utama LED dan fog lamp, termasuk penyetelan arah sorot lampu.', 175000, 90],
            ['Pemasangan Sensor Parkir & Alarm', 'Pemasangan sensor parkir atau alarm mobil dengan konfigurasi dan uji fungsi.', 200000, 120],
            ['Pemasangan Sarung Jok & Karpet', 'Pemasangan sarung jok dan karpet dasar sesuai ukuran kendaraan agar pas dan rapi.', 100000, 60],
        ];

        return collect($data)->map(fn ($j) => InstallationService::create([
            'nama' => $j[0], 'deskripsi' => $j[1], 'harga' => $j[2], 'durasi_pemasangan' => $j[3],
        ]));
    }

    /** Pesanan, pembayaran, jadwal, dan ulasan contoh agar dashboard dan laporan tidak kosong. */
    private function transaksiDemo(array $akun, $produk, $jasa): void
    {
        $users = $akun['user'];
        $metode = ['qris', 'dana', 'ovo', 'gopay', 'tunai'];

        $buat = function (User $user, Carbon $tanggal, string $status, ?string $statusBayar, bool $denganJasa, ?Carbon $jadwal = null) use ($produk, $jasa, $metode) {
            $order = Order::create([
                'id_user' => $user->id_user,
                'tanggal_order' => $tanggal,
                'total_pembayaran' => 0,
                'status' => $status,
            ]);

            $total = 0;
            foreach ($produk->shuffle()->take(mt_rand(1, 3)) as $p) {
                $jumlah = mt_rand(1, 2);
                $subtotal = (float) $p->harga * $jumlah;
                OrderItem::create([
                    'id_order' => $order->id_order, 'id_produk' => $p->id_produk,
                    'jumlah' => $jumlah, 'harga_satuan' => $p->harga, 'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            if ($denganJasa) {
                $j = $jasa->random();
                $statusBooking = match (true) {
                    $status === 'selesai' => 'selesai',
                    $status === 'ditolak' => 'dibatalkan',
                    $status === 'diproses' => 'dijadwalkan',
                    default => 'menunggu',
                };
                ServiceBooking::create([
                    'id_user' => $user->id_user, 'id_order' => $order->id_order, 'id_services' => $j->id_services,
                    'jadwal_pemasangan' => $jadwal ?? $tanggal->copy()->addDays(2)->setTime(9, 0),
                    'status_pemasangan' => $statusBooking, 'harga' => $j->harga,
                ]);
                $total += (float) $j->harga;
            }

            $order->update(['total_pembayaran' => $total]);

            if ($statusBayar) {
                Pembayaran::create([
                    'id_order' => $order->id_order,
                    'metode' => $metode[array_rand($metode)],
                    'total_pembayaran' => $total,
                    'status' => $statusBayar,
                    'tanggal_bayar' => $tanggal->copy()->addHours(2),
                    'catatan' => $statusBayar === 'ditolak' ? 'Bukti pembayaran tidak terbaca.' : null,
                ]);
            }

            return $order;
        };

        // 18 pesanan selesai tersebar ±10 bulan terakhir (untuk grafik & laporan)
        for ($i = 0; $i < 18; $i++) {
            $tanggal = now()->subDays(mt_rand(10, 300))->setTime(mt_rand(8, 16), mt_rand(0, 59));
            $buat($users->random(), $tanggal, 'selesai', 'berhasil', $i % 3 === 0);
        }

        // Pesanan aktif dengan berbagai status
        $buat($users[0], now()->subDays(1), 'pending', null, false);
        $buat($users[1], now()->subDays(1), 'pending', 'pending', true, now()->addDays(3)->setTime(9, 0));
        $buat($users[2], now()->subDays(2), 'pending', 'pending', false);
        $buat($users[3], now()->subDays(3), 'diproses', 'diverifikasi', true, now()->addDays(2)->setTime(13, 0));
        $buat($users[4], now()->subDays(4), 'diproses', 'diverifikasi', false);
        $buat($users[0], now()->subDays(5), 'diproses', 'diverifikasi', true, now()->addDays(4)->setTime(10, 0));
        $buat($users[5], now()->subDays(6), 'ditolak', 'ditolak', true);
        $buat($users[6], now()->subDays(8), 'ditolak', null, false);

        // Ulasan dari pesanan yang selesai
        $komentar = [
            [5, 'Kualitas barang bagus dan sesuai deskripsi. Pemasangannya rapi, sangat puas!'],
            [5, 'Pengiriman cepat, produk original. Pasti beli lagi di sini.'],
            [4, 'Barang bagus, harga bersaing. Hanya saja packing bisa lebih rapi.'],
            [5, 'Mantap, mobil jadi tampil beda. Admin ramah dan responsif.'],
            [4, 'Sesuai harapan, pemasangan cepat dan hasil memuaskan.'],
            [3, 'Produk oke, tapi stok sempat kosong sehingga harus menunggu.'],
            [5, 'Sangat direkomendasikan untuk yang cari aksesoris mobil di Indramayu.'],
            [4, 'Kualitas sepadan dengan harga. Terima kasih HITSBAH!'],
        ];

        $sudah = [];
        $items = OrderItem::query()->with('order')->whereHas('order', fn ($q) => $q->where('status', 'selesai'))->get()->shuffle();
        $n = 0;
        foreach ($items as $item) {
            $kunci = $item->order->id_user.'-'.$item->id_produk;
            if (isset($sudah[$kunci]) || $n >= 14) {
                continue;
            }
            $sudah[$kunci] = true;
            [$rating, $komen] = $komentar[$n % count($komentar)];
            Review::create([
                'id_user' => $item->order->id_user,
                'id_produk' => $item->id_produk,
                'rating' => $rating,
                'komen' => $komen,
                'balasan_admin' => $n % 3 === 0 ? 'Terima kasih atas ulasannya! Kami senang Anda puas dengan layanan HITSBAH Transport.' : null,
                'tanggal_balasan' => $n % 3 === 0 ? now()->subDays(mt_rand(1, 20)) : null,
            ]);
            $n++;
        }
    }
}
