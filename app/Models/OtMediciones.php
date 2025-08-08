<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtMediciones extends BaseModel
{
    use HasFactory;

    protected $table = 'ot_mediciones';

    protected $primaryKey = 'IDOtMediciones';

    protected $guarded = [];
}
