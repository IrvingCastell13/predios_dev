<?php

namespace App\Models;

class PlanDefAccionTarea extends BaseModel
{
    protected $table = 'plan_def_accion_tareas'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDTareaAccion'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];
}
