<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanAccionRoles extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'IDRolAcccion';

    protected $table = 'plan_def_accion_roles';

    protected $guarded = [];
}
