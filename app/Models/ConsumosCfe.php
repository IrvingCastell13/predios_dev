<?php

namespace App\Models;

class ConsumosCfe extends BaseModel
{
    protected $table = 'gd_consumos_cfe';

    protected $primaryKey = 'IDConsumoCFE';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDServicioPublico',
        'IDConceptoServicioPublico',
        'RPUConsumoCFE',
        'ALecturaConsumoCFE',
        'PeriodoLecturaConsumoCFE',
        'InicioPeriodoLecturaConsumoCFE',
        'FinPeriodoLecturaConsumoCFE',
        'LecturaAnteriorConsumoCFE',
        'LecturaActualConsumoCFE',
        'DemandaConsumoCFE',
        'ReactivoConsumoCFE',
        'FactorPotenciaConsumoCFE',
        'FactorCargaConsumoCFE',
        'ImporteEnergiaConsumoCFE',
        'ImporteDAPConsumoCFE',
        'ImporteIVAConsumoCFE',
        'ImporteTotalConsumoCFE',
        'TasaIVAConsumoCFE',
        'DocumentoIDConsumoCFE',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
