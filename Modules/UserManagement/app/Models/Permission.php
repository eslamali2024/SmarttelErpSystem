<?php

namespace Modules\UserManagement\Models;

use App\Traits\ScopeFilter;
use Spatie\Permission\Models\Permission as PermissionModel;

class Permission extends PermissionModel
{
    use ScopeFilter;
}
