{{-- Dipakai di dalam elemen ber-x-data="jadwalPicker(...)". Variabel: $jasa, $opsional --}}
<div class="space-y-4">
    <div>
        <label for="id_services" class="form-label">Jasa Pemasangan @if ($opsional)<span class="font-normal text-ink-500">(opsional)</span>@endif</label>
        <select id="id_services" name="id_services" x-model="idServices" @change="jam = ''; muatSlot()" class="form-input {{ $errors->has('id_services') ? 'border-accent' : '' }}">
            <option value="">{{ $opsional ? 'Tanpa jasa pemasangan' : 'Pilih jasa pemasangan' }}</option>
            @foreach ($jasa as $j)
                <option value="{{ $j->id_services }}">{{ $j->nama }} — {{ rupiah($j->harga) }} ({{ $j->durasi_label }})</option>
            @endforeach
        </select>
        @error('id_services')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div x-show="idServices" x-cloak class="space-y-4">
        <div class="flex items-start gap-2 rounded-lg bg-ink-50 p-3 text-xs text-ink-600">
            <x-icon name="map-pin" size="h-4 w-4" class="mt-0.5 text-brand-600" />
            <span>Pemasangan dilakukan <strong>di tempat (bengkel {{ $site['site_name'] }})</strong>: {{ $site['alamat'] }}. Durasi ± <span x-text="durasi || '-'"></span>.</span>
        </div>

        <div>
            <label for="tanggal" class="form-label">Tanggal Pemasangan</label>
            <input id="tanggal" type="date" name="tanggal" x-model="tanggal" @change="jam = ''; muatSlot()" min="{{ now()->toDateString() }}" max="{{ now()->addDays(60)->toDateString() }}" class="form-input {{ $errors->has('tanggal') ? 'border-accent' : '' }}">
            @error('tanggal')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div x-show="tanggal">
            <span class="form-label">Jam Tersedia</span>
            <div x-show="memuat" class="flex items-center gap-2 text-sm text-ink-500"><svg class="spinner h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg> Memuat jadwal...</div>
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4" x-show="!memuat">
                <template x-for="s in slots" :key="s.jam">
                    <button type="button" :disabled="!s.tersedia" @click="jam = s.jam"
                            class="rounded-lg border px-3 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:bg-ink-50 disabled:text-ink-300 disabled:line-through"
                            :class="jam === s.jam ? 'border-brand-600 bg-brand-600 text-white shadow' : 'border-ink-200 bg-white text-ink-800 hover:border-brand-500'"
                            x-text="s.jam"></button>
                </template>
            </div>
            <p x-show="pesan" x-text="pesan" class="mt-2 text-sm text-accent"></p>
            @error('jam')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <input type="hidden" name="jam" :value="jam">
    </div>
</div>
