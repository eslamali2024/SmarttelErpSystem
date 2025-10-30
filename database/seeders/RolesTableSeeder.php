<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /* @var array */
    private $roles = [
        'super-admin',
        'admin',
        'hr-manager',
        'project-manager',
        'it-manager',
        'sales-manager',
        'finance-manager',
        'supply-chain-manager',
        'warehouse-manager',
        'sales-representative',
        'hr-staff',
        'warehouse-staff',
        'treasury-staff',
        'accountant',
        'bank-staff',
        'customer-service',
        'driver',
        'cleaner',
        'buffet-staff',
        'receptionist',
        'security',
        'maintenance-staff',
        'employee',
        'collaborate'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed  roles
        foreach ($this->roles as $role) {
            Role::create([
                'name'          => $role,
                'guard_name'    => 'web'
            ]);
        }
    }
}
