<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // The list (with Persian names) lives in config/shura_permissions.php.
        $permissions = collect(config('shura_permissions.permissions'))
            ->map(fn ($item, $name) => ['name' => $name, 'group_name' => $item[0]])
            ->values()
            ->all();

        foreach ($permissions as $permission) {
            $existing = Permission::where('name', $permission['name'])
                ->where('guard_name', 'web')
                ->first();

            if ($existing) {
                DB::table('permissions')
                    ->where('id', $existing->id)
                    ->update(['group_name' => $permission['group_name']]);
            } else {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'guard_name' => 'web',
                    'group_name' => $permission['group_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Permission::where('name', 'all.reports')->delete();

        $allPermissionNames = collect($permissions)->pluck('name')->toArray();

        $superAdmin = Role::updateOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );
        $superAdmin->syncPermissions($allPermissionNames);

        $admin = Role::updateOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web']
        );
        $admin->syncPermissions($allPermissionNames);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
