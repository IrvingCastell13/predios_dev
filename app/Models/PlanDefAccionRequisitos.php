<?php

namespace App\Models;

class PlanDefAccionRequisitos extends BaseModel
{
    protected $table = 'plan_def_accion_requisitos';

    protected $primaryKey = 'IDRequisitoAccion';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];
}
