<?php

namespace App\Models;

class Zonas extends BaseModel
{
    protected $table = 'conf_zonas';

    protected $primaryKey = 'IDZona';

    protected $appends = ['estado', 'predio'];

    protected $guarded = [];

    public function nivel()
    {
        return $this->belongsTo(Niveles::class, 'IDNivel');
    }

    public function datos_generales()
    {
        return $this->hasMany(DatosGenerales::class, 'IDZona', 'IDZona');
    }

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDZona');
    }

    public function tipo()
    {
        return $this->belongsTo(TiposInmuebles::class, 'IDTipoInmueble', 'IDTipoZona');
    }

    public function tipo_zona()
    {

        return $this->belongsTo(TiposInmuebles::class, 'IDTipoZona', 'IDTipoInmueble');
    }

    public function getEstadoAttribute()
    {
        return $this->IDTrazo ? true : false;
    }

    public function getPredioAttribute()
    {
        return $this->nivel->edificio->predio->IDPredio ?? null;
    }
}
