<?php

namespace App\Models;

class DatosDocumentos extends BaseModel
{
    protected $table = 'gd_datos_documento';

    protected $primaryKey = 'IDDatoDocumento';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'IDDocumento',
        'MetadatoTipoDocumentoID',
        'ValorDatoDocumento',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
    ];

    public function metadatoTipoDocumento()
    {
        return $this->belongsTo(MetadatosTipoDocumento::class, 'MetadatoTipoDocumentoID');
    }
}
