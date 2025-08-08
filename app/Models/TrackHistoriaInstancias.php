<?php

namespace App\Models;

use Carbon\Carbon;

class TrackHistoriaInstancias extends BaseModel
{
    protected $table = 'track_historia_instancias';

    protected $primaryKey = 'IDHistoriaInstancia';

    public $incrementing = false;

    public $timestamps = false;



    const EVENTO_RECHAZO_ID = '002398bd-ee39-11ef-a708-00090ffe0001';

    protected $fillable = [
        'IDInstancia',
        'IDEstadoAnteriorHistoria',
        'IDEstadoSiguienteHistoria',
        'IDEventoHistoria',
        'FechaEventoAnteriorHistoria',
        'FechaEventoHistoria',
        'IDPersonaTransitoHistoria',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];

    protected $appends = ['persona_historial', 'estado', 'evento'];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersonaTransitoHistoria', 'IDPersona');
    }

    public function estado_siguente()
    {
        return $this->belongsTo(TrackEstados::class, 'IDEstadoSiguienteHistoria', 'IDEstado');
    }

    public function getPersonaHistorialAttribute()
    {
        $fecha = Carbon::parse($this->FechaEventoHistoria)->format('d/m/Y h:i');

        $nombre = $this->persona ? $this->persona->full_name : '';
        return "{$nombre} - {$fecha}";
    }

    public function getEstadoAttribute()
    {

        return "Cambio el ticket a: {$this->estado_siguente->NombreEstado}";
    }

    public function getEventoAttribute()
    {
        if ($this->IDEventoHistoria) {
            $evento = TrackEventos::find($this->IDEventoHistoria);
            return $evento ? $evento->NombreEvento : 'Evento no encontrado';
        }
        return 'No hay evento asociado';
    }
}
