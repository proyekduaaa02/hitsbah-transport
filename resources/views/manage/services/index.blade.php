@extends($layout)
@section('title', 'Jasa Pemasangan')

@section('content')
@php $formBaru = old('_form') === 'create'; @endphp
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="relative w-full sm:w-80"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari jasa..." class="form-input pl-10" aria-label="Cari jasa"></form>
    <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'create')"><x-icon name="plus" size="h-4 w-4" /> Tambah Jasa</button>
</div>

<div class="card overflow-hidden">
    @if ($services->isEmpty())
        <x-empty-state icon="wrench" title="Belum ada jasa pemasangan" text="Tambahkan jasa agar pelanggan dapat melakukan booking." />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Nama Jasa</th><th>Deskripsi</th><th class="text-right">Harga</th><th>Durasi</th><th class="text-center">Booking</th><th></th></tr></thead>
            <tbody>@foreach ($services as $s)
                <tr>
                    <td class="font-semibold text-ink-900">{{ $s->nama }}</td>
                    <td class="max-w-xs"><p class="line-clamp-2 text-ink-600">{{ $s->deskripsi }}</p></td>
                    <td class="whitespace-nowrap text-right font-semibold">{{ rupiah($s->harga) }}</td>
                    <td class="whitespace-nowrap">{{ $s->durasi_label }}</td>
                    <td class="text-center">{{ $s->bookings_count }}</td>
                    <td class="whitespace-nowrap text-right">
                        <button type="button" class="btn btn-outline btn-sm" @click="$dispatch('open-modal', 'edit-{{ $s->id_services }}')"><x-icon name="pencil" size="h-4 w-4" /> Edit</button>
                        <form method="POST" action="{{ route($prefix.'.services.destroy', $s) }}" class="inline" data-confirm="Hapus jasa {{ $s->nama }}?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading aria-label="Hapus {{ $s->nama }}"><x-icon name="trash" size="h-4 w-4" /></button></form>
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$services" />

{{-- Modal tambah --}}
<x-modal name="create" title="Tambah Jasa Pemasangan" :show="$formBaru">
    <form method="POST" action="{{ route($prefix.'.services.store') }}" class="space-y-4" novalidate>
        @csrf <input type="hidden" name="_form" value="create">
        <x-field name="nama" label="Nama Jasa" :value="$formBaru ? old('nama') : ''" :error="$formBaru" required />
        <x-field name="deskripsi" type="textarea" label="Deskripsi" :value="$formBaru ? old('deskripsi') : ''" :error="$formBaru" />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="harga" type="number" min="0" step="1000" label="Harga (Rp)" :value="$formBaru ? old('harga') : ''" :error="$formBaru" required />
            <x-field name="durasi_pemasangan" type="number" min="15" step="15" label="Durasi (menit)" :value="$formBaru ? old('durasi_pemasangan') : ''" :error="$formBaru" required />
        </div>
        <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'create')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
    </form>
</x-modal>

{{-- Modal edit per baris --}}
@foreach ($services as $s)
    @php $err = old('_form') === 'edit-'.$s->id_services; @endphp
    <x-modal :name="'edit-'.$s->id_services" title="Edit Jasa Pemasangan" :show="$err">
        <form method="POST" action="{{ route($prefix.'.services.update', $s) }}" class="space-y-4" novalidate>
            @csrf @method('PUT') <input type="hidden" name="_form" value="edit-{{ $s->id_services }}">
            <x-field name="nama" label="Nama Jasa" :value="$err ? old('nama') : $s->nama" :error="$err" required />
            <x-field name="deskripsi" type="textarea" label="Deskripsi" :value="$err ? old('deskripsi') : $s->deskripsi" :error="$err" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-field name="harga" type="number" min="0" step="1000" label="Harga (Rp)" :value="$err ? old('harga') : (int) $s->harga" :error="$err" required />
                <x-field name="durasi_pemasangan" type="number" min="15" step="15" label="Durasi (menit)" :value="$err ? old('durasi_pemasangan') : $s->durasi_pemasangan" :error="$err" required />
            </div>
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'edit-{{ $s->id_services }}')">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </x-modal>
@endforeach
@endsection
