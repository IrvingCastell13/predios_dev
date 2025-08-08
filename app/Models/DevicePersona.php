<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevicePersona extends BaseModel
{
    use HasFactory;

    protected $table = 'ms_personas_devices';

    protected $primaryKey = 'IDPersonaDevice';

    protected $guarded = [];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona');
    }
}
