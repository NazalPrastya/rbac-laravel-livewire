<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::withTrashed()->firstOrNew(['email' => 'testing@gmail.com']);
            $admin->fill([
                'name' => 'Super Admin',
                'phone' => '08123456789',
                'password' => Hash::make('password'),
            ]);
            $admin->save();
            $admin->restore();

            $superAdmin = Role::withTrashed()->firstOrNew(['name' => 'Super Admin']);
            $superAdmin->desc = 'Memiliki akses penuh ke seluruh menu dan fitur aplikasi.';
            $superAdmin->save();
            $superAdmin->restore();

            $dashboard = $this->upsertMenu('Dashboard', [
                'url' => '/dashboard',
                'permission_key' => 'dashboard',
                'order' => 0,
                'icon' => 'material-symbols:dashboard',
                'parent_id' => null,
                'is_active' => true,
            ]);
            $userManagement = $this->upsertMenu('User Management', [
                'url' => '#',
                'permission_key' => 'user-management',
                'order' => 1,
                'icon' => 'mage:users-fill',
                'parent_id' => null,
                'is_active' => true,
            ]);
            $user = $this->upsertMenu('User', [
                'url' => '/dashboard/user-management/user',
                'permission_key' => 'user-management.user',
                'order' => 1,
                'icon' => 'lucide:users',
                'parent_id' => $userManagement->id,
                'is_active' => true,
            ]);
            $role = $this->upsertMenu('Role & Permission', [
                'url' => '/dashboard/user-management/role',
                'permission_key' => 'user-management.role',
                'order' => 2,
                'icon' => 'lucide:shield-check',
                'parent_id' => $userManagement->id,
                'is_active' => true,
            ]);
            $menu = $this->upsertMenu('Menu', [
                'url' => '/dashboard/user-management/menu',
                'permission_key' => 'user-management.menu',
                'order' => 3,
                'icon' => 'lucide:menu',
                'parent_id' => $userManagement->id,
                'is_active' => true,
            ]);

            UserRole::firstOrCreate([
                'user_id' => $admin->id,
                'role_id' => $superAdmin->id,
            ]);

            collect([$dashboard, $userManagement, $user, $role, $menu])->each(function (Menu $menu) use ($superAdmin, $admin): void {
                $privilege = Privilege::withTrashed()->firstOrNew([
                    'role_id' => $superAdmin->id,
                    'menu_id' => $menu->id,
                ]);
                $privilege->fill([
                    'can_read' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_by' => $privilege->created_by ?? $admin->id,
                    'updated_by' => $admin->id,
                ]);
                $privilege->save();
                $privilege->restore();
            });
        });
    }

    /** @param array<string, mixed> $attributes */
    private function upsertMenu(string $name, array $attributes): Menu
    {
        return Menu::query()->updateOrCreate(['menu_name' => $name], $attributes);
    }
}
