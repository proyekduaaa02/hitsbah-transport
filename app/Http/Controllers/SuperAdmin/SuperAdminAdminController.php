<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Http\Requests\AccountRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/** Mengelola akun admin (role: admin). */
class SuperAdminAdminController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(Request $request): View
    {
        return $this->render('super-admin.admins.index', [
            'admins' => User::role(User::ROLE_ADMIN)
                ->search($request->query('q'))
                ->latest('id_user')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function store(AccountRequest $request): RedirectResponse
    {
        $admin = new User($request->safe()->only(['nama', 'email', 'alamat']));
        $admin->password = Hash::make($request->input('password'));
        $admin->role = User::ROLE_ADMIN;
        $admin->save();

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function update(AccountRequest $request, User $admin): RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        $admin->fill($request->safe()->only(['nama', 'email', 'alamat']));
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }
        $admin->save();

        return back()->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless($admin->isAdmin(), 404);

        $admin->delete();

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }
}
