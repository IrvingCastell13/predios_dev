<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Niveles extends BaseModel
{
    protected $table = 'conf_niveles';

    protected $primaryKey = 'IDNivel';

    protected $appends = ['cantidad_zonas', 'estado', 'zonas_trazo', 'plano'];

    protected $guarded = [];

    public function edificio()
    {
        return $this->belongsTo(Edificios::class, 'IDEdificio');
    }

    public function datos_generales()
    {
        return $this->hasMany(DatosGenerales::class, 'IDNivel', 'IDNivel');
    }

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDNivel');
    }

    public function getCantidadZonasAttribute()
    {
        return Zonas::where('IDNivel', $this->IDNivel)->count();
    }

    public function zonas()
    {
        return $this->hasMany(Zonas::class, 'IDNivel', 'IDNivel');
    }

    public function getEstadoAttribute()
    {
        return $this->IDTrazo ? true : false;
    }

    public function getZonasTrazoAttribute()
    {

        $cantidadZonas = Zonas::where('IDNivel', $this->IDNivel)->whereNotNull('IDTrazo')->count();

        return $cantidadZonas;
    }

    public function getPlanoAttribute()
    {
        if (! $this->IDImagenPlanoNivel) {
            return null;
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->IDImagenPlanoNivel,
            now()->addMinutes(20)
        );

        return $url;
    }

    public function eliminarEnCascada()
    {
        foreach ($this->zonas as $zona) {
            $zona->Borrado = true;
            $zona->save();
        }

        $this->Borrado = true;
        $this->save();
    }
}
