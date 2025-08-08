<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RutinasEquipos extends BaseModel
{
    use HasFactory;

    protected $table = 'rut_rutinas_equipos';

    protected $primaryKey = 'IDDefinicionRutina';

    protected $guarded = [];

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'IDEquipo', 'IDEquipo');
    }
}
