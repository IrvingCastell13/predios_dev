<?php

namespace App\Models;

class Renovacion extends BaseModel
{
    protected $table = 'plan_def_renovacion'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDDefRenovacion'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    public function tipoDocumento()
    {
        return $this->belongsTo(TiposDocumentos::class, 'IDTipoDocumento', 'IDTipoDocumento');
    }
}
