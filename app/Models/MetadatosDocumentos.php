<?php

namespace App\Models;

class MetadatosDocumentos extends BaseModel
{
    protected $table = 'gd_metadatos_tipodocumento';

    protected $primaryKey = 'IDMetadatoTipoDocumento';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDTipoDocumento',
        'ClaveMetadatoTipoDocumento',
        'NombreMetadatoTipoDocumento',
        'DescripcionMetadatoTipoDocumento',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];
}
