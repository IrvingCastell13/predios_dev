<?php

namespace App\Models;

class TipoEquipos extends BaseModel
{
    protected $table = 'in_tipos_equipo';

    protected $primaryKey = 'IDTipoEquipo';

    protected $guarded = [];

    public function archivo()
    {
        return $this->belongsTo(Archivos::class, 'ImagenTipoEquipo', 'IDArchivo');
        // return $this->hasOne(Archivos::class, 'IDObjeto', 'IDTipoEquipo');
    }

    public function fabricante()
    {
        return $this->belongsTo(Fabricantes::class, 'IDFabricante', 'IDFabricante');
    }
}
