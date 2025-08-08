<?php

namespace App\Models;

class TiposPlan extends BaseModel
{
    protected $table = 'plan_tipos_plan'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDTipoPlan'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];
}
