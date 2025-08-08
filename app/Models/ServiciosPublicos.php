<?php

namespace App\Models;

class ServiciosPublicos extends BaseModel
{
    protected $table = 'gd_servicios_publicos';

    protected $primaryKey = 'IDServicioPublico';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDTipoServicioPublico',
        'PredioID',
        'IDEdificio',
        'IDNivel',
        'IDZona',
        'CuentaServicioPublico',
        'IDProveedor',
        'IDTipoDocumento',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
