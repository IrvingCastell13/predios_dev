<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class MsRolesPredios extends BaseModel
{
    use HasFactory, HasPermissions, HasRoles;

    protected $guard_name = 'api';

    protected $table = 'ms_roles_en_predios';

    protected $primaryKey = 'IDRolesPredio';

    protected $guarded = [];

    public function usuario()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
