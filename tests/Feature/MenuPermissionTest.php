<?php

use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Route;

test('user menu permissions are combined across assigned roles', function () {
    $reader = Role::create(['name' => 'Reader']);
    $editor = Role::create(['name' => 'Editor']);
    $menu = Menu::create([
        'menu_name' => 'User',
        'url' => '/dashboard/user-management/user',
        'permission_key' => 'user-management.user',
        'is_active' => true,
    ]);
    $user = User::factory()->create();

    UserRole::create(['user_id' => $user->id, 'role_id' => $reader->id]);
    UserRole::create(['user_id' => $user->id, 'role_id' => $editor->id]);
    Privilege::create(['role_id' => $reader->id, 'menu_id' => $menu->id, 'can_read' => true]);
    Privilege::create(['role_id' => $editor->id, 'menu_id' => $menu->id, 'can_update' => true]);

    expect($user->hasMenuPermission('user-management.user', 'read'))->toBeTrue();
    expect($user->hasMenuPermission('user-management.user', 'create'))->toBeFalse();
    expect($user->hasMenuPermission('user-management.user', 'update'))->toBeTrue();
    expect($user->hasMenuPermission('user-management.user', 'delete'))->toBeFalse();
    expect($user->accessibleMenus()->pluck('id')->all())->toContain($menu->id);
});

test('dashboard menu route requires read permission', function () {
    $menu = Menu::create([
        'menu_name' => 'User Management',
        'url' => '/dashboard/user-management/user',
        'permission_key' => 'user-management.user',
        'is_active' => true,
    ]);
    $allowedUser = User::factory()->create();
    $deniedUser = User::factory()->create();
    $role = Role::create(['name' => 'Reader']);

    UserRole::create(['user_id' => $allowedUser->id, 'role_id' => $role->id]);
    Privilege::create(['role_id' => $role->id, 'menu_id' => $menu->id, 'can_read' => true]);

    $this->actingAs($allowedUser)->get(route('user.index'))->assertOk();
    $this->actingAs($deniedUser)->get(route('user.index'))->assertForbidden();
});

test('detail routes can use the same permission key as their menu', function () {
    Route::middleware(['auth', 'menu.permission:user-management.user'])
        ->get('/dashboard/user/{user}', fn () => 'detail user');

    $menu = Menu::create([
        'menu_name' => 'User Management',
        'url' => '/dashboard/user-management/user',
        'permission_key' => 'user-management.user',
        'is_active' => true,
    ]);
    $user = User::factory()->create();
    $role = Role::create(['name' => 'Reader']);

    UserRole::create(['user_id' => $user->id, 'role_id' => $role->id]);
    Privilege::create(['role_id' => $role->id, 'menu_id' => $menu->id, 'can_read' => true]);

    $this->actingAs($user)->get('/dashboard/user/any-user-id')->assertOk();
});
