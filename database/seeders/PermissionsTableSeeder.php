<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Seed  roles
        $permissions = [
            ...$this->getHRModules(),
            ...$this->getUserManagementModules()
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name'          => $permission,
                'guard_name'    => 'web'
            ]);
        }
    }

    private function getHRModules(): array
    {
        return [
            // Modules
            'dashboard_access',
            'hr_access',
            'usermanagement_access',
            'crm_access',
            'sales_access',
            'pre_sales_access',
            'supply_chain_access',

            // Organization
            'organization_access',

            // Division
            'division_access',
            'division_create',
            'division_edit',
            'division_delete',
            'division_show',

            // Department
            'department_access',
            'department_create',
            'department_edit',
            'department_delete',
            'department_show',

            // Section
            'section_access',
            'section_create',
            'section_edit',
            'section_delete',
            'section_show',

            // Position
            'position_access',
            'position_create',
            'position_edit',
            'position_delete',
            'position_show',

            // Employees
            'employee_access',
            'employee_create',
            'employee_edit',
            'employee_delete',
            'employee_show',
        ];
    }
    private function getUserManagementModules(): array
    {
        return [
            // Roles
            'role_access',
            'role_create',
            'role_edit',
            'role_delete',
            'role_show',

            // Permissions
            'permission_access',
            'permission_create',
            'permission_edit',
            'permission_delete',
            'permission_show',

            // Users
            'user_access',
            'user_create',
            'user_edit',
            'user_delete',
            'user_show',
        ];
    }
}
