<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObligatoriosTipoinmueble extends BaseModel
{
    use HasFactory;

    protected $table = 'gd_obligatorios_tipo_inmueble';

    protected $primaryKey = 'IDTipoDocumento';

    protected $guarded = [];
}
