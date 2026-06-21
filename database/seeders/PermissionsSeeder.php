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

        $permissions = [
            ['name' => 'all.users', 'group_name' => 'Users'],
            ['name' => 'all.category', 'group_name' => 'Category'],
            ['name' => 'all.income', 'group_name' => 'Income'],
            ['name' => 'all.undeposited', 'group_name' => 'Undeposited'],
            ['name' => 'all.recieve.payment', 'group_name' => 'Recieve'],
            ['name' => 'all.financial.report', 'group_name' => 'Financial'],
            ['name' => 'all.credits', 'group_name' => 'Credits'],
            ['name' => 'all.aid', 'group_name' => 'Aid'],
            ['name' => 'all.expense', 'group_name' => 'Expense'],
            ['name' => 'all.report', 'group_name' => 'Reports'],
            ['name' => 'manage.roles', 'group_name' => 'Access'],
        ];

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
