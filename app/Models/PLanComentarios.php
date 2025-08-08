<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PLanComentarios extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'IDComentarioAcccion';

    protected $table = 'plan_def_comentarios';

    protected $appends = ['fecha_hora'];

    protected $guarded = [];

    public function getFechaHoraAttribute()
    {
        $fecha = Carbon::parse($this->FechaComentarioAccion);

        return $fecha->format('d/m/Y - H:i');
    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
