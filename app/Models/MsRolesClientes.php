<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class MsRolesClientes extends BaseModel
{
    use HasFactory, HasPermissions, HasRoles;

    protected $guard_name = 'api';

    protected $table = 'ms_roles_con_clientes';

    protected $primaryKey = 'IDRolesCliente';

    protected $guarded = [];
}
