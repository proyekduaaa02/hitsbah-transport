{{-- Ilustrasi mobil (SVG inline, memakai variabel warna design system) --}}
<svg viewBox="0 0 660 300" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi mobil" {{ $attributes }}>
    <ellipse cx="330" cy="283" rx="292" ry="9" fill="var(--primary)" opacity=".08"/>
    <path d="M44 228 L44 204 C44 190 56 181 76 177 L148 166 C176 126 218 100 270 96 L398 96 C444 96 484 126 510 164 L568 172 C598 177 616 190 616 208 L616 230 C616 236 611 240 605 240 L55 240 C48 240 44 235 44 228 Z" fill="var(--primary)"/>
    <path d="M176 164 C196 132 228 112 268 110 L322 110 L322 164 Z" fill="var(--surface-alt)"/>
    <path d="M338 110 L396 110 C428 110 458 132 480 164 L338 164 Z" fill="var(--surface-alt)"/>
    <path d="M70 190 L602 196" stroke="#fff" stroke-opacity=".16" stroke-width="2"/>
    <rect x="200" y="208" width="250" height="5" rx="2.5" fill="var(--accent)"/>
    <rect x="292" y="176" width="28" height="5" rx="2.5" fill="#fff" opacity=".45"/>
    <path d="M594 186 L612 190 C615 191 616 194 616 197 L616 203 L590 200 Z" fill="var(--accent)"/>
    <rect x="44" y="198" width="12" height="17" rx="3" fill="var(--accent)"/>
    <circle cx="170" cy="240" r="50" fill="var(--background)"/><circle cx="480" cy="240" r="50" fill="var(--background)"/>
    @foreach ([170, 480] as $cx)
        <g>
            <circle cx="{{ $cx }}" cy="240" r="43" fill="#1c1c1c"/>
            <circle cx="{{ $cx }}" cy="240" r="27" fill="#eaeaea"/>
            <g stroke="#111" stroke-width="3" stroke-linecap="round">
                @foreach ([0, 72, 144, 216, 288] as $deg)
                    <line x1="{{ $cx }}" y1="240" x2="{{ round($cx + 24 * sin(deg2rad($deg)), 1) }}" y2="{{ round(240 - 24 * cos(deg2rad($deg)), 1) }}"/>
                @endforeach
            </g>
            <circle cx="{{ $cx }}" cy="240" r="8" fill="var(--primary)"/>
            <circle cx="{{ $cx }}" cy="240" r="3" fill="var(--accent)"/>
        </g>
    @endforeach
</svg>
