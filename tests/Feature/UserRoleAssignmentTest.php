<?php

use App\Livewire\UserForm;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Livewire\Livewire;

test('a user can be assigned multiple roles', function () {
    $admin = Role::create(['name' => 'Admin']);
    $operator = Role::create(['name' => 'Operator']);

    Livewire::test(UserForm::class)
        ->set('name', 'User Baru')
        ->set('phone', '08123456789')
        ->set('email', 'user@example.com')
        ->set('password', 'password123')
        ->set('passwordConfirmation', 'password123')
        ->set('roleIds', [$admin->id, $operator->id])
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'user@example.com')->firstOrFail();

    expect($user->roles()->pluck('roles.id')->all())
        ->toEqualCanonicalizing([$admin->id, $operator->id]);
});

test('updating a user replaces their assigned roles', function () {
    $admin = Role::create(['name' => 'Admin']);
    $operator = Role::create(['name' => 'Operator']);
    $user = User::factory()->create();

    UserRole::create(['user_id' => $user->id, 'role_id' => $admin->id]);

    Livewire::test(UserForm::class)
        ->call('edit', $user->id)
        ->set('roleIds', [$operator->id])
        ->call('save')
        ->assertHasNoErrors();

    expect($user->refresh()->roles()->pluck('roles.id')->all())
        ->toEqual([$operator->id]);
});
