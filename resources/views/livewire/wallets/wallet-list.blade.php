<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="font-display text-xl text-pine-dark">Dompet</h2>
        @if (Auth::user()->isOwner())
            <button wire:click="openWalletForm" class="bg-kunyit text-pine-dark font-semibold text-sm px-4 py-2 rounded-lg">+ Tambah Dompet</button>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($wallets as $wallet)
            <div class="bg-cream rounded-2xl border border-line p-5">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-display font-semibold text-pine-dark">{{ $wallet->name }}</h3>
                    @if (Auth::user()->isOwner())
                        <button wire:click="openWalletForm({{ $wallet->id }})" class="text-xs text-pine font-semibold">Edit</button>
                    @endif
                </div>
                <p class="font-mono text-xl font-semibold {{ $wallet->current_balance < 0 ? 'text-chili' : 'text-pine-dark' }}">
                    Rp{{ number_format($wallet->current_balance, 0, ',', '.') }}
                </p>
                <div class="flex gap-2 mt-2">
                    @if (! $wallet->is_cash_flow)
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-black/10 text-ink-soft">di luar arus kas</span>
                    @endif
                    @if ($wallet->allow_negative)
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-chili/15 text-chili">boleh minus</span>
                    @endif
                </div>
                @if ($wallet->note)
                    <p class="text-xs text-ink-soft mt-2">{{ $wallet->note }}</p>
                @endif
                @if (Auth::user()->canInput())
                    <button wire:click="openMutationForm({{ $wallet->id }})" class="mt-4 w-full text-sm px-3 py-2 rounded-lg border border-line font-medium">
                        + Catat Mutasi
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Modal: form dompet --}}
    @if ($showWalletForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showWalletForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-display text-lg text-pine-dark mb-4">{{ $editingId ? 'Edit' : 'Tambah' }} Dompet</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Nama Dompet</label>
                    <input type="text" wire:model="name" placeholder="BCA / Gopay / Cash Dapur" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('name') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Saldo Awal</label>
                    <input type="number" step="0.01" wire:model="opening_balance" @if($editingId) disabled @endif class="w-full rounded-lg border-line text-sm px-3 py-2 disabled:bg-black/5">
                    @if ($editingId)<p class="text-[11px] text-ink-soft mt-1">Saldo awal cuma bisa diset sekali pas dompet dibuat.</p>@endif
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="is_cash_flow" class="rounded border-line">
                    Tercatat di arus kas utama
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="allow_negative" class="rounded border-line">
                    Saldo boleh minus
                </label>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Catatan (opsional)</label>
                    <textarea wire:model="note" rows="2" class="w-full rounded-lg border-line text-sm px-3 py-2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showWalletForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="saveWallet" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: form mutasi --}}
    @if ($showMutationForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showMutationForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-display text-lg text-pine-dark mb-4">Catat Mutasi</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Jenis</label>
                    <select wire:model.live="mutationType" class="w-full rounded-lg border-line text-sm px-3 py-2">
                        <option value="in">Uang Masuk</option>
                        <option value="out">Uang Keluar</option>
                        <option value="transfer">Transfer ke Dompet Lain</option>
                    </select>
                </div>
                @if ($mutationType === 'transfer')
                    <div>
                        <label class="text-xs font-mono text-ink-soft">Dompet Tujuan</label>
                        <select wire:model="mutationTargetWalletId" class="w-full rounded-lg border-line text-sm px-3 py-2">
                            <option value="">Pilih dompet tujuan</option>
                            @foreach ($wallets as $w)
                                @if ($w->id !== $mutationWalletId)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('mutationTargetWalletId') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
                <div>
                    <label class="text-xs font-mono text-ink-soft">Jumlah</label>
                    <input type="number" step="0.01" wire:model="mutationAmount" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('mutationAmount') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Tanggal</label>
                    <input type="date" wire:model="mutationDate" class="w-full rounded-lg border-line text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Catatan (opsional)</label>
                    <textarea wire:model="mutationNote" rows="2" class="w-full rounded-lg border-line text-sm px-3 py-2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-5">
                <button wire:click="$set('showMutationForm', false)" class="text-sm px-4 py-2 rounded-lg border border-line">Batal</button>
                <button wire:click="saveMutation" class="text-sm px-4 py-2 rounded-lg bg-kunyit text-pine-dark font-semibold">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
