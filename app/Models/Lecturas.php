<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lecturas extends BaseModel
{
    use HasFactory;

    protected $table = 'plan_def_lecturas';

    protected $primaryKey = 'IDDefLecturas';

    protected $guarded = [];
}
