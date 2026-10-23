<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.hitsbah');
        Paginator::defaultSimpleView('vendor.pagination.hitsbah');

        // Data pengaturan (nama usaha, kontak, dst.) tersedia di semua view sebagai $site.
        View::composer('*', function ($view) {
            static $site = null;

            if ($site === null) {
                $site = [];
                try {
                    $site = Setting::semua();
                } catch (\Throwable) {
                    // tabel settings belum ada (sebelum php artisan migrate)
                }
            }

            $view->with('site', $site + [
                'site_name' => 'HITSBAH Transport',
                'nama_usaha' => 'HITSBAH Transport',
                'alamat' => 'Desa Pengauban, Kecamatan Lelea, Kabupaten Indramayu',
                'kontak' => '0812-0000-0000',
                'email' => 'info@hitsbahtransport.test',
                'deskripsi' => 'Penyedia aksesoris mobil dan jasa pemasangan profesional.',
            ]);
        });

        // Jumlah item keranjang untuk badge di navbar.
        View::composer('components.navbar', function ($view) {
            $jumlah = 0;
            $user = Auth::user();
            if ($user && $user->isUser()) {
                $jumlah = (int) ($user->cart?->items()->sum('jumlah') ?? 0);
            }
            $view->with('jumlahKeranjang', $jumlah);
        });
    }
}
