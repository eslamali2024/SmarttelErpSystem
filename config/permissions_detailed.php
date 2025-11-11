<?php

return [
    'Dashboard' => [
        'id'     => 'dashboard',
        'access' => 'dashboard_access',
        'title'  => 'Dashboard',
    ],

    'Users Management' => [
        'id'               => 'usermanagement',
        'access'           => 'usermanagement_access',
        'title'            => 'User Management',
        'children'      => [
            'Permissions' => [
                'Create'                    => 'permission_create',
                'Edit'                      => 'permission_edit',
                'Show'                      => 'permission_show',
                'Delete'                    => 'permission_delete',
                'List'                      => 'permission_access',
            ],
            'Roles' => [
                'Create'                    => 'role_create',
                'Edit'                      => 'role_edit',
                'Show'                      => 'role_show',
                'Delete'                    => 'role_delete',
                'List'                      => 'role_access',
            ],
            'Users' => [
                'Create'                    => 'user_create',
                'Edit'                      => 'user_edit',
                'Show'                      => 'user_show',
                'Delete'                    => 'user_delete',
                'List'                      => 'user_access',
            ],
            'Approval Flow' => [
                'Create'                    => 'approval_flow_create',
                'Edit'                      => 'approval_flow_edit',
                'Show'                      => 'approval_flow_show',
                'Delete'                    => 'approval_flow_delete',
                'List'                      => 'approval_flow_access',
            ],
            'Approval Flow Step' => [
                'Create'                    => 'approval_flow_step_create',
                'Edit'                      => 'approval_flow_step_edit',
                'Show'                      => 'approval_flow_step_show',
                'Delete'                    => 'approval_flow_step_delete',
                'List'                      => 'approval_flow_step_access',
            ],
        ]
    ],

    'HR' => [
        'id'            => 'hr',
        'access'        => 'hr_access',
        'title'         => 'HR',
        'children'      => [
            'Organization Management' => [
                'id'                        => 'organization-management',
                'title'                     => 'Organization Management',
                'access'                    => 'organization_access',

                'children'                  => [
                    'Divisions' => [
                        'Create'                    => 'division_create',
                        'Edit'                      => 'division_edit',
                        'Show'                      => 'division_show',
                        'Delete'                    => 'division_delete',
                        'List'                      => 'division_access',
                    ],
                    'Departments' => [
                        'Create'                    => 'department_create',
                        'Edit'                      => 'department_edit',
                        'Show'                      => 'department_show',
                        'Delete'                    => 'department_delete',
                        'List'                      => 'department_access',
                    ],
                    'Sections' => [
                        'Create'                    => 'section_create',
                        'Edit'                      => 'section_edit',
                        'Show'                      => 'section_show',
                        'Delete'                    => 'section_delete',
                        'List'                      => 'section_access',
                    ],
                    'Positions' => [
                        'Create'                    => 'position_create',
                        'Edit'                      => 'position_edit',
                        'Show'                      => 'position_show',
                        'Delete'                    => 'position_delete',
                        'List'                      => 'position_access',
                    ],
                ]
            ],
            'Master Data' => [
                'id'                        => 'master-data',
                'title'                     => 'Master Data',
                'access'                    => 'hr_master_access',

                'children'                  => [
                    'Time Managements' => [
                        'Create'                    => 'time_management_create',
                        'Edit'                      => 'time_management_edit',
                        'Show'                      => 'time_management_show',
                        'Delete'                    => 'time_management_delete',
                        'List'                      => 'time_management_access',
                    ],
                    'Work Schedules' => [
                        'Create'                    => 'work_schedule_create',
                        'Edit'                      => 'work_schedule_edit',
                        'Show'                      => 'work_schedule_show',
                        'Delete'                    => 'work_schedule_delete',
                        'List'                      => 'work_schedule_access',
                    ],
                    'Work Schedule Rules' => [
                        'Create'                    => 'work_schedule_rule_create',
                        'Edit'                      => 'work_schedule_rule_edit',
                        'Show'                      => 'work_schedule_rule_show',
                        'Delete'                    => 'work_schedule_rule_delete',
                        'List'                      => 'work_schedule_rule_access',
                    ],
                    'Allowance Types' => [
                        'Create'                    => 'allowance_type_create',
                        'Edit'                      => 'allowance_type_edit',
                        'Show'                      => 'allowance_type_show',
                        'Delete'                    => 'allowance_type_delete',
                        'List'                      => 'allowance_type_access',
                    ],
                    'Insurance Companies' => [
                        'Create'                    => 'insurance_company_create',
                        'Edit'                      => 'insurance_company_edit',
                        'Show'                      => 'insurance_company_show',
                        'Delete'                    => 'insurance_company_delete',
                        'List'                      => 'insurance_company_access',
                    ],
                    'Public Holidays' => [
                        'Create'                    => 'public_holiday_create',
                        'Edit'                      => 'public_holiday_edit',
                        'Show'                      => 'public_holiday_show',
                        'Delete'                    => 'public_holiday_delete',
                        'List'                      => 'public_holiday_access',
                    ],
                    'Bonus Types' => [
                        'Create'                    => 'bonus_type_create',
                        'Edit'                      => 'bonus_type_edit',
                        'Show'                      => 'bonus_type_show',
                        'Delete'                    => 'bonus_type_delete',
                        'List'                      => 'bonus_type_access',
                    ],
                    'Deduction Types' => [
                        'Create'                    => 'deduction_type_create',
                        'Edit'                      => 'deduction_type_edit',
                        'Show'                      => 'deduction_type_show',
                        'Delete'                    => 'deduction_type_delete',
                        'List'                      => 'deduction_type_access',
                    ],
                    'Leave Types' => [
                        'Create'                    => 'leave_type_create',
                        'Edit'                      => 'leave_type_edit',
                        'Show'                      => 'leave_type_show',
                        'Delete'                    => 'leave_type_delete',
                        'List'                      => 'leave_type_access',
                    ],
                ]
            ],
            'Employees' => [
                'Create'                    => 'employee_create',
                'Edit'                      => 'employee_edit',
                'Show'                      => 'employee_show',
                'Delete'                    => 'employee_delete',
                'List'                      => 'employee_access',
            ],
            'Employee Contracts' => [
                'Create'                    => 'employee_contract_create',
                'Edit'                      => 'employee_contract_edit',
                'Show'                      => 'employee_contract_show',
                'Delete'                    => 'employee_contract_delete',
                'List'                      => 'employee_contract_access',
            ],
            'Bonuses' => [
                'Create'                    => 'bonuse_create',
                'Edit'                      => 'bonuse_edit',
                'Show'                      => 'bonuse_show',
                'Delete'                    => 'bonuse_delete',
                'List'                      => 'bonuse_access',
            ],
            'Deductions' => [
                'Create'                    => 'deduction_create',
                'Edit'                      => 'deduction_edit',
                'Show'                      => 'deduction_show',
                'Delete'                    => 'deduction_delete',
                'List'                      => 'deduction_access',
            ]
        ]
    ],

    // 'CRM' => [
    //     'id'               => 'crm',
    //     'access'           => 'crm_access',
    //     'title'            => 'CRM',
    //     'children'      => []
    // ],

    // 'Supply Chain' => [
    //     'id'               => 'supply_chain',
    //     'access'           => 'supply_chain_access',
    //     'title'            => 'Supply Chain',
    //     'children'      => []
    // ],

];
