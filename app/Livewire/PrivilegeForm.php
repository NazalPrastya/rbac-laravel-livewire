<?php

namespace App\Livewire;

use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

final class PrivilegeForm extends Component
{
    public ?string $roleId = null;

    public string $roleName = '';

    /** @var array<int, array{can_read: bool, can_create: bool, can_update: bool, can_delete: bool}> */
    public array $permissions = [];

    #[On('role-privilege-edit')]
    public function edit(string $rowId): void
    {
        $role = Role::findOrFail($rowId);
        $privileges = Privilege::query()
            ->where('role_id', $role->id)
            ->get()
            ->keyBy('menu_id');

        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->permissions = Menu::query()->pluck('id')->mapWithKeys(function (int $menuId) use ($privileges): array {
            $privilege = $privileges->get($menuId);

            return [$menuId => [
                'can_read' => $privilege?->can_read ?? false,
                'can_create' => $privilege?->can_create ?? false,
                'can_update' => $privilege?->can_update ?? false,
                'can_delete' => $privilege?->can_delete ?? false,
            ]];
        })->all();

        $this->js("Flux.modal('privilege-form-modal').show()");
    }

    public function save(): void
    {
        $role = Role::findOrFail($this->roleId);
        $menuIds = Menu::query()->pluck('id');

        DB::transaction(function () use ($role, $menuIds): void {
            foreach ($menuIds as $menuId) {
                $permission = $this->permissions[$menuId] ?? [];
                $attributes = [
                    'can_read' => (bool) ($permission['can_read'] ?? false),
                    'can_create' => (bool) ($permission['can_create'] ?? false),
                    'can_update' => (bool) ($permission['can_update'] ?? false),
                    'can_delete' => (bool) ($permission['can_delete'] ?? false),
                ];

                $privilege = Privilege::withTrashed()
                    ->where('role_id', $role->id)
                    ->where('menu_id', $menuId)
                    ->first();

                if (! in_array(true, $attributes, true)) {
                    $privilege?->delete();

                    continue;
                }

                if ($privilege) {
                    $privilege->fill($attributes + ['updated_by' => auth()->id()])->save();
                    $privilege->restore();

                    continue;
                }

                Privilege::create($attributes + [
                    'role_id' => $role->id,
                    'menu_id' => $menuId,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        });

        $this->js("Flux.modal('privilege-form-modal').close(); Flux.toast('Privilege berhasil diperbarui.')");
    }

    public function render()
    {
        return view('livewire.privilege-form', [
            'menus' => Menu::query()
                ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
                ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                ->orderBy('order')
                ->get(['id', 'menu_name', 'parent_id']),
        ]);
    }
}
