<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtRequisitos extends BaseModel
{
    use HasFactory;

    protected $table = 'ot_requisitos';

    protected $primaryKey = 'IDOtRequisitos';

    protected $guarded = [];
}
