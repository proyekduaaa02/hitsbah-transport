@php
    $pesan = [];
    foreach (['success', 'error', 'warning', 'info'] as $tipe) {
        if (session($tipe)) {
            $pesan[] = ['tipe' => $tipe, 'teks' => session($tipe)];
        }
    }
    if ($errors->any() && empty($pesan)) {
        $pesan[] = ['tipe' => 'error', 'teks' => 'Periksa kembali isian formulir Anda.'];
    }
@endphp
{{-- Wadah toast global (kanan bawah). Dipakai juga oleh JavaScript: window.toast('pesan', 'success|error') --}}
<div x-data x-init="@foreach ($pesan as $p) $store.toasts.push(@js($p['teks']), @js($p['tipe'])); @endforeach"
     class="pointer-events-none fixed bottom-4 right-4 z-[100] flex w-[calc(100%-2rem)] flex-col items-end gap-2 sm:w-96" aria-live="polite" aria-atomic="false">
    <template x-for="t in $store.toasts.items" :key="t.id">
        <div role="status" :class="t.leaving ? 'toast-out' : 'toast-in'"
             class="pointer-events-auto flex w-full items-start gap-3 rounded-2xl border border-line bg-white p-4 shadow-[0_18px_40px_-18px_rgba(17,17,17,0.35)]">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-white" :class="t.tipe === 'success' ? 'bg-primary' : (t.tipe === 'error' ? 'bg-accent' : 'bg-primary')">
                <svg x-show="t.tipe === 'success'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                <svg x-show="t.tipe !== 'success'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5m0 3.5h.01"/></svg>
            </span>
            <p class="flex-1 text-sm text-fg" x-text="t.pesan"></p>
            <button type="button" @click="$store.toasts.remove(t.id)" class="text-muted transition hover:text-fg" aria-label="Tutup notifikasi"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </template>
</div>
