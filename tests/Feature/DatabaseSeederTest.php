<?php

use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder creates an administrator with full menu privileges', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'testing@gmail.com')->firstOrFail();
    $role = Role::where('name', 'Super Admin')->firstOrFail();

    expect(User::where('email', 'testing@gmail.com')->count())->toBe(1);
    expect(Role::where('name', 'Super Admin')->count())->toBe(1);
    expect($admin->roles()->pluck('roles.id')->all())->toEqual([$role->id]);
    expect(Menu::count())->toBe(5);
    expect(Privilege::where('role_id', $role->id)->count())->toBe(5);
    expect($admin->hasMenuPermission('user-management.user', 'read'))->toBeTrue();
    expect($admin->hasMenuPermission('user-management.user', 'create'))->toBeTrue();
    expect($admin->hasMenuPermission('user-management.user', 'update'))->toBeTrue();
    expect($admin->hasMenuPermission('user-management.user', 'delete'))->toBeTrue();
});
