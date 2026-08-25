<div>
    @if (auth()->user()?->hasMenuPermission('user-management.menu', 'create'))
        <flux:button wire:click="create" variant="primary" icon="plus">
            Tambah Menu
        </flux:button>
    @endif

    <flux:modal name="menu-form-modal" class="w-full max-w-xl" :closable="true">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ $menuId ? 'Edit Menu' : 'Tambah Menu' }}</flux:heading>
                <flux:subheading>
                    {{ $menuId ? 'Perbarui konfigurasi menu.' : 'Gunakan nama iconify, misalnya material-symbols:dashboard.' }}
                </flux:subheading>
            </div>

            <flux:input wire:model="menuName" label="Nama menu" autofocus />

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="url" label="URL" placeholder="/dashboard/example atau # untuk parent" />
                <flux:input wire:model="permissionKey" label="Permission key" placeholder="user-management.user" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:select wire:model="parentId" label="Parent menu">
                    <option value="">Tidak ada (menu utama)</option>
                    @foreach ($this->parentMenus() as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->menu_name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="order" label="Urutan" type="number" min="0" />
            </div>

            <div>
                <flux:input wire:model="icon" label="Iconify icon" placeholder="material-symbols:dashboard" />
                @if ($icon)
                    <div class="mt-2 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <iconify-icon icon="{{ $icon }}" class="size-5" aria-hidden="true"></iconify-icon>
                        <span>Preview icon</span>
                    </div>
                @endif
            </div>

            <flux:switch wire:model="isActive" label="Menu aktif" />

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="primary" type="submit">
                    {{ $menuId ? 'Update Menu' : 'Simpan Menu' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-menu-modal" class="w-full max-w-lg" :closable="true">
        <form wire:submit="delete" class="space-y-5">
            <div>
                <flux:heading size="lg">Hapus menu?</flux:heading>
                <flux:subheading>
                    Menu <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $deletingName }}</span> akan dihapus permanen.
                    Pastikan tidak ada submenu yang masih menggunakannya.
                </flux:subheading>
            </div>

            @error('delete')
                <flux:text class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
            @enderror

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="filled">Batal</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" type="submit">Hapus menu</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
