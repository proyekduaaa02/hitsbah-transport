@extends('layouts.super-admin')
@section('title', 'Akun User')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="relative w-full sm:w-80"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / email..." class="form-input pl-10" aria-label="Cari user"></form>
    <p class="text-sm text-ink-500">{{ $users->total() }} pelanggan terdaftar</p>
</div>

<div class="card overflow-hidden">
    @if ($users->isEmpty())
        <x-empty-state icon="users" title="User tidak ditemukan" />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Pelanggan</th><th>Alamat</th><th class="text-center">Pesanan</th><th>Terdaftar</th><th></th></tr></thead>
            <tbody>@foreach ($users as $u)
                <tr>
                    <td><div class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-ink-900 text-sm font-bold uppercase text-white">{{ mb_substr($u->nama, 0, 1) }}</span><div><p class="font-semibold text-ink-900">{{ $u->nama }}</p><p class="text-xs text-ink-500">{{ $u->email }}</p></div></div></td>
                    <td class="max-w-xs"><p class="line-clamp-2 text-ink-600">{{ $u->alamat ?? '-' }}</p></td>
                    <td class="text-center">{{ $u->orders_count }}</td>
                    <td class="whitespace-nowrap text-ink-600">{{ tanggal_id($u->created_at) }}</td>
                    <td class="whitespace-nowrap text-right">
                        <button type="button" class="btn btn-outline btn-sm" @click="$dispatch('open-modal', 'user-{{ $u->id_user }}')"><x-icon name="pencil" size="h-4 w-4" /> Edit</button>
                        <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" class="inline" data-confirm="Hapus akun {{ $u->nama }}?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading aria-label="Hapus {{ $u->nama }}"><x-icon name="trash" size="h-4 w-4" /></button></form>
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$users" />

@foreach ($users as $u)
    @php $err = old('_form') === 'user-'.$u->id_user; @endphp
    <x-modal :name="'user-'.$u->id_user" title="Edit Akun Pelanggan" :show="$err">
        <form method="POST" action="{{ route('superadmin.users.update', $u) }}" class="space-y-4" novalidate>
            @csrf @method('PUT') <input type="hidden" name="_form" value="user-{{ $u->id_user }}">
            <x-field name="nama" label="Nama" :value="$err ? old('nama') : $u->nama" :error="$err" required />
            <x-field name="email" type="email" label="Email" :value="$err ? old('email') : $u->email" :error="$err" required />
            <x-field name="alamat" type="textarea" rows="2" label="Alamat" :value="$err ? old('alamat') : $u->alamat" :error="$err" />
            <x-field name="password" type="password" label="Password Baru" hint="Kosongkan jika tidak diubah (minimal 8 karakter)." :error="$err" autocomplete="new-password" />
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'user-{{ $u->id_user }}')">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </x-modal>
@endforeach
@endsection
