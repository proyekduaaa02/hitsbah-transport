<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Basis test: helper pembuat data dan Vite dimatikan (tidak perlu npm run build saat testing). */
abstract class HitsbahTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Setting::lupakanCache();
    }

    protected function buatUser(string $role = 'user'): User
    {
        $user = new User();
        $user->forceFill([
            'nama' => 'Tester '.$role,
            'email' => $role.'-'.uniqid().'@test.local',
            'password' => Hash::make('password'),
            'alamat' => 'Jl. Contoh No. 1, Indramayu',
            'role' => $role,
        ])->save();

        return $user;
    }

    protected function buatProduk(int $stok = 10, int $harga = 100000): Produk
    {
        $kategori = Kategori::firstOrCreate(['nama' => 'Interior'], ['deskripsi' => 'Kategori uji']);

        return Produk::create([
            'id_kategori' => $kategori->id_kategori,
            'nama' => 'Produk Uji '.uniqid(),
            'deskripsi' => 'Deskripsi uji',
            'harga' => $harga,
            'stok' => $stok,
        ]);
    }
}
