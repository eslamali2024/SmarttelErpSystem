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
                ]
            ],
            'Employees' => [
                'Create'                    => 'employee_create',
                'Edit'                      => 'employee_edit',
                'Show'                      => 'employee_show',
                'Delete'                    => 'employee_delete',
                'List'                      => 'employee_access',
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
