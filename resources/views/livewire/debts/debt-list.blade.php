<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div class="flex gap-2 bg-black/5 p-1 rounded-lg">
            <button wire:click="$set('tab', 'debt')" @class(['px-4 py-1.5 rounded-md text-sm font-semibold', 'bg-cream text-pine-dark' => $tab==='debt'])>Utang</button>
            <button wire:click="$set('tab', 'receivable')" @class(['px-4 py-1.5 rounded-md text-sm font-semibold', 'bg-cream text-pine-dark' => $tab==='receivable'])>Piutang</button>
        </div>
        @if (Auth::user()->canInput())
            <button wire:click="openForm" class="bg-kunyit text-pine-dark font-semibold text-sm px-4 py-2 rounded-lg">+ Catat {{ $tab === 'debt' ? 'Utang' : 'Piutang' }}</button>
        @endif
    </div>

    <div class="bg-cream rounded-2xl border border-line divide-y divide-dashed divide-line">
        @forelse ($debts as $debt)
            <div class="p-4 flex items-center justify-between gap-4">
                <div>
                    <p class="font-medium">{{ $debt->party_name }}</p>
                    <p class="text-xs text-ink-soft font-mono">
                        {{ $debt->date->format('d M Y') }}
                        @if ($debt->due_date) · jatuh tempo {{ $debt->due_date->format('d M Y') }} @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-mono font-semibold">Rp{{ number_format($debt->remaining_amount, 0, ',', '.') }}</p>
                    <span @class([
                        'text-[10px] font-mono px-2 py-0.5 rounded-full',
                        'bg-chili/15 text-chili' => $debt->status === 'unpaid',
                        'bg-kunyit/15 text-kunyit-dark' => $debt->status === 'partial',
                        'bg-sprout/15 text-sprout' => $debt->status === 'paid',
                    ])>{{ $debt->status }}</span>
                </div>
                @if (Auth::user()->canInput() && $debt->status !== 'paid')
                    <button wire:click="openPaymentForm({{ $debt->id }})" class="text-xs text-pine font-semibold whitespace-nowrap">+ Bayar</button>
                @endif
            </div>
        @empty
            <p class="p-6 text-sm text-ink-soft text-center">Belum ada catatan.</p>
        @endforelse
    </div>

    @if ($showForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-display text-lg text-pine-dark mb-4">Catat {{ $type === 'debt' ? 'Utang' : 'Piutang' }}</h3>
            <div class="space-y-3">
                <input type="hidden" wire:model="type">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Nama Pihak</label>
                    <input type="text" wire:model="party_name" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('party_name') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Jumlah</label>
                    <input type="number" step="0.01" wire:model="amount" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('amount') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-mono text-ink-soft">Tanggal</label>
                        <input type="date" wire:model="date" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="text-xs font-mono text-ink-soft">Jatuh Tempo (opsional)</label>
                        <input type="date" wire:model="due_date" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Catatan (opsional)</label>
                    <textarea wire:model="note" rows="2" class="w-full rounded-lg border-line text-sm px-3 py-2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="save" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    @if ($showPaymentForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showPaymentForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-sm">
            <h3 class="font-display text-lg text-pine-dark mb-4">Catat Pembayaran</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Jumlah Bayar</label>
                    <input type="number" step="0.01" wire:model="paymentAmount" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('paymentAmount') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Tanggal</label>
                    <input type="date" wire:model="paymentDate" class="w-full rounded-lg border-line text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Catatan (opsional)</label>
                    <textarea wire:model="paymentNote" rows="2" class="w-full rounded-lg border-line text-sm px-3 py-2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showPaymentForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="savePayment" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
