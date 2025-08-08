<?php

namespace App\Models;

class CostosOrden extends BaseModel
{
    protected $table = 'ot_costos_orden_trabajo';

    protected $primaryKey = 'IDCostoOT';

    protected $guarded = [];

    public function unidad_medida()
    {
        return $this->belongsTo(UnidadMedida::class, 'UnidadCostoOT', 'IDUnidadMedida');
    }
}
