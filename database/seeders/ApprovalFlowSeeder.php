<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\Approval\ApprovalTypeEnum;
use App\Models\Approval\ApprovalFlow;

class ApprovalFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles          = Role::all();

        foreach ($this->models() as $key => $flow) {

            $steps        = $this->steps($roles)[$key] ?? [];
            $approvalFlow = ApprovalFlow::updateOrCreate([
                'approvable_type' => $flow['approvable_type']
            ], $flow);

            foreach ($steps as $step) {
                $approvalFlow->approvalFlowSteps()->updateOrCreate([
                    'approver_type' => $step['approver_type'],
                    'order'         => $step['order'],
                ], $step);
            }
        }
    }

    private function models()
    {
        return [
            // \Modules\Hr\Models\Bonus::class =>    [
            //     'name'                    => 'Bonus Approval',
            //     'approvable_type'         => \Modules\Hr\Models\Bonus::class,
            //     'redirect_route'          => 'hr.bonuses.show',
            // ],
        ];
    }

    private function steps($roles)
    {
        return [
            // \Modules\Hr\Models\EmployeeErrand::class =>    [
            //     [
            //         'name'           => "Section Manager Approval",
            //         'manager_column' => 'manager_id',
            //         'approver_type'  => ApprovalTypeEnum::SECTION->value,
            //         'order'          => 1
            //     ],
            //     [
            //         'name'           => "Department Manager Approval",
            //         'manager_column' => 'manager_id',
            //         'approver_type'  => ApprovalTypeEnum::DEPARTMENT->value,
            //         'order'          => 2
            //     ],
            //     [
            //         'name'          => "HR Manager Approval",
            //         'roles'         => $roles->whereIn('name', ['hr-manager', 'hr-staff'])->pluck('id')->toArray(),
            //         'approver_type' => ApprovalTypeEnum::ROLE->value,
            //         'order'         => 3
            //     ],
            // ],

        ];
    }
}
