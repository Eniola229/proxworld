<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            CurrencySeeder::class,
            PricingSettingsSeeder::class,
        ]);

        // First super admin — change this password immediately after first login.
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'hi@gmail.com'],
            ['name' => 'ProxWorld Owner', 'password' => Hash::make('ChangeMe123!'), 'role' => 'super_admin', 'is_active' => true]
        );
        $superAdmin->assignRole('super_admin');
    }
}
