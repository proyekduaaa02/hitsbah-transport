<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SystemSettingController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    private const KUNCI = [
        'site_name', 'nama_usaha', 'alamat', 'kontak', 'email', 'deskripsi',
        'jam_buka', 'jam_tutup', 'kapasitas_pemasangan', 'info_pembayaran',
    ];

    public function edit(): View
    {
        return $this->render('super-admin.settings', ['setting' => Setting::semua()]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        foreach (self::KUNCI as $kunci) {
            Setting::simpan($kunci, $request->input($kunci));
        }

        if ($request->hasFile('logo')) {
            if ($lama = Setting::ambil('logo')) {
                Storage::disk('public')->delete($lama);
            }
            Setting::simpan('logo', $request->file('logo')->store('settings', 'public'));
        }

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
