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
            Permission::updateOrCreate(
                [
                    'name' => $permission
                ],
                [
                    'name'          => $permission,
                    'guard_name'    => 'web'
                ]
            );
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

            // Master Data
            'hr_master_access',

            // Time Managements
            'time_management_access',
            'time_management_create',
            'time_management_edit',
            'time_management_delete',
            'time_management_show',

            // Work Schedules
            'work_schedule_access',
            'work_schedule_create',
            'work_schedule_edit',
            'work_schedule_delete',
            'work_schedule_show',

            // Work Schedule Rules
            'work_schedule_rule_access',
            'work_schedule_rule_create',
            'work_schedule_rule_edit',
            'work_schedule_rule_delete',
            'work_schedule_rule_show',

            // Allowance Types
            'allowance_type_access',
            'allowance_type_create',
            'allowance_type_edit',
            'allowance_type_delete',
            'allowance_type_show',

            // Insurance Companies
            'insurance_company_access',
            'insurance_company_create',
            'insurance_company_edit',
            'insurance_company_delete',
            'insurance_company_show',

            // Public Holidays
            'public_holiday_access',
            'public_holiday_create',
            'public_holiday_edit',
            'public_holiday_delete',
            'public_holiday_show',

            // Bonus Types
            'bonus_type_access',
            'bonus_type_create',
            'bonus_type_edit',
            'bonus_type_delete',
            'bonus_type_show',

            // Deduction Types
            'deduction_type_access',
            'deduction_type_create',
            'deduction_type_edit',
            'deduction_type_delete',
            'deduction_type_show',

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

            // Approval flow
            'approval_flow_access',
            'approval_flow_create',
            'approval_flow_edit',
            'approval_flow_delete',
            'approval_flow_show',

            // Approval flow Step
            'approval_flow_step_access',
            'approval_flow_step_create',
            'approval_flow_step_edit',
            'approval_flow_step_delete',
            'approval_flow_step_show',
        ];
    }
}
