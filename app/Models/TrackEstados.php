<?php

namespace App\Models;

class TrackEstados extends BaseModel
{
    protected $table = 'track_estados';

    protected $primaryKey = 'IDEstado';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDFlujo',
        'ClaveEstado',
        'NombreEstado',
        'DescripcionEstado',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
