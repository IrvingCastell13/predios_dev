<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRutina extends Model
{
    use HasFactory;

    protected $table = 'rut_tipos_rutina';

    protected $primaryKey = 'IDTipoRutina';

    protected $guarded = [];
}
