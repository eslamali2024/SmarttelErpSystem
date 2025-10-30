<?php

namespace Modules\UserManagement\Models;

use App\Traits\ScopeFilter;
use Spatie\Permission\Models\Role as RoleModel;

class Role extends RoleModel
{
    use ScopeFilter;
}
