<div>
    @if (auth()->user()?->hasMenuPermission('user-management.user', 'create'))
        <flux:button wire:click="create" variant="primary" icon="plus">Tambah User</flux:button>
    @endif

    <flux:modal name="user-form-modal" class="w-full max-w-xl" :closable="true">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ $userId ? 'Edit User' : 'Tambah User' }}</flux:heading>
                <flux:subheading>{{ $userId ? 'Perbarui informasi dan role pengguna.' : 'Tambahkan pengguna beserta role yang dimilikinya.' }}</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nama user" autofocus />

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="phone" label="Nomor telepon" />
                <flux:input wire:model="email" label="Email" type="email" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <flux:input wire:model="password" label="Password" type="password" viewable />
                <flux:input wire:model="passwordConfirmation" label="Konfirmasi password" type="password" viewable />
            </div>

            <div>
                <flux:label>Role</flux:label>
                <flux:description>Pilih satu atau lebih role untuk user ini.</flux:description>

                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @forelse ($roles as $role)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 px-3 py-2.5 text-sm text-zinc-700 transition hover:border-zinc-300 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-zinc-600">
                            <input
                                wire:model="roleIds"
                                type="checkbox"
                                value="{{ $role->id }}"
                                class="size-4 rounded border-zinc-300 text-emerald-500 focus:ring-emerald-500 dark:border-zinc-600 dark:bg-zinc-800"
                            />
                            <span>{{ $role->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-zinc-500">Belum ada role yang dapat dipilih.</p>
                    @endforelse
                </div>

                @error('roleIds')
                    <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                @enderror
                @error('roleIds.*')
                    <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="primary" type="submit">{{ $userId ? 'Update User' : 'Simpan User' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-user-modal" class="w-full max-w-lg" :closable="true">
        <form wire:submit="delete" class="space-y-5">
            <div>
                <flux:heading size="lg">Hapus user?</flux:heading>
                <flux:subheading>User <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $deletingName }}</span> akan dihapus.</flux:subheading>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="danger" type="submit">Hapus User</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
