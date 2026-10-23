<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Http\Requests\AccountRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/** Mengelola akun pelanggan (role: user). */
class SuperAdminUserController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(Request $request): View
    {
        return $this->render('super-admin.users.index', [
            'users' => User::role(User::ROLE_USER)
                ->withCount('orders')
                ->search($request->query('q'))
                ->latest('id_user')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function update(AccountRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        $user->fill($request->safe()->only(['nama', 'email', 'alamat']));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        return back()->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->isUser(), 404);

        if ($user->orders()->exists() || $user->serviceBookings()->exists()) {
            return back()->with('error', 'Akun tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $user->delete();

        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
