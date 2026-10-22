<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TrackingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['tracking.view', 'tracking.manage'];

        foreach ($names as $name) {
            Permission::findOrCreate($name, 'admin');
        }

        Role::where('guard_name', 'admin')
            ->whereHas('permissions', fn ($q) => $q->where('name', 'settings.manage'))
            ->get()
            ->each(fn ($role) => $role->givePermissionTo($names));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}