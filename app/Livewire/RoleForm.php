<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Attributes\On;
use Livewire\Component;

final class RoleForm extends Component
{
    public ?string $roleId = null;

    public string $name = '';

    public string $description = '';

    public ?string $deletingId = null;

    public string $deletingName = '';

    public function create(): void
    {
        $this->resetForm();
        $this->js("Flux.modal('role-form-modal').show()");
    }

    #[On('role-edit')]
    public function edit(string $rowId): void
    {
        $role = Role::findOrFail($rowId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->description = $role->desc ?? '';
        $this->resetValidation();
        $this->js("Flux.modal('role-form-modal').show()");
    }

    public function save(): void
    {
        $validated = $this->validate();
        $attributes = [
            'name' => $validated['name'],
            'desc' => $validated['description'] ?: null,
        ];

        $this->roleId
            ? Role::findOrFail($this->roleId)->update($attributes)
            : Role::create($attributes);

        $message = $this->roleId ? 'Role berhasil diperbarui.' : 'Role berhasil ditambahkan.';
        $this->dispatch('role-updated');
        $this->resetForm();
        $this->js("Flux.modal('role-form-modal').close(); Flux.toast(".json_encode($message).')');
    }

    #[On('role-delete-request')]
    public function confirmDelete(string $rowId): void
    {
        $role = Role::findOrFail($rowId);
        $this->deletingId = $role->id;
        $this->deletingName = $role->name;
        $this->js("Flux.modal('delete-role-modal').show()");
    }

    public function delete(): void
    {
        Role::findOrFail($this->deletingId)->delete();
        $this->dispatch('role-updated');
        $this->deletingId = null;
        $this->deletingName = '';
        $this->js("Flux.modal('delete-role-modal').close(); Flux.toast('Role berhasil dihapus.')");
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset('roleId', 'name', 'description');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.role-form');
    }
}
