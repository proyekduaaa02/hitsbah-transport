import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
document.documentElement.classList.add('js');

const rupiah = (angka) => 'Rp ' + Math.round(Number(angka)).toLocaleString('id-ID');
window.rupiah = rupiah;

/* ==========================================================================
   TOAST (kanan bawah, slide-in + fade, hilang otomatis)
   ========================================================================== */
Alpine.store('toasts', {
    items: [],
    seq: 0,
    push(pesan, tipe = 'success', durasi = 4200) {
        const id = ++this.seq;
        this.items.push({ id, pesan, tipe, leaving: false });
        setTimeout(() => this.remove(id), durasi);
    },
    remove(id) {
        const t = this.items.find((x) => x.id === id);
        if (!t || t.leaving) return;
        t.leaving = true; // animasi keluar, lalu dihapus dari daftar
        setTimeout(() => (this.items = this.items.filter((x) => x.id !== id)), 260);
    },
});
window.toast = (pesan, tipe = 'success') => Alpine.store('toasts').push(pesan, tipe);

/* ==========================================================================
   KOMPONEN ALPINE
   ========================================================================== */
// Navbar: berubah halus saat di-scroll
Alpine.data('navbar', () => ({
    buka: false,
    akun: false,
    scrolled: false,
    init() {
        const cek = () => (this.scrolled = window.scrollY > 8);
        cek();
        window.addEventListener('scroll', cek, { passive: true });
    },
}));

// Pemilih jadwal pemasangan: ambil slot jam dari server
Alpine.data('jadwalPicker', (config) => ({
    url: config.url,
    harga: config.harga || {},
    idServices: config.idServices ? String(config.idServices) : '',
    tanggal: config.tanggal || '',
    jam: config.jam || '',
    slots: [],
    durasi: '',
    memuat: false,
    pesan: '',

    get hargaJasa() {
        return Number(this.harga[this.idServices] || 0);
    },

    rupiah,

    init() {
        if (this.idServices && this.tanggal) {
            this.muatSlot();
        }
    },

    async muatSlot() {
        this.slots = [];
        this.pesan = '';

        if (!this.idServices || !this.tanggal) {
            return;
        }

        this.memuat = true;
        try {
            const params = new URLSearchParams({ id_services: this.idServices, tanggal: this.tanggal });
            const res = await fetch(`${this.url}?${params.toString()}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!res.ok) {
                throw new Error('gagal');
            }

            const data = await res.json();
            this.slots = data.slots;
            this.durasi = data.durasi;

            if (!this.slots.some((s) => s.jam === this.jam && s.tersedia)) {
                this.jam = '';
            }
            if (!this.slots.some((s) => s.tersedia)) {
                this.pesan = 'Tidak ada jadwal tersedia pada tanggal ini. Silakan pilih tanggal lain.';
            }
        } catch (e) {
            this.pesan = 'Gagal memuat jadwal. Periksa koneksi internet Anda lalu coba lagi.';
        } finally {
            this.memuat = false;
        }
    },
}));

Alpine.start();

/* ==========================================================================
   SCROLL REVEAL + STAGGER (Intersection Observer)
   ========================================================================== */
function initReveal() {
    const target = [];

    // Container berstagger: anak ke-n muncul dengan jeda 80ms (maks. 8 langkah agar tidak lambat)
    document.querySelectorAll('[data-stagger]').forEach((box) => {
        const langkah = Number(box.dataset.stagger) || 80;
        Array.from(box.children).forEach((el, i) => {
            el.dataset.reveal = el.dataset.reveal || 'up';
            el.style.setProperty('--d', Math.min(i, 8) * langkah + 'ms');
            target.push(el);
        });
    });

    document.querySelectorAll('[data-reveal]').forEach((el) => {
        if (!target.includes(el)) target.push(el);
    });

    if (reduceMotion || !('IntersectionObserver' in window)) return;

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                io.unobserve(el);
                el.classList.add('is-visible');
                // Lepas class reveal setelah selesai agar transisi hover bawaan komponen tidak terganggu.
                const delay = parseFloat(getComputedStyle(el).getPropertyValue('--d')) || 0;
                setTimeout(() => {
                    el.classList.remove('reveal', 'is-visible');
                    el.style.removeProperty('--d');
                }, 700 + delay + 60);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    target.forEach((el) => {
        el.classList.add('reveal');
        io.observe(el);
    });
}

/* ==========================================================================
   COUNT-UP (angka statistik dashboard)
   ========================================================================== */
function formatHitung(nilai, format) {
    return format === 'rupiah' ? rupiah(nilai) : Math.round(nilai).toLocaleString('id-ID');
}

function initCountUp() {
    const els = document.querySelectorAll('[data-count]');
    if (!els.length) return;

    const jalankan = (el) => {
        const akhir = Number(el.dataset.count) || 0;
        const format = el.dataset.format || 'number';
        if (reduceMotion || akhir === 0) {
            el.textContent = formatHitung(akhir, format);
            return;
        }
        const durasi = 1000;
        const mulai = performance.now();
        const langkah = (sekarang) => {
            const p = Math.min((sekarang - mulai) / durasi, 1);
            const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            el.textContent = formatHitung(akhir * eased, format);
            if (p < 1) requestAnimationFrame(langkah);
        };
        el.textContent = formatHitung(0, format);
        requestAnimationFrame(langkah);
    };

    if (!('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                io.unobserve(e.target);
                jalankan(e.target);
            }
        });
    }, { threshold: 0.4 });
    els.forEach((el) => io.observe(el));
}

/* ==========================================================================
   GRAFIK PENJUALAN (Chart.js): bar tumbuh, garis tergambar bertahap, titik muncul
   ========================================================================== */
window.grafikPenjualan = (canvas, data) => {
    if (typeof canvas === 'string') canvas = document.getElementById(canvas);
    if (!canvas) return;

    const css = getComputedStyle(document.documentElement);
    const ambil = (v, cadangan) => css.getPropertyValue(v).trim() || cadangan;
    const accent = ambil('--accent', '#d92d20');
    const primary = ambil('--primary', '#111111');
    const garis = ambil('--border', '#eaeaea');
    const muted = ambil('--muted', '#666666');

    const wrap = canvas.parentElement;
    wrap.classList.add('skeleton', 'rounded-xl');
    canvas.style.opacity = '0';

    const buat = () => {
        wrap.classList.remove('skeleton');
        canvas.style.transition = 'opacity .4s ease';
        canvas.style.opacity = '1';

        const n = Math.max(data.transaksi.length, 1);
        const jeda = 900 / n;
        const yAwal = (ctx) =>
            ctx.index === 0
                ? ctx.chart.scales.y1.getPixelForValue(0)
                : ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;

        new Chart(canvas, {
            data: {
                labels: data.labels,
                datasets: [
                    {
                        type: 'bar', label: 'Pendapatan (Rp)', data: data.pendapatan,
                        backgroundColor: accent, borderRadius: 6, maxBarThickness: 36, yAxisID: 'y',
                    },
                    {
                        type: 'line', label: 'Jumlah transaksi', data: data.transaksi,
                        borderColor: primary, backgroundColor: primary, borderWidth: 2, tension: 0.35,
                        pointRadius: 3, pointHoverRadius: 5, yAxisID: 'y1',
                        animations: reduceMotion ? false : {
                            x: { type: 'number', easing: 'linear', duration: jeda, from: NaN,
                                delay(ctx) { if (ctx.type !== 'data' || ctx.xStarted) return 0; ctx.xStarted = true; return ctx.index * jeda; } },
                            y: { type: 'number', easing: 'linear', duration: jeda, from: yAwal,
                                delay(ctx) { if (ctx.type !== 'data' || ctx.yStarted) return 0; ctx.yStarted = true; return ctx.index * jeda; } },
                        },
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: reduceMotion ? false : { duration: 800, easing: 'easeOutQuart' },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, color: muted } },
                    tooltip: {
                        backgroundColor: primary, padding: 10, cornerRadius: 8,
                        callbacks: {
                            label: (c) => (c.dataset.yAxisID === 'y' ? ' ' + rupiah(c.parsed.y) : ' ' + c.parsed.y + ' transaksi'),
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: muted } },
                    y: { beginAtZero: true, grid: { color: garis }, border: { display: false }, ticks: { color: muted, callback: (v) => (v >= 1000000 ? v / 1000000 + ' jt' : v.toLocaleString('id-ID')) } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, border: { display: false }, ticks: { color: muted, precision: 0 } },
                },
            },
        });
    };

    // Gambar grafik baru saat terlihat di layar agar animasinya tidak terlewat.
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            if (entries.some((e) => e.isIntersecting)) {
                io.disconnect();
                buat();
            }
        }, { threshold: 0.25 });
        io.observe(canvas);
    } else {
        buat();
    }
};

/* ==========================================================================
   GAMBAR PRODUK: tampilkan setelah dimuat (skeleton shimmer di belakangnya)
   ========================================================================== */
function initGambar() {
    document.querySelectorAll('.img-zoom img').forEach((img) => {
        const selesai = () => {
            img.classList.add('is-loaded');
            img.parentElement.classList.remove('skeleton');
        };
        if (img.complete) selesai();
        else {
            img.addEventListener('load', selesai, { once: true });
            img.addEventListener('error', selesai, { once: true });
        }
    });
}

/* ==========================================================================
   KERANJANG (AJAX): tombol berubah jadi "✓ Ditambahkan", toast, badge berdenyut
   ========================================================================== */
function updateBadgeKeranjang(jumlah) {
    document.querySelectorAll('[data-cart-badge]').forEach((el) => {
        el.textContent = jumlah;
        el.classList.toggle('hidden', !(jumlah > 0));
        el.classList.remove('pulse-once');
        void el.offsetWidth; // restart animasi
        el.classList.add('pulse-once');
    });
}

async function tambahKeKeranjang(form) {
    const tombol = form.querySelector('[data-cart-btn]');
    if (tombol && tombol.dataset.state === 'loading') return;
    if (tombol) tombol.dataset.state = 'loading';

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (res.redirected && /\/login/.test(res.url)) {
            window.location.href = res.url;
            return;
        }
        if (res.status === 419) {
            window.location.reload();
            return;
        }

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            window.toast(data.message || 'Produk gagal ditambahkan ke keranjang.', 'error');
            if (tombol) delete tombol.dataset.state;
            return;
        }

        window.toast(data.message || 'Produk berhasil ditambahkan ke keranjang.', 'success');
        updateBadgeKeranjang(data.cart_count);

        if (tombol) {
            tombol.dataset.state = 'added';
            setTimeout(() => delete tombol.dataset.state, 1800);
        }
    } catch (e) {
        window.toast('Koneksi bermasalah. Silakan coba lagi.', 'error');
        if (tombol) delete tombol.dataset.state;
    }
}

/* ==========================================================================
   PROGRESS BAR NAVIGASI (pengganti spinner besar)
   ========================================================================== */
const progress = (() => {
    let el, timer;
    const pastikan = () => {
        if (!el) {
            el = document.createElement('div');
            el.id = 'nav-progress';
            document.body.appendChild(el);
        }
        return el;
    };
    return {
        mulai() {
            const bar = pastikan();
            clearInterval(timer);
            bar.style.opacity = '1';
            bar.style.width = '35%';
            let w = 35;
            timer = setInterval(() => { w += (92 - w) * 0.12; bar.style.width = w + '%'; }, 300);
        },
        reset() {
            clearInterval(timer);
            if (el) { el.style.opacity = '0'; el.style.width = '0'; }
        },
    };
})();

document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href]');
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
    if (a.target === '_blank' || a.hasAttribute('download')) return;
    const url = new URL(a.href, location.href);
    if (url.origin !== location.origin) return;
    if (url.pathname === location.pathname && url.search === location.search) return; // hash / halaman sama
    progress.mulai();
});

/* ==========================================================================
   FORM: konfirmasi (modal) + status loading pada tombol submit
   ========================================================================== */
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    if (form.matches('[data-cart-form]')) {
        event.preventDefault();
        tambahKeKeranjang(form);
        return;
    }

    const pesan = form.dataset.confirm;
    if (pesan && !form.dataset.confirmed) {
        event.preventDefault();
        window.dispatchEvent(new CustomEvent('konfirmasi', { detail: { form, pesan } }));
        return;
    }

    progress.mulai();

    setTimeout(() => {
        form.querySelectorAll('button[type="submit"]:not([data-no-loading])').forEach((btn) => {
            btn.disabled = true;
            btn.classList.add('opacity-70');
            if (!btn.querySelector('.spinner')) {
                btn.insertAdjacentHTML(
                    'afterbegin',
                    '<svg class="spinner h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>'
                );
            }
        });
    }, 0);
});

// Kembali dari cache browser: aktifkan lagi tombol yang sempat dinonaktifkan.
window.addEventListener('pageshow', (e) => {
    progress.reset();
    if (e.persisted) {
        document.querySelectorAll('button[type="submit"]:disabled').forEach((b) => {
            b.disabled = false;
            b.classList.remove('opacity-70');
            b.querySelector('.spinner')?.remove();
        });
    }
});

/* ==========================================================================
   INISIALISASI
   ========================================================================== */
const mulai = () => {
    initGambar();
    initReveal();
    initCountUp();
};
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mulai);
else mulai();

/* Service worker (PWA) */
if ('serviceWorker' in navigator && window.location.protocol !== 'file:') {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
