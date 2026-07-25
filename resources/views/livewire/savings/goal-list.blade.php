<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="font-display text-xl text-pine-dark">Target Tabungan</h2>
        @if (Auth::user()->canInput())
            <button wire:click="openForm" class="bg-kunyit text-pine-dark font-semibold text-sm px-4 py-2 rounded-lg">+ Target Baru</button>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($goals as $goal)
            <div class="bg-cream rounded-2xl border border-line p-5">
                <div class="flex justify-between items-start">
                    <h3 class="font-display font-semibold text-pine-dark">{{ $goal->name }}</h3>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full {{ $goal->status === 'achieved' ? 'bg-sprout/15 text-sprout' : 'bg-black/10 text-ink-soft' }}">{{ $goal->status }}</span>
                </div>
                <p class="font-mono text-sm text-ink-soft mt-1">Rp{{ number_format($goal->collected_amount, 0, ',', '.') }} / Rp{{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                <div class="h-2 rounded-full bg-black/10 overflow-hidden mt-2">
                    <div class="h-full rounded-full bg-sprout" style="width: {{ $goal->progress_percent }}%"></div>
                </div>
                @if ($goal->deadline)
                    <p class="text-xs text-ink-soft mt-2 font-mono">Tenggat: {{ $goal->deadline->format('d M Y') }}</p>
                @endif
                @if (Auth::user()->canInput() && $goal->status === 'active')
                    <button wire:click="openContributeForm({{ $goal->id }})" class="mt-4 w-full text-sm px-3 py-2 rounded-lg border border-line font-medium">+ Setor</button>
                @endif
            </div>
        @empty
            <p class="text-sm text-ink-soft">Belum ada target tabungan.</p>
        @endforelse
    </div>

    @if ($showForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-display text-lg text-pine-dark mb-4">Target Tabungan Baru</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Nama Target</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('name') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Nominal Tujuan</label>
                    <input type="number" step="0.01" wire:model="target_amount" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('target_amount') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Tenggat (opsional)</label>
                    <input type="date" wire:model="deadline" class="w-full rounded-lg border-line text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Dompet Tujuan (opsional)</label>
                    <select wire:model="wallet_id" class="w-full rounded-lg border-line text-sm px-3 py-2">
                        <option value="">Tanpa dompet spesifik</option>
                        @foreach ($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="save" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    @if ($showContributeForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showContributeForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-sm">
            <h3 class="font-display text-lg text-pine-dark mb-4">Setor ke Target</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Jumlah Setoran</label>
                    <input type="number" step="0.01" wire:model="contributeAmount" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('contributeAmount') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Ambil dari Dompet (opsional)</label>
                    <select wire:model="contributeWalletId" class="w-full rounded-lg border-line text-sm px-3 py-2">
                        <option value="">Nggak potong dompet manapun</option>
                        @foreach ($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Rp{{ number_format($w->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-ink-soft mt-1">Kalau pilih dompet, saldo otomatis kepotong sebesar setoran.</p>
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Tanggal</label>
                    <input type="date" wire:model="contributeDate" class="w-full rounded-lg border-line text-sm px-3 py-2">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showContributeForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="saveContribution" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
