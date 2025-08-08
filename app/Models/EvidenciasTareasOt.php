<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvidenciasTareasOt extends BaseModel
{
    use HasFactory;

    protected $table = 'ot_evidencias_tareas';

    protected $primaryKey = 'IDTareaOT';

    protected $guarded = [];

    public function archivo()
    {
        return $this->belongsTo(Archivos::class, 'IDArchivo', 'IDArchivo');
    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
