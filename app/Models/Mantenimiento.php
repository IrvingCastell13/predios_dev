<?php

namespace App\Models;

class Mantenimiento extends BaseModel
{
    protected $table = 'plan_def_mantenimiento'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDDefMant'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipos::class, 'IDTipoEquipo', 'IDTipoEquipo');
    }
}
