<?php

namespace App\Models;

class AccionValores extends BaseModel
{
    protected $table = 'plan_def_accion_valores'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDValorAccion';

    protected $guarded = [];
}
