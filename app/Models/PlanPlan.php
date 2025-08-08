<?php

namespace App\Models;

class PlanPlan extends BaseModel
{
    protected $table = 'plan_planes'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDPlan'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    public function tipo()
    {
        return $this->belongsTo(TiposPlan::class, 'IDTipoPlan');
    }

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDPlan', 'IDInstancia');
    }
}
