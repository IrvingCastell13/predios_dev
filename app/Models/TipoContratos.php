<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoContratos extends BaseModel
{
    use HasFactory;

    protected $table = 'ga_tipos_contrato';

    protected $primaryKey = 'IDTipoContrato';

    protected $guarded = [];
}
