@extends('layouts.super-admin')
@section('title', 'Akun Admin')

@section('content')
@php $baru = old('_form') === 'create'; @endphp
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="relative w-full sm:w-80"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / email..." class="form-input pl-10" aria-label="Cari admin"></form>
    <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'create')"><x-icon name="plus" size="h-4 w-4" /> Tambah Admin</button>
</div>

<div class="card overflow-hidden">
    @if ($admins->isEmpty())
        <x-empty-state icon="shield" title="Belum ada akun admin" text="Tambahkan admin untuk membantu mengelola pesanan." />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Admin</th><th>Alamat</th><th>Dibuat</th><th></th></tr></thead>
            <tbody>@foreach ($admins as $a)
                <tr>
                    <td><div class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold uppercase text-white">{{ mb_substr($a->nama, 0, 1) }}</span><div><p class="font-semibold text-ink-900">{{ $a->nama }}</p><p class="text-xs text-ink-500">{{ $a->email }}</p></div></div></td>
                    <td class="max-w-xs"><p class="line-clamp-2 text-ink-600">{{ $a->alamat ?? '-' }}</p></td>
                    <td class="whitespace-nowrap text-ink-600">{{ tanggal_id($a->created_at) }}</td>
                    <td class="whitespace-nowrap text-right">
                        <button type="button" class="btn btn-outline btn-sm" @click="$dispatch('open-modal', 'admin-{{ $a->id_user }}')"><x-icon name="pencil" size="h-4 w-4" /> Edit</button>
                        <form method="POST" action="{{ route('superadmin.admins.destroy', $a) }}" class="inline" data-confirm="Hapus akun admin {{ $a->nama }}?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading aria-label="Hapus {{ $a->nama }}"><x-icon name="trash" size="h-4 w-4" /></button></form>
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$admins" />

<x-modal name="create" title="Tambah Akun Admin" :show="$baru">
    <form method="POST" action="{{ route('superadmin.admins.store') }}" class="space-y-4" novalidate>
        @csrf <input type="hidden" name="_form" value="create">
        <x-field name="nama" label="Nama" :value="$baru ? old('nama') : ''" :error="$baru" required />
        <x-field name="email" type="email" label="Email" :value="$baru ? old('email') : ''" :error="$baru" required />
        <x-field name="alamat" type="textarea" rows="2" label="Alamat" :value="$baru ? old('alamat') : ''" :error="$baru" />
        <x-field name="password" type="password" label="Password" hint="Minimal 8 karakter." :error="$baru" autocomplete="new-password" required />
        <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'create')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</x-modal>
@foreach ($admins as $a)
    @php $err = old('_form') === 'admin-'.$a->id_user; @endphp
    <x-modal :name="'admin-'.$a->id_user" title="Edit Akun Admin" :show="$err">
        <form method="POST" action="{{ route('superadmin.admins.update', $a) }}" class="space-y-4" novalidate>
            @csrf @method('PUT') <input type="hidden" name="_form" value="admin-{{ $a->id_user }}">
            <x-field name="nama" label="Nama" :value="$err ? old('nama') : $a->nama" :error="$err" required />
            <x-field name="email" type="email" label="Email" :value="$err ? old('email') : $a->email" :error="$err" required />
            <x-field name="alamat" type="textarea" rows="2" label="Alamat" :value="$err ? old('alamat') : $a->alamat" :error="$err" />
            <x-field name="password" type="password" label="Password Baru" hint="Kosongkan jika tidak diubah." :error="$err" autocomplete="new-password" />
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'admin-{{ $a->id_user }}')">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </x-modal>
@endforeach
@endsection
