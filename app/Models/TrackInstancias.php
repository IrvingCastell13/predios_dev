<?php

namespace App\Models;

class TrackInstancias extends BaseModel
{
    protected $table = 'track_instancias';

    protected $primaryKey = 'IDInstancia';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDInstancia',
        'IDFlujo',
        'IDEstadoActualInstancia',
        'IDEstadoAnteriorInstancia',
        'IDUltimoEventoInstancia',
        'FechaUltimoEventoInstancia',
        'IDPersonaTransitoInstancia',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];

    public function estadoActual()
    {
        return $this->belongsTo(TrackEstados::class, 'IDEstadoActualInstancia');
    }

    public function historial()
    {
        return $this->hasMany(TrackHistoriaInstancias::class, 'IDInstancia')->orderBy('FechaCreacionObjeto', 'desc');
    }
}
