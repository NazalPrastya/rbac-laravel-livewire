<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'userTable';

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
        return User::query()->with('roles:id,name')->latest();
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
            ->add('phone')
            ->add('email')
            ->add('roles_display', function (User $model): string {
                $roles = $model->roles->pluck('name')->map(fn (string $role) => e($role));

                return $roles->isEmpty()
                    ? '-'
                    : '<span class="inline-flex flex-wrap gap-1">'.$roles->map(fn (string $role) => '<span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-100">'.$role.'</span>')->implode('').'</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'id')->index(),
            Column::make('Nama', 'name')->sortable()->searchable(),
            Column::make('Nomor Telepon', 'phone')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Role', 'roles_display'),
            Column::action('Actions'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('phone')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
        ];
    }

    #[On('user-updated')]
    public function refreshTable(): void {}

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('<iconify-icon icon="lucide:pencil" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-yellow-300 text-zinc-900 hover:bg-yellow-400')
                ->dispatch('user-edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('<iconify-icon icon="lucide:trash-2" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600')
                ->dispatch('user-delete-request', ['rowId' => $row->id]),
        ];
    }
}
