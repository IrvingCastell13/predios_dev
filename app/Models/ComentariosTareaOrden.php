<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComentariosTareaOrden extends BaseModel
{
    use HasFactory;

    protected $table = 'ot_comentarios_tareas';

    protected $primaryKey = 'IDComentarioOTTarea';

    protected $guarded = [];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
