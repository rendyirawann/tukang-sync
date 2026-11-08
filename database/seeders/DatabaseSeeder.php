<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            SaasSeeder::class,
        ]);

        // Tim contoh hanya untuk lingkungan pengembangan.
        if (! app()->isProduction()) {
            $this->call(DemoTimSeeder::class);
        }
    }
}
