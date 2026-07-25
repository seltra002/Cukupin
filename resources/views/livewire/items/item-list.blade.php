<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari kebutuhan..."
                   class="rounded-lg border-line text-sm px-3 py-2">
            <select wire:model.live="filterCategory" class="rounded-lg border-line text-sm px-3 py-2">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="rounded-lg border-line text-sm px-3 py-2">
                <option value="">Semua Status</option>
                <option value="aman">Aman</option>
                <option value="menipis">Menipis</option>
                <option value="habis">Habis</option>
            </select>
        </div>
        @if (Auth::user()->canInput())
            <button wire:click="openForm" class="bg-kunyit text-pine-dark font-semibold text-sm px-4 py-2 rounded-lg">
                + Catat Kebutuhan
            </button>
        @endif
    </div>

    <div class="bg-cream rounded-2xl border border-line overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-black/5 text-ink-soft text-xs uppercase font-mono">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Kategori</th>
                    <th class="text-right px-4 py-3">Qty</th>
                    <th class="text-right px-4 py-3">Harga</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    @if (Auth::user()->canInput())<th class="px-4 py-3"></th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr class="border-t border-dashed border-line">
                        <td class="px-4 py-3 font-medium">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-ink-soft">{{ $item->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ rtrim(rtrim($item->qty, '0'), '.') }} {{ $item->unit }}</td>
                        <td class="px-4 py-3 text-right font-mono">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'text-xs font-mono px-2 py-1 rounded-full',
                                'bg-sprout/15 text-sprout' => $item->stock_status === 'aman',
                                'bg-kunyit/15 text-kunyit-dark' => $item->stock_status === 'menipis',
                                'bg-chili/15 text-chili' => $item->stock_status === 'habis',
                            ])>{{ $item->stock_status }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-soft">{{ $item->date->format('d M Y') }}</td>
                        @if (Auth::user()->canInput())
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <button wire:click="openForm({{ $item->id }})" class="text-xs text-pine font-semibold mr-3">Edit</button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Yakin mau hapus catatan ini?" class="text-xs text-chili font-semibold">Hapus</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $items->links() }}</div>
    </div>

    {{-- Modal form --}}
    @if ($showForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" wire:click.self="$set('showForm', false)">
        <div class="bg-cream rounded-2xl p-6 w-full max-w-md">
            <h3 class="font-display text-lg text-pine-dark mb-4">{{ $editingId ? 'Edit' : 'Catat' }} Kebutuhan</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-mono text-ink-soft">Nama</label>
                    <input type="text" wire:model="name" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    @error('name') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Kategori</label>
                    <select wire:model="category_id" class="w-full rounded-lg border-line text-sm px-3 py-2">
                        <option value="">Tanpa kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-mono text-ink-soft">Jumlah</label>
                        <input type="number" step="0.01" wire:model="qty" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="text-xs font-mono text-ink-soft">Satuan</label>
                        <input type="text" wire:model="unit" placeholder="kg/pcs/liter" class="w-full rounded-lg border-line text-sm px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Harga</label>
                    <input type="number" step="0.01" wire:model="price" class="w-full rounded-lg border-line text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Status Stok</label>
                    <select wire:model="stock_status" class="w-full rounded-lg border-line text-sm px-3 py-2">
                        <option value="aman">Aman</option>
                        <option value="menipis">Menipis</option>
                        <option value="habis">Habis</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-mono text-ink-soft">Tanggal</label>
                    <input type="date" wire:model="date" class="w-full rounded-lg border-line text-sm px-3 py-2">
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
</div>
