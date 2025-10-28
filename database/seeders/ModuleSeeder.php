<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds/
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('modules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($this->getAllModules() as $module) {
            Module::create($module);
        }
    }

    /**
     * Get all modules to seed/
     *
     * @return array
     */
    private function getAllModules(): array
    {
        return array_merge(
            $this->getMainModules(),
            $this->getHRModules(),

        );
    }

    private function getMainModules(): array
    {
        return [
            [
                'name'              => 'Dashboard',
                'icon'              => 'ri-dashboard-line',
                'path'              => 'dashboard',
                'permission_title'  => 'dashboard_access',
                'status'            => true,
            ],
            [
                'key'               => 'hr',
                'name'              => 'HR',
                'icon'              => 'ri-shield-user-fill',
                'path'              => 'hr',
                'permission_title'  => 'hr_access',
                'status'            => true,
            ],
            [
                'key'               => 'crm',
                'name'              => 'CRM',
                'icon'              => 'ri-customer-service-line',
                'path'              => 'crm',
                'permission_title'  => 'crm_access',
                'status'            => true,
            ],
            [
                'key'               => 'sales',
                'name'              => 'Sales',
                'icon'              => 'ri-phone-line',
                'path'              => 'sales',
                'permission_title'  => 'sales_access',
                'status'            => true,
            ],
            [
                'key'               => 'pre-sales',
                'name'              => 'Pre Sales',
                'icon'              => 'ri-phone-line',
                'path'              => 'pre-sales',
                'permission_title'  => 'pre_sales_access',
                'status'            => true,
            ],
            [
                'key'               => 'supply-chain',
                'name'              => 'Supply Chain',
                'icon'              => 'ri-user-fill',
                'path'              => 'supply-chain',
                'permission_title'  => 'supply_chain_access',
                'status'            => true,
            ],

        ];
    }
    private function getHRModules(): array
    {
        return [
            [
                'key'               => 'organization',
                'name'              => 'Organization Management',
                'icon'              => 'ri-building-2-fill',
                'path'              => 'hr/organization',
                'permission_title'  => 'organization_access',
                'status'            => true,
                'parent_key'        => 'hr',
            ],
            [
                'name'              => 'department',
                'icon'              => 'ri-building-2-fill',
                'path'              => 'hr/organization/departments',
                'permission_title'  => 'department_access',
                'status'            => true,
                'parent_key'        => 'organization',
            ],
        ];
    }
}
