<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Budget meter card --}}
        <div class="lg:col-span-2 bg-cream rounded-2xl border border-line p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-lg text-pine-dark">Pengeluaran Bulan Ini</h3>
                <span class="font-mono text-xs text-ink-soft">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="flex items-end gap-2 mb-3">
                <span class="font-mono text-2xl font-semibold text-pine-dark">Rp{{ number_format($spentThisMonth, 0, ',', '.') }}</span>
                <span class="text-sm text-ink-soft mb-1">/ Rp{{ number_format($budget, 0, ',', '.') }} budget</span>
            </div>
            <div class="h-2.5 rounded-full bg-black/10 overflow-hidden">
                <div class="h-full rounded-full {{ $budgetPercent >= 100 ? 'bg-chili' : ($budgetPercent >= 80 ? 'bg-kunyit' : 'bg-sprout') }}"
                     style="width: {{ $budgetPercent }}%"></div>
            </div>
            <p class="font-mono text-xs text-ink-soft mt-2">{{ $budgetPercent }}% dari budget bulanan terpakai</p>
        </div>

        {{-- Total saldo dompet --}}
        <div class="bg-pine-dark text-cream rounded-2xl p-6">
            <h3 class="font-display text-lg mb-2">Saldo Gabungan</h3>
            <p class="font-mono text-2xl font-semibold">Rp{{ number_format($totalBalance, 0, ',', '.') }}</p>
            <ul class="mt-4 space-y-1.5 text-sm">
                @foreach ($wallets as $wallet)
                    <li class="flex justify-between text-cream/80">
                        <span>{{ $wallet->name }}</span>
                        <span class="font-mono">Rp{{ number_format($wallet->current_balance, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Nota: stok menipis/habis --}}
        <div class="bg-cream rounded-2xl border border-line overflow-hidden">
            <div class="h-2.5" style="background-image:radial-gradient(circle 3px, #163730 3px, transparent 3.5px);background-size:14px 14px;"></div>
            <div class="p-6">
                <h3 class="font-display text-base text-pine-dark mb-4">Stok Perlu Diisi Ulang</h3>
                @forelse ($lowStockItems as $item)
                    <div class="flex justify-between items-baseline text-sm py-2 border-b border-dashed border-line last:border-0">
                        <span class="font-medium">{{ $item->name }}</span>
                        <span class="font-mono text-xs {{ $item->stock_status === 'habis' ? 'text-chili' : 'text-kunyit-dark' }}">
                            {{ $item->stock_status === 'habis' ? 'habis' : 'menipis' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft">Aman semua, belum ada yang menipis 👌</p>
                @endforelse
            </div>
        </div>

        {{-- Utang & Piutang --}}
        <div class="bg-cream rounded-2xl border border-line p-6">
            <h3 class="font-display text-base text-pine-dark mb-4">Utang & Piutang</h3>
            <div class="flex justify-between text-sm py-2 border-b border-dashed border-line">
                <span>Utang belum lunas</span>
                <span class="font-mono text-chili">Rp{{ number_format($unpaidDebtsTotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm py-2">
                <span>Piutang belum lunas</span>
                <span class="font-mono text-sprout">Rp{{ number_format($unpaidReceivablesTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Target tabungan --}}
        <div class="bg-cream rounded-2xl border border-line p-6">
            <h3 class="font-display text-base text-pine-dark mb-4">Target Tabungan Aktif</h3>
            @forelse ($activeSavings as $goal)
                <div class="mb-3 last:mb-0">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium">{{ $goal->name }}</span>
                        <span class="font-mono text-xs text-ink-soft">{{ $goal->progress_percent }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-black/10 overflow-hidden">
                        <div class="h-full rounded-full bg-sprout" style="width: {{ $goal->progress_percent }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft">Belum ada target tabungan aktif.</p>
            @endforelse
        </div>
    </div>
</div>
