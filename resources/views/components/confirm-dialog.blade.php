<div x-data="{ buka: false, pesan: '', form: null }"
     x-on:konfirmasi.window="buka = true; pesan = $event.detail.pesan; form = $event.detail.form"
     x-on:keydown.escape.window="buka = false"
     x-show="buka" x-cloak
     x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[95] flex items-center justify-center p-4" role="alertdialog" aria-modal="true" aria-label="Konfirmasi">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm" @click="buka = false"></div>
    <div x-show="buka"
         x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
         class="relative w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-accent-soft text-accent"><x-icon name="warning" size="h-6 w-6" /></div>
        <h2 class="mt-4 text-base font-semibold text-fg">Konfirmasi</h2>
        <p class="mt-1 text-sm text-muted" x-text="pesan"></p>
        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" class="btn btn-outline" @click="buka = false">Batal</button>
            <button type="button" class="btn btn-primary" @click="form.dataset.confirmed = '1'; buka = false; form.requestSubmit()">Ya, lanjutkan</button>
        </div>
    </div>
</div>
