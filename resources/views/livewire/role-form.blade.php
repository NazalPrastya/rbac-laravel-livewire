<div>
    <flux:button wire:click="create" variant="primary" icon="plus">Tambah Role</flux:button>

    <flux:modal name="role-form-modal" class="w-full max-w-xl" :closable="true">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ $roleId ? 'Edit Role' : 'Tambah Role' }}</flux:heading>
                <flux:subheading>{{ $roleId ? 'Perbarui informasi role pengguna.' : 'Tambahkan role untuk pengaturan hak akses pengguna.' }}</flux:subheading>
            </div>

            <flux:input wire:model="name" label="Nama role" autofocus />
            <flux:textarea wire:model="description" label="Deskripsi" rows="4" placeholder="Deskripsi role (opsional)" />

            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="primary" type="submit">{{ $roleId ? 'Update Role' : 'Simpan Role' }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-role-modal" class="w-full max-w-lg" :closable="true">
        <form wire:submit="delete" class="space-y-5">
            <div>
                <flux:heading size="lg">Hapus role?</flux:heading>
                <flux:subheading>Role <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $deletingName }}</span> akan dihapus.</flux:subheading>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="danger" type="submit">Hapus Role</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
