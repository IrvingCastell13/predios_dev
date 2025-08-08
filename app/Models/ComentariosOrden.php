<?php

namespace App\Models;

class ComentariosOrden extends BaseModel
{
    protected $table = 'ot_comentarios_orden_trabajo';

    protected $primaryKey = 'IDComentarioOT';

    protected $guarded = [];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
