<?php

namespace App\Models;

class DatosGenerales extends BaseModel
{
    protected $table = 'conf_datos_generales';

    protected $primaryKey = 'IDDatoGeneral';

    protected $guarded = [];

    public function metadato()
    {
        return $this->belongsTo(MetadatosGenerales::class, 'IDMetadatoGeneral', 'IDMetadatoGeneral');
    }
}
