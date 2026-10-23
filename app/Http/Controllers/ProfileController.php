<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Profil dipakai bersama oleh User, Admin, dan Super Admin; layout menyesuaikan role.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'layout' => match ($user->role) {
                'super_admin' => 'layouts.super-admin',
                'admin' => 'layouts.admin',
                default => 'layouts.app',
            },
            'aksi' => match ($user->role) {
                'super_admin' => route('superadmin.profile.update'),
                'admin' => route('admin.profile.update'),
                default => route('user.profile.update'),
            },
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->only('nama', 'email', 'alamat'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
