<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefRutinas extends BaseModel
{
    use HasFactory;

    protected $table = 'rut_def_rutinas';

    protected $primaryKey = 'IDDefinicionRutina';

    protected $guarded = [];

    public function rutina_zona()
    {
        return $this->hasMany(RutinasZonas::class, 'IDDefinicionRutina', 'IDDefinicionRutina');
    }

    public function rutina_equipos()
    {
        return $this->hasMany(RutinasEquipos::class, 'IDDefinicionRutina', 'IDDefinicionRutina');
    }

    public function tareas()
    {
        return $this->hasMany(PlanDefAccionTarea::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }

    public function requisitos()
    {
        return $this->hasMany(PlanDefAccionRequisitos::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }

    public function mediciones()
    {
        return $this->hasMany(Lecturas::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }
}
