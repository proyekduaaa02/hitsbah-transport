# HITSBAH Transport

**Pengembangan Platform Penjualan dan Jasa Pemasangan Aksesoris Mobil Berbasis Website di HITSBAH Transport**

Website e-commerce + sistem booking jasa pemasangan aksesoris mobil. Dibangun dengan **Laravel 13**, **MySQL**, **Blade**, **Tailwind CSS 4**, **Alpine.js**, dan **Chart.js**. Dapat di-install ke layar utama HP sebagai **PWA** (tetap berjalan di browser, bukan aplikasi Android/iOS native).

## Prasyarat

| Kebutuhan | Versi |
|---|---|
| PHP | **8.3 atau lebih** (syarat Laravel 13) — ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`/`imagick` tidak wajib |
| MySQL / MariaDB | via XAMPP |
| Composer | 2.x |
| Node.js | 20 atau lebih (untuk build Tailwind/Vite) |

## Langkah Instalasi

```bash
# 1. Ekstrak proyek lalu masuk ke foldernya
cd hitsbah-transport

# 2. Pasang dependensi
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

4. Nyalakan **Apache + MySQL** di XAMPP, buka phpMyAdmin, lalu buat database kosong bernama **`hitsbah_transport`** (collation `utf8mb4_unicode_ci`).
   Jika username/password MySQL Anda berbeda, ubah `DB_USERNAME` dan `DB_PASSWORD` di file `.env`.

```bash
# 5. Buat tabel + data contoh
php artisan migrate --seed

# 6. Tautan penyimpanan gambar (wajib agar gambar produk & bukti bayar tampil)
php artisan storage:link

# 7. Build aset front-end
npm run build

# 8. Jalankan
php artisan serve
```

Buka **http://localhost:8000**. Untuk pengembangan dengan hot-reload, jalankan `npm run dev` di terminal kedua.

> Ingin mengulang dari awal? `php artisan migrate:fresh --seed`

## Akun Demo

Password semua akun: **`password`**

| Role | Email |
|---|---|
| Pelanggan (User) | `user@example.com` |
| Admin | `admin@example.com` |
| Super Admin | `superadmin@example.com` |

Seeder juga membuat 5 kategori, 15 produk, 5 jasa pemasangan, 10 pelanggan, 2 admin, 1 super admin, serta data transaksi/ulasan contoh agar dashboard dan laporan langsung terisi.

## Fitur per Role

**User** — registrasi/login, katalog (cari, filter kategori, urutkan harga, pagination), detail produk + ulasan, jasa pemasangan, keranjang, checkout (opsional + jasa pemasangan), pembayaran (unggah bukti), riwayat & status pesanan/pembayaran/pemasangan, jadwal & riwayat pemasangan, ulasan & rating (1–5), profil.

**Admin** — dashboard statistik, lihat/proses pesanan, verifikasi/tolak pembayaran, kelola jadwal pemasangan, CRUD jasa pemasangan, balas/kelola ulasan, lihat data pelanggan pada transaksi, profil.

**Super Admin** — semua fitur Admin + CRUD produk (upload gambar) & kategori, kelola akun user dan admin, laporan penjualan (filter hari/minggu/bulan/tahun, grafik Chart.js, produk terlaris), pengaturan sistem (nama website/usaha, logo, alamat, kontak, email, deskripsi, jam & kapasitas pemasangan, petunjuk pembayaran).

Hak akses dipisahkan dengan middleware `role` pada `/user`, `/admin`, `/super-admin`.

## Alur Status

```
Pesanan   : pending → diterima → diproses → selesai      (atau ditolak)
Pembayaran: (belum) → pending → diverifikasi → berhasil  (atau ditolak → kirim ulang)
Pemasangan: menunggu → dijadwalkan → diproses → selesai  (atau dibatalkan)
```

- Admin memverifikasi pembayaran → pesanan otomatis **diproses**, jadwal pemasangan menjadi **dijadwalkan**.
- Pesanan baru dapat **selesai** setelah pembayaran diverifikasi; saat selesai, pembayaran menjadi **berhasil**.
- Pesanan **ditolak** → stok dikembalikan, booking dibatalkan.
- Stok divalidasi ulang dan dikurangi di dalam transaksi database saat checkout. Harga selalu diambil dari database.

## Kesesuaian dengan Proposal

- Hanya web (tanpa aplikasi mobile native). PWA hanya memungkinkan *install* dari browser.
- **Tidak ada pembayaran VA Bank / transfer bank**. Metode: QRIS, DANA, OVO, GoPay (unggah bukti, diverifikasi manual admin) dan bayar di tempat (tunai).
- Pemasangan **hanya di tempat** (bengkel); tidak ada layanan ke rumah.
- Struktur database mengikuti ERD (11 tabel). Penyesuaian teknis kecil:
  - Tabel `order` dinamai **`orders`** karena `ORDER` adalah kata cadangan SQL.
  - Kolom tambahan: `order_item.harga_satuan`, `services_booking.harga` (riwayat harga tidak berubah bila harga produk/jasa diubah), `reviews.balasan_admin` + `tanggal_balasan` (fitur balas ulasan), `pembayaran.bukti_pembayaran` + `tanggal_bayar` + `catatan`.
  - Tabel tambahan `settings` untuk fitur pengaturan sistem.
- Booking jasa selalu terhubung ke satu `order` (sesuai ERD). Booking jasa saja membuat pesanan tanpa item produk.

## PWA (Install ke HP)

`manifest.webmanifest`, `sw.js`, ikon, dan halaman offline sudah disertakan. Browser hanya menawarkan *Install / Add to Home Screen* bila situs diakses lewat **HTTPS** (atau `localhost`). Untuk demo di HP gunakan hosting ber-HTTPS atau tunnel (mis. Cloudflare Tunnel / ngrok).

## Struktur Singkat

```
app/Http/Controllers/        controller publik & user
app/Http/Controllers/Admin/  controller Admin (+ ManageController basis bersama)
app/Http/Controllers/SuperAdmin/  controller Super Admin (mewarisi logika Admin bila sama)
app/Http/Middleware/RoleMiddleware.php
app/Http/Requests/           validasi (pesan Bahasa Indonesia di lang/id)
app/Models/                  11 model + Setting
app/Services/                OrderService, PaymentService, BookingService, SalesReportService
database/migrations|seeders/
resources/views/             layouts, components, public, user, admin, manage (bersama), super-admin, errors
routes/web.php               seluruh route (named routes)
```

## Desain & Animasi ("Modern Automotive E-Commerce — Clean White Premium")

Satu design system untuk seluruh halaman (publik, user, admin, super admin). Semua warna berasal dari CSS variable di **`resources/css/app.css`** (blok `:root`):

| Variabel | Nilai | Fungsi |
|---|---|---|
| `--primary` / `--text` | `#111111` | teks utama, tombol gelap |
| `--accent` | `#D92D20` | satu-satunya warna aksen (tombol utama, tautan, indikator aktif) |
| `--background` / `--surface` | `#FFFFFF` | latar halaman, kartu, navbar, sidebar |
| `--surface-alt` | `#F7F7F7` | section alternatif, footer |
| `--muted` | `#666666` | teks sekunder |
| `--border` | `#EAEAEA` | garis tipis |

Mengganti `--accent` mengubah seluruh website sekaligus. Font tunggal **Inter** (dibundel lewat npm, tanpa CDN), set ikon tunggal bergaya outline.

Animasi (CSS transition/keyframe + Intersection Observer + Alpine.js, hanya `transform` dan `opacity`; durasi 150–700 ms):
page load fade/slide-up, navbar fade+slide-down lalu solid + bayangan saat scroll, menu mobile slide-down dengan item berurutan, hero (headline → subtitle → CTA → gambar dari kanan, elemen dekoratif melayang halus), scroll reveal + stagger kartu (80 ms), hover kartu produk/jasa, tombol (naik saat hover, mengecil saat ditekan), tombol **Tambah ke Keranjang** via AJAX (berubah menjadi "✓ Ditambahkan", toast kanan bawah, badge keranjang berdenyut), stepper checkout (garis progres bergerak), badge status (status berjalan berdenyut halus), count-up angka dashboard (1 detik), animasi grafik Chart.js (bar tumbuh, garis tergambar bertahap), modal/dropdown/toast, fokus input beraksen, pesan error fade+slide-down, skeleton shimmer pada gambar produk dan grafik, progress bar tipis saat berpindah halaman.

Menghormati `prefers-reduced-motion` (animasi dimatikan/dikurangi) dan animasi dikurangi di layar kecil.

## Testing

```bash
php artisan test
```

Berisi test akses role, registrasi, keranjang/stok, checkout, alur verifikasi pembayaran, penolakan pesanan, dan validasi jadwal pemasangan (memakai SQLite in-memory; butuh ekstensi `pdo_sqlite`).

## Keamanan

CSRF, hashing password (`Hash::make`), autentikasi + otorisasi middleware per role, Form Request validation, escaping output Blade, proteksi mass-assignment (`role` tidak dapat diisi dari input), pembatasan percobaan login, user hanya dapat mengakses pesanan miliknya sendiri.
