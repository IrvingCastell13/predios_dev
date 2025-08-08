<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RutinasZonas extends BaseModel
{
    use HasFactory;

    protected $table = 'rut_rutinas_zonas';

    protected $primaryKey = 'IDDefinicionRutina';

    protected $guarded = [];

    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'IDZona', 'IDZona');
    }
}
