<?php

namespace App\Models;

class TareasOrden extends BaseModel
{
    protected $table = 'ot_tareas_orden';

    protected $primaryKey = 'IDTareaOT';

    protected $guarded = [];

    protected $appends = ['fecha', 'hora'];

    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDTareaOT');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciasTareasOt::class, 'IDTareaOT', 'IDTareaOT');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentariosTareaOrden::class, 'IDOTTarea', 'IDTareaOT');
    }

    public function getFechaAttribute()
    {
        return $this->FechaActualizacionObjeto->format('d/m-Y');
    }

    public function getHoraAttribute()
    {
        return $this->FechaActualizacionObjeto->format('H:i');
    }
}
