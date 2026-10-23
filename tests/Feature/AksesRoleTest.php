<?php

namespace Tests\Feature;

class AksesRoleTest extends HitsbahTestCase
{
    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get('/user/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/super-admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_user_tidak_dapat_membuka_halaman_admin_maupun_super_admin(): void
    {
        $user = $this->buatUser('user');

        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/super-admin/dashboard')->assertForbidden();
    }

    public function test_admin_tidak_dapat_membuka_fitur_super_admin(): void
    {
        $admin = $this->buatUser('admin');

        $this->actingAs($admin)->get('/super-admin/products')->assertForbidden();
        $this->actingAs($admin)->get('/super-admin/reports')->assertForbidden();
        $this->actingAs($admin)->get('/super-admin/settings')->assertForbidden();
    }

    public function test_admin_dapat_membuka_dashboard_admin(): void
    {
        $this->actingAs($this->buatUser('admin'))->get('/admin/dashboard')->assertOk();
    }

    public function test_super_admin_dapat_membuka_dashboard_dan_laporan(): void
    {
        $sa = $this->buatUser('super_admin');

        $this->actingAs($sa)->get('/super-admin/dashboard')->assertOk();
        $this->actingAs($sa)->get('/super-admin/reports')->assertOk();
    }

    public function test_registrasi_selalu_membuat_role_user_dan_password_di_hash(): void
    {
        $this->post('/register', [
            'nama' => 'Pelanggan Baru',
            'email' => 'baru@test.local',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'alamat' => 'Indramayu',
            'role' => 'super_admin', // upaya menyuntikkan role harus diabaikan
        ])->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'baru@test.local', 'role' => 'user']);
        $this->assertNotSame('rahasia123', \App\Models\User::where('email', 'baru@test.local')->value('password'));
    }

    public function test_halaman_public_dapat_dibuka(): void
    {
        $this->get('/')->assertOk();
        $this->get('/produk')->assertOk();
        $this->get('/jasa')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}
