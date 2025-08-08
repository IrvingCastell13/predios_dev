<?php

namespace App\Models;

class TrackFlujos extends BaseModel
{
    protected $table = 'track_flujos';

    protected $primaryKey = 'IDFlujo';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ClaveFlujo',
        'NombreFlujo',
        'DescripcionFlujo',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
