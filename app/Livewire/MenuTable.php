<?php

namespace App\Livewire;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MenuTable extends PowerGridComponent
{
    public string $tableName = 'menuTable';

    public function setUp(): array
    {
        return [
            PowerGrid::footer()
                ->showPerPage(15, [10, 15, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Menu::query()
            ->with('parent:id,menu_name')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('order');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('menu_name')
            ->add('url')
            ->add('order')
            ->add('icon')
            ->add('parent_id')
            ->add('is_active')
            ->add('number_display', fn (Menu $model) => $model->parent_id ? '-' : $model->id)
            ->add('icon_display', function (Menu $model): string {
                $icon = e($model->icon ?: 'lucide:circle-help');

                return "<iconify-icon icon=\"{$icon}\" class=\"size-4\" aria-hidden=\"true\"></iconify-icon>";
            })
            ->add('menu_name_display', function (Menu $model): string {
                $name = e($model->menu_name);

                if ($model->parent_id) {
                    return "<span class=\"inline-flex items-center gap-2 ps-6\"><iconify-icon icon=\"lucide:corner-down-right\" class=\"size-4 text-zinc-400\" aria-hidden=\"true\"></iconify-icon>{$name}</span>";
                }

                return "<span class=\"inline-flex items-center gap-2\">{$name}<span class=\"rounded-md bg-yellow-300 px-2 py-1 text-xs font-medium text-zinc-900\">Menu Induk</span></span>";
            })
            ->add('status_display', fn (Menu $model) => $model->is_active
                ? '<span class="rounded-md bg-emerald-500 px-2 py-1 text-xs font-medium text-emerald-950">Aktif</span>'
                : '<span class="rounded-md bg-zinc-200 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200">Nonaktif</span>')
            ->add('parent_display', fn (Menu $model) => e($model->parent?->menu_name ?? '-'));
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'number_display', 'id'),
            Column::make('Icon', 'icon_display'),
            Column::make('Nama', 'menu_name_display', 'menu_name')
                ->sortable()
                ->searchable(),
            Column::make('URL', 'url')
                ->sortable()
                ->searchable(),
            Column::make('Order', 'order')->sortable(),
            Column::make('Status', 'status_display', 'is_active'),
            Column::make('Parent', 'parent_display'),
            Column::action('Actions'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('menu_name')->operators(['contains']),
            Filter::inputText('url')->operators(['contains']),
            Filter::boolean('is_active'),
        ];
    }

    #[On('menu-updated')]
    public function refreshTable(): void {}

    public function actions(Menu $row): array
    {
        return [
            Button::add('edit')
                ->slot('<iconify-icon icon="lucide:pencil" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-yellow-300 text-zinc-900 hover:bg-yellow-400')
                ->dispatch('menu-edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('<iconify-icon icon="lucide:trash-2" class="size-4" aria-hidden="true"></iconify-icon>')
                ->id()
                ->class('inline-flex size-9 items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600')
                ->dispatch('menu-delete-request', ['rowId' => $row->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
