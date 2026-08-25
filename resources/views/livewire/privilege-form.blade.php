<div>
    <flux:modal name="privilege-form-modal" class="w-full max-w-6xl" :closable="true">
        <form wire:submit="save" class="space-y-5">
            <div>
                <flux:heading size="lg">Kelola Privilege: {{ $roleName }}</flux:heading>
                <flux:subheading>Atur izin akses, tambah, ubah, dan hapus untuk setiap menu.</flux:subheading>
            </div>

            <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="w-full min-w-[700px] text-sm">
                    <thead >
                        <tr>
                            <th class="px-3 py-3 text-left font-medium">Menu</th>
                            <th class="px-3 py-3 text-center font-medium">Izin Mengakses</th>
                            <th class="px-3 py-3 text-center font-medium">Izin Menambah</th>
                            <th class="px-3 py-3 text-center font-medium">Izin Mengupdate</th>
                            <th class="px-3 py-3 text-center font-medium">Izin Menghapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($menus as $menu)
                            <tr class="bg-white dark:bg-zinc-900">
                                <td class="px-3 py-2.5 font-medium text-zinc-800 dark:text-zinc-100">
                                    <span @class(['inline-flex items-center gap-2', 'ps-8' => $menu->parent_id])>
                                        @if ($menu->parent_id)
                                            <iconify-icon icon="lucide:corner-down-right" class="size-4 text-zinc-400" aria-hidden="true"></iconify-icon>
                                        @endif
                                        {{ $menu->menu_name }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center"><flux:checkbox wire:model="permissions.{{ $menu->id }}.can_read" /></td>
                                <td class="px-3 py-2.5 text-center"><flux:checkbox wire:model="permissions.{{ $menu->id }}.can_create" /></td>
                                <td class="px-3 py-2.5 text-center"><flux:checkbox wire:model="permissions.{{ $menu->id }}.can_update" /></td>
                                <td class="px-3 py-2.5 text-center"><flux:checkbox wire:model="permissions.{{ $menu->id }}.can_delete" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-8 text-center text-zinc-500">Belum ada menu yang dapat diatur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close><flux:button variant="filled">Batal</flux:button></flux:modal.close>
                <flux:button variant="primary" type="submit">Simpan Privilege</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
