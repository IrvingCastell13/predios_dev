<?php

namespace App\Models;

class TrackEventos extends BaseModel
{
    protected $table = 'track_eventos';

    protected $primaryKey = 'IDEvento';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ClaveEvento',
        'NombreEvento',
        'DescripcionEvento',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
