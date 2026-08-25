<?php

use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'Dashboard Reader']);
    $menu = Menu::create([
        'menu_name' => 'Dashboard',
        'url' => '/dashboard',
        'permission_key' => 'dashboard',
        'is_active' => true,
    ]);

    UserRole::create(['user_id' => $user->id, 'role_id' => $role->id]);
    Privilege::create(['role_id' => $role->id, 'menu_id' => $menu->id, 'can_read' => true]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
