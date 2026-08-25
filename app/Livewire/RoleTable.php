<?php

namespace App\Livewire;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RoleTable extends PowerGridComponent
{
    public string $tableName = 'roleTable';

    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage(15, [10, 15, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Role::query()->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('desc')
            ->add('desc_display', fn (Role $model) => e($model->desc ?? '-'));
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'id')->index(),
            Column::make('Nama Role', 'name')->sortable()->searchable(),
            Column::make('Deskripsi', 'desc_display', 'desc')->sortable()->searchable(),
            Column::action('Actions'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('desc')->operators(['contains']),
        ];
    }

    #[On('role-updated')]
    public function refreshTable(): void {}

    public function actions(Role $row): array
    {
        return [
            Button::add('privilege')
                ->slot('<iconify-icon icon="solar:key-bold" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-green-500   hover:bg-emerald-600')
                ->dispatch('role-privilege-edit', ['rowId' => $row->id]),
            Button::add('edit')
                ->slot('<iconify-icon icon="lucide:pencil" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-yellow-300 text-zinc-900 hover:bg-yellow-400')
                ->dispatch('role-edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('<iconify-icon icon="lucide:trash-2" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600')
                ->dispatch('role-delete-request', ['rowId' => $row->id]),
        ];
    }
}
