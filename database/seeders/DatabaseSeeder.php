<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 調用 UserSeeder
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            OrganizationsSeeder::class,
        ]);

        // 如果需要額外的測試用戶，可以使用 factory
        // User::factory(10)->create();
    }
}
