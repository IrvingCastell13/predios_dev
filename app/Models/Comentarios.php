<?php

namespace App\Models;

class Comentarios extends BaseModel
{
    protected $table = 'tk_comentarios';

    protected $primaryKey = 'IDComentarioTicket';

    protected $guarded = [];

    protected $appends = ['fecha_hora'];

    public function getFechaHoraAttribute()
    {
        return $this->FechaActualizacionObjeto->format('d/m/Y - H:i');
    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
