<div class="space-y-6">
    <div class="bg-cream rounded-2xl border border-line p-6">
        <h3 class="font-display text-lg text-pine-dark mb-4">Undang User Baru</h3>
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-mono text-ink-soft">Email</label>
                <input type="email" wire:model="email" class="w-full rounded-lg border-line text-sm px-3 py-2">
                @error('email') <p class="text-xs text-chili mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Hak Akses</label>
                <select wire:model="permission" class="rounded-lg border-line text-sm px-3 py-2">
                    <option value="view_only">Lihat Saja</option>
                    <option value="can_input">Bisa Input</option>
                </select>
            </div>
            <button wire:click="invite" class="bg-kunyit text-pine-dark font-semibold text-sm px-4 py-2 rounded-lg">Kirim Undangan</button>
        </div>
    </div>

    @if ($invitations->count())
        <div class="bg-cream rounded-2xl border border-line p-6">
            <h3 class="font-display text-base text-pine-dark mb-3">Undangan Pending</h3>
            <div class="divide-y divide-dashed divide-line">
                @foreach ($invitations as $inv)
                    <div class="py-2 flex justify-between items-center text-sm">
                        <span>{{ $inv->email }} <span class="text-xs font-mono text-ink-soft">({{ $inv->permission }})</span></span>
                        <button wire:click="cancelInvitation({{ $inv->id }})" class="text-xs text-chili font-semibold">Batalkan</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-cream rounded-2xl border border-line p-6">
        <h3 class="font-display text-base text-pine-dark mb-3">Anggota Rumah</h3>
        <div class="divide-y divide-dashed divide-line">
            @forelse ($members as $m)
                <div class="py-3 flex justify-between items-center gap-3">
                    <div>
                        <p class="font-medium text-sm">{{ $m->name }}</p>
                        <p class="text-xs text-ink-soft">{{ $m->email }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select wire:change="updatePermission({{ $m->id }}, $event.target.value)" class="text-xs rounded-lg border-line px-2 py-1">
                            <option value="view_only" @selected($m->permission === 'view_only')>Lihat Saja</option>
                            <option value="can_input" @selected($m->permission === 'can_input')>Bisa Input</option>
                        </select>
                        <button wire:click="revoke({{ $m->id }})" wire:confirm="Cabut akses user ini?" class="text-xs text-chili font-semibold">Cabut Akses</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft py-2">Belum ada anggota lain.</p>
            @endforelse
        </div>
    </div>
</div>
