<?php

namespace App\Models;

class PlanTipoAccion extends BaseModel
{
    protected $table = 'plan_tipos_accion';

    protected $primaryKey = 'IDTipoAccion';

    public $incrementing = false;

    protected $guarded = [];
}
