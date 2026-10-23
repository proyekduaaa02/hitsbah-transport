@extends($layout)
@section('title', 'Profil Saya')

@section('content')
@if ($layout === 'layouts.app')<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">@include('user.partials.subnav')@else<div class="mx-auto max-w-3xl">@endif
    <div class="card p-5 sm:p-6">
        <div class="flex items-center gap-4 border-b border-ink-100 pb-5">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-ink-900 text-xl font-bold uppercase text-white">{{ mb_substr($user->nama, 0, 1) }}</span>
            <div><h1 class="text-xl font-bold text-ink-900">{{ $user->nama }}</h1><p class="text-sm text-ink-500">{{ $user->email }} · {{ $user->roleLabel() }}</p></div>
        </div>
        <form method="POST" action="{{ $aksi }}" class="mt-6 space-y-4" novalidate>
            @csrf @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <x-field name="nama" label="Nama Lengkap" :value="old('nama', $user->nama)" required />
                <x-field name="email" type="email" label="Email" :value="old('email', $user->email)" required />
            </div>
            <x-field name="alamat" type="textarea" label="Alamat" :value="old('alamat', $user->alamat)" rows="3" required />
            <div class="rounded-xl border border-ink-100 bg-ink-50 p-4">
                <p class="text-sm font-semibold text-ink-900">Ubah Password <span class="font-normal text-ink-500">(kosongkan jika tidak diubah)</span></p>
                <div class="mt-3 grid gap-4 sm:grid-cols-3">
                    <x-field name="password_lama" type="password" label="Password Lama" autocomplete="current-password" />
                    <x-field name="password" type="password" label="Password Baru" autocomplete="new-password" />
                    <x-field name="password_confirmation" type="password" label="Konfirmasi" autocomplete="new-password" />
                </div>
            </div>
            <div class="flex justify-end"><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </div>
</div>
@endsection
