<?php

namespace App\Models;

class ConceptosServiciosPublicos extends BaseModel
{
    protected $table = 'gd_conceptos_serviciopublico';

    protected $primaryKey = 'IDConceptoServicioPublico';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDServicioPublico',
        'IDTipoConceptoServicio',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
        'EsPublico',
    ];
}
