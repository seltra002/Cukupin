<x-layouts.app :title="'Profil & Telegram'">
    @php
        $link = auth()->user()->telegramLink;
        if (! $link) {
            $link = auth()->user()->telegramLink()->create([
                'verification_code' => strtoupper(\Illuminate\Support\Str::random(6)),
            ]);
        }
    @endphp

    <div class="bg-cream rounded-2xl border border-line p-6 max-w-lg">
        <h3 class="font-display text-lg text-pine-dark mb-2">Hubungkan Telegram</h3>
        @if ($link->isVerified())
            <p class="text-sm text-sprout font-semibold">✅ Akun Telegram kamu udah terhubung.</p>
        @else
            <p class="text-sm text-ink-soft mb-4">
                1. Buka bot Telegram Cukupin, ketik <span class="font-mono">/start</span><br>
                2. Kirim kode ini ke bot:
            </p>
            <p class="font-mono text-2xl font-bold text-pine-dark tracking-widest bg-black/5 rounded-lg px-4 py-3 inline-block">{{ $link->verification_code }}</p>
        @endif
    </div>
</x-layouts.app>
