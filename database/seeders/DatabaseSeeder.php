<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Testing',
            'phone' => '08123456789',
            'email' => 'testing@gmail.com',
            'password' => bcrypt('password')
        ]);

        Menu::create([
            'menu_name' => 'Dashboard',
            'url' => '/dashboard',
            'order' => 0,
            'icon' => 'material-symbols:dashboard',
            'is_active' => true
        ]);

        $masterData = Menu::create([
            'menu_name' => 'Master Data',
            'url' => '#',
            'order' => 1,
            'icon' => 'tdesign:data',
            'is_active' => true
        ]);
        
        Menu::create([
            'menu_name' => 'Enumeration',
            'url' => '/dashboard/enumeration',
            'order' => 1,
            'is_active' => true,
            'parent_id' => $masterData->id
        ]);

        $userManagement = Menu::create([
            'menu_name' => 'User Management',
            'url' => '#',
            'order' => 2,
            'icon' => 'mage:users-fill',
            'is_active' => true
        ]);

        Menu::create([
            'menu_name' => 'User',
            'url' => '/dashboard/user-management/user',
            'order' => 1,
            'is_active' => true,
            'parent_id' => $userManagement->id
        ]);

        Menu::create([
            'menu_name' => 'Role',
            'url' => '/dashboard/user-management/role',
            'order' => 2,
            'is_active' => true,
            'parent_id' => $userManagement->id
        ]);
        Menu::create([
            'menu_name' => 'Menu',
            'url' => '/dashboard/user-management/menu',
            'order' => 3,
            'is_active' => true,
            'parent_id' => $masterData->id
        ]);
    }
}
