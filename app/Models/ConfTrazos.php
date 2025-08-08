<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfTrazos extends BaseModel
{
    use HasFactory;

    protected $table = 'conf_trazos';

    protected $primaryKey = 'IDTrazo';

    protected $guarded = [];
}
