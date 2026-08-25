<?php

namespace App\Livewire;

use App\Concerns\AuthorizesMenuPermission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

final class UserForm extends Component
{
    use AuthorizesMenuPermission;

    private const PERMISSION_KEY = 'user-management.user';

    public ?string $userId = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    /** @var array<int, string> */
    public array $roleIds = [];

    public ?string $deletingId = null;

    public string $deletingName = '';

    public function create(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'create');
        $this->resetForm();
        $this->js("Flux.modal('user-form-modal').show()");
    }

    #[On('user-edit')]
    public function edit(string $rowId): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'update');
        $user = User::findOrFail($rowId);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone ?? '';
        $this->email = $user->email;
        $this->roleIds = UserRole::query()
            ->where('user_id', $user->id)
            ->pluck('role_id')
            ->all();
        $this->resetValidation();
        $this->js("Flux.modal('user-form-modal').show()");
    }

    public function save(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, $this->userId ? 'update' : 'create');
        $validated = $this->validate();
        $attributes = [
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?: null,
            'email' => $validated['email'],
        ];

        DB::transaction(function () use ($attributes, $validated): void {
            if ($this->userId) {
                $user = User::findOrFail($this->userId);

                if ($validated['password']) {
                    $attributes['password'] = $validated['password'];
                }

                $user->update($attributes);
            } else {
                $attributes['password'] = $validated['password'];
                $user = User::create($attributes);
            }

            UserRole::query()->where('user_id', $user->id)->delete();

            foreach ($validated['roleIds'] as $roleId) {
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
            }
        });

        $message = $this->userId ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.';
        $this->dispatch('user-updated');
        $this->resetForm();
        $this->js("Flux.modal('user-form-modal').close(); Flux.toast(".json_encode($message).')');
    }

    #[On('user-delete-request')]
    public function confirmDelete(string $rowId): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'delete');
        $user = User::findOrFail($rowId);

        $this->deletingId = $user->id;
        $this->deletingName = $user->name;
        $this->js("Flux.modal('delete-user-modal').show()");
    }

    public function delete(): void
    {
        $this->authorizeMenuPermission(self::PERMISSION_KEY, 'delete');
        User::findOrFail($this->deletingId)->delete();
        $this->dispatch('user-updated');
        $this->deletingId = null;
        $this->deletingName = '';
        $this->js("Flux.modal('delete-user-modal').close(); Flux.toast('User berhasil dihapus.')");
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => [$this->userId ? 'nullable' : 'required', 'string'],
            'roleIds' => ['nullable', 'array'],
            'roleIds.*' => ['required', 'string', 'distinct', Rule::exists('roles', 'id')],
        ];
    }

    private function resetForm(): void
    {
        $this->reset('userId', 'name', 'phone', 'email', 'password', 'passwordConfirmation', 'roleIds');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.user-form', [
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
