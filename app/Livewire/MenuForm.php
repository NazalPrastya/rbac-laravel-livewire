<?php

namespace App\Livewire;

use App\Concerns\AuthorizesMenuPermission;
use App\Models\Menu;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

final class MenuForm extends Component
{
    use AuthorizesMenuPermission;

    private const PERMISSION_KEY = 'user-management.menu';

    public ?int $menuId = null;

    public string $menuName = '';

    public string $url = '';

    public string $permissionKey = '';

    public int $order = 0;

    public string $icon = 'material-symbols:dashboard';

    public ?int $parentId = null;

    public bool $isActive = true;

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function create(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'create');
        $this->resetForm();
        $this->js("Flux.modal('menu-form-modal').show()");
    }

    #[On('menu-edit')]
    public function edit(int $rowId): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'update');
        $menu = Menu::findOrFail($rowId);

        $this->menuId = $menu->id;
        $this->menuName = $menu->menu_name;
        $this->url = $menu->url;
        $this->permissionKey = $menu->permission_key ?? '';
        $this->order = $menu->order;
        $this->icon = $menu->icon ?? '';
        $this->parentId = $menu->parent_id;
        $this->isActive = $menu->is_active;
        $this->resetValidation();

        $this->js("Flux.modal('menu-form-modal').show()");
    }

    public function save(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, $this->menuId ? 'update' : 'create');
        $validated = $this->validate();

        $attributes = [
            'menu_name' => $validated['menuName'],
            'url' => $validated['url'],
            'permission_key' => $validated['permissionKey'],
            'order' => $validated['order'],
            'icon' => $validated['icon'] ?: null,
            'parent_id' => $validated['parentId'] ?: null,
            'is_active' => $validated['isActive'],
        ];

        $this->menuId
            ? Menu::findOrFail($this->menuId)->update($attributes)
            : Menu::create($attributes);

        $message = $this->menuId ? 'Menu berhasil diperbarui.' : 'Menu berhasil ditambahkan.';

        $this->dispatch('menu-updated');
        $this->resetForm();
        $this->js("Flux.modal('menu-form-modal').close(); Flux.toast(".json_encode($message).')');
    }

    #[On('menu-delete-request')]
    public function confirmDelete(int $rowId): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'delete');
        $menu = Menu::findOrFail($rowId);

        $this->deletingId = $menu->id;
        $this->deletingName = $menu->menu_name;
        $this->js("Flux.modal('delete-menu-modal').show()");
    }

    public function delete(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'delete');
        $menu = Menu::findOrFail($this->deletingId);

        if ($menu->children()->exists()) {
            $this->addError('delete', 'Menu ini masih memiliki submenu. Pindahkan atau hapus submenu terlebih dahulu.');

            return;
        }

        $menu->delete();
        $this->dispatch('menu-updated');
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetErrorBag('delete');
        $this->js("Flux.modal('delete-menu-modal').close(); Flux.toast('Menu berhasil dihapus.')");
    }

    public function parentMenus()
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->when($this->menuId, fn ($query) => $query->whereKeyNot($this->menuId))
            ->orderBy('order')
            ->orderBy('menu_name')
            ->get(['id', 'menu_name']);
    }

    protected function rules(): array
    {
        return [
            'menuName' => ['required', 'string', 'max:50'],
            'url' => ['required', 'string', 'max:255'],
            'permissionKey' => ['required', 'string', 'max:100', 'alpha_dash:ascii', Rule::unique('menus', 'permission_key')->ignore($this->menuId)],
            'order' => ['required', 'integer', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parentId' => ['nullable', 'integer', Rule::exists('menus', 'id')],
            'isActive' => ['boolean'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset('menuId', 'menuName', 'url', 'permissionKey', 'order', 'parentId');
        $this->icon = 'material-symbols:dashboard';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.menu-form');
    }
}
