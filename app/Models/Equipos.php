<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Illuminate\Support\Facades\Storage;

class Equipos extends BaseModel
{
    protected $table = 'in_equipos'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDEquipo'; // Ajusta según la clave primaria de tu tabla

    protected $appends = ['image', 'ubicacion', 'gerarquia'];

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->CodigoBarras = static::generateUniqueCode($model->IDCliente);
        });
    }

    protected static function generateUniqueCode($clienteId)
    {
        do {
            // Generar 8 dígitos aleatorios
            $randomNumbers = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Generar letras aleatorias (A-Z)
            $initialLetter = chr(mt_rand(65, 90)); // A-Z (65-90 en ASCII)
            $finalLetter = chr(mt_rand(65, 90));   // A-Z (65-90 en ASCII)

            $codigo = $initialLetter . $randomNumbers . $finalLetter;

            // Verificar que no exista para este cliente
            $exists = static::where('IDCliente', $clienteId)
                ->where('CodigoBarras', $codigo)
                ->exists();
        } while ($exists);

        return $codigo;
    }
    public function accionMantenimiento()
    {
        return $this->belongsTo(AccionesMantenimiento::class, 'IDEquipo', 'IDEquipo');
    }

    public function accionesMantenimiento()
    {
        return $this->hasMany(AccionesMantenimiento::class, 'IDEquipo', 'IDEquipo');
    }


    public function tipo()
    {
        return $this->belongsTo(TiposEquipo::class, 'IDTipoEquipo');
    }

    public function predio()
    {
        return $this->belongsTo(Predios::class, 'IDPredio');
    }

    public function edificio()
    {
        return $this->belongsTo(Edificios::class, 'IDEdificio');
    }

    public function nivel()
    {
        return $this->belongsTo(Niveles::class, 'IDNivel');
    }

    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'IDZona');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TiposDocumentos::class, 'IDTipoDocumento');
    }


    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDEquipo');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivos::class, 'IDImagenEquipo', 'IDArchivo');
    }

    public function subsistema()
    {
        return $this->belongsTo(Subsistema::class, 'IDSubsistema');
    }

    // public function tipoDocumento()
    // {
    //     return $this->belongsTo(iMA::class, 'IDImagenEquipo');
    // }
    public function getImageAttribute()
    {
        if (! $this->IDImagenEquipo) {
            return '/images/defaut_ticket.jpg';
        }
        $url = MetikHelper::getImageAwsS3($this->archivo);

        return $url;
    }

    public function getGerarquiaAttribute()
    {
        return collect([

            optional(optional($this->subsistema)->sistema)->NombreSistema,
            optional($this->subsistema)->NombreSubsistema,
        ])->filter()->implode(' - ');
    }



    public function getUbicacionAttribute()
    {
        return collect([
            optional($this->predio)->NombrePredio,
            optional($this->edificio)->NombreEdificio,
            optional($this->nivel)->NombreNivel,
            optional($this->zona)->NombreZona,
        ])->filter()->implode(' - ');
    }
}
