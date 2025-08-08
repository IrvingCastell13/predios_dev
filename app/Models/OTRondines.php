<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OTRondines extends BaseModel
{
    use HasFactory;

    protected $table = 'ot_rondin';

    protected $primaryKey = 'IDOTRondin';

    protected $guarded = [];

    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'IDZona', 'IDZona');
    }
}
