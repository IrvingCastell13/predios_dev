<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contratos extends BaseModel
{
    use HasFactory;

    protected $table = 'ga_contratos';

    protected $primaryKey = 'IDContrato';

    protected $guarded = [];
}
