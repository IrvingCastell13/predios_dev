<?php

namespace App\Models;

class Municipio extends BaseModel
{
    protected $table = 'conf_municipios';

    protected $primaryKey = 'IDMunicipio';

    protected $guarded = [];

    /**
     * Relación con el modelo Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'IDEstado', 'IDEstado');
    }
}
