<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetadatosTipoDocumento extends BaseModel
{
    use HasFactory;

    protected $table = 'gd_metadatos_tipodocumento';

    protected $primaryKey = 'IDMetadatoTipoDocumento';

    protected $guarded = [];
}
