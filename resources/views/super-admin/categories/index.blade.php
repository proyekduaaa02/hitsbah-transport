@extends('layouts.super-admin')
@section('title', 'Kategori Produk')

@section('content')
@php $baru = old('_form') === 'create'; @endphp
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="relative w-full sm:w-80"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari kategori..." class="form-input pl-10" aria-label="Cari kategori"></form>
    <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'create')"><x-icon name="plus" size="h-4 w-4" /> Tambah Kategori</button>
</div>

<div class="card overflow-hidden">
    @if ($categories->isEmpty())
        <x-empty-state icon="tag" title="Belum ada kategori" text="Tambahkan kategori untuk mengelompokkan produk." />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Nama</th><th>Deskripsi</th><th class="text-center">Produk</th><th></th></tr></thead>
            <tbody>@foreach ($categories as $c)
                <tr>
                    <td class="font-semibold text-ink-900">{{ $c->nama }}</td>
                    <td class="max-w-md"><p class="line-clamp-2 text-ink-600">{{ $c->deskripsi }}</p></td>
                    <td class="text-center">{{ $c->produk_count }}</td>
                    <td class="whitespace-nowrap text-right">
                        <button type="button" class="btn btn-outline btn-sm" @click="$dispatch('open-modal', 'edit-{{ $c->id_kategori }}')"><x-icon name="pencil" size="h-4 w-4" /> Edit</button>
                        <form method="POST" action="{{ route('superadmin.categories.destroy', $c) }}" class="inline" data-confirm="Hapus kategori {{ $c->nama }}?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading aria-label="Hapus {{ $c->nama }}"><x-icon name="trash" size="h-4 w-4" /></button></form>
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$categories" />

<x-modal name="create" title="Tambah Kategori" :show="$baru">
    <form method="POST" action="{{ route('superadmin.categories.store') }}" class="space-y-4" novalidate>
        @csrf <input type="hidden" name="_form" value="create">
        <x-field name="nama" label="Nama Kategori" :value="$baru ? old('nama') : ''" :error="$baru" required />
        <x-field name="deskripsi" type="textarea" label="Deskripsi" :value="$baru ? old('deskripsi') : ''" :error="$baru" />
        <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'create')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</x-modal>
@foreach ($categories as $c)
    @php $err = old('_form') === 'edit-'.$c->id_kategori; @endphp
    <x-modal :name="'edit-'.$c->id_kategori" title="Edit Kategori" :show="$err">
        <form method="POST" action="{{ route('superadmin.categories.update', $c) }}" class="space-y-4" novalidate>
            @csrf @method('PUT') <input type="hidden" name="_form" value="edit-{{ $c->id_kategori }}">
            <x-field name="nama" label="Nama Kategori" :value="$err ? old('nama') : $c->nama" :error="$err" required />
            <x-field name="deskripsi" type="textarea" label="Deskripsi" :value="$err ? old('deskripsi') : $c->deskripsi" :error="$err" />
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'edit-{{ $c->id_kategori }}')">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </x-modal>
@endforeach
@endsection
