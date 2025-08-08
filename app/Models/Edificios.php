<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Edificios extends BaseModel
{
    protected $appends = ['cantidad_niveles', 'cantidad_zonas', 'estado', 'niveles_trazo', 'zonas_trazo', 'plano'];

    protected $table = 'conf_edificios';

    protected $primaryKey = 'IDEdificio';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    public function getCantidadNivelesAttribute()
    {
        return Niveles::where('IDEdificio', $this->IDEdificio)->count();
    }

    public function getCantidadZonasAttribute()
    {

        $niveles = Niveles::where('IDEdificio', $this->IDEdificio)->pluck('IDNivel');

        $cantidadZonas = Zonas::whereIn('IDNivel', $niveles)->count();

        return $cantidadZonas;
    }

    public function getNivelesTrazoAttribute()
    {
        return Niveles::where('IDEdificio', $this->IDEdificio)->whereNotNull('IDTrazo')->count();
    }

    public function datos_generales()
    {
        return $this->hasMany(DatosGenerales::class, 'IDEdificio', 'IDEdificio');
    }

    public function getZonasTrazoAttribute()
    {
        $niveles = Niveles::where('IDEdificio', $this->IDEdificio)->pluck('IDNivel');
        $cantidadZonas = Zonas::whereIn('IDNivel', $niveles)->whereNotNull('IDTrazo')->count();

        return $cantidadZonas;
    }

    public function getEstadoAttribute()
    {

        return $this->IDTrazo ? true : false;

    }

    public function niveles()
    {
        return $this->hasMany(Niveles::class, 'IDEdificio', 'IDEdificio');
    }

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDEdificio');
    }

    public function predio()
    {
        return $this->belongsTo(Predios::class, 'IDPredio', 'IDPredio');
    }

    public function eliminarEnCascada()
    {
        foreach ($this->niveles as $niv) {
            $niv->eliminarEnCascada();
        }

        $this->Borrado = true;
        $this->save();
    }

    public function getPlanoAttribute()
    {
        if (! $this->IDImagenEdificio) {
            return null;
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->IDImagenEdificio,
            now()->addMinutes(20)
        );

        return $url;
    }
}
