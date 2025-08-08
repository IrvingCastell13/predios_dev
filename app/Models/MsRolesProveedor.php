<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class MsRolesProveedor extends BaseModel
{
    use HasFactory, HasPermissions, HasRoles;

    protected $guard_name = 'api';

    protected $table = 'ms_roles_proveedor';

    protected $primaryKey = 'IDRolProveedor';

    protected $guarded = [];
}
