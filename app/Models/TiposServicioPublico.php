<?php

namespace App\Models;

class TiposServicioPublico extends BaseModel
{
    protected $table = 'gd_tipos_serviciopublico';

    protected $primaryKey = 'IDTipoServicioPublico';

    protected $guarded = [];

    public function tipoDocumento()
    {
        return $this->belongsTo(TiposDocumentos::class, 'IDTipoDocumento');
    }
}
