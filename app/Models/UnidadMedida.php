<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'ot_unidad_medida';

    protected $primaryKey = 'IDUnidadMedida';

    protected $casts = [
        'IDUnidadMedida' => 'string',
    ];

    protected $guarded = [];
}
