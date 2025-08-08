<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Documentos extends BaseModel
{
    protected $table = 'gd_documentos';

    protected $primaryKey = 'IDDocumento';

    protected static $usuarioAutenticado;

    protected $guard_name = 'api';

    protected $guarded = [];

    protected $appends = ['ubicacion', 'follow', 'follow_app', 'image', 'gerarquia', 'extension'];

    protected static function booted()
    {
        static::$usuarioAutenticado = Auth::guard('api')->user();
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

    public function accionesRenovacion()
    {
        return $this->hasMany(PlanAccionRenovacion::class, 'IDDocumento', 'IDDocumento');
    }

    public function accionRenovacion()
    {
        return $this->belongsTo(PlanAccionRenovacion::class, 'IDDocumento', 'IDDocumento');
    }

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDDocumento', 'IDInstancia');
    }

    public function metadatos()
    {
        return $this->hasMany(DatosDocumentos::class, 'IDDocumento');
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'IDObjeto', 'IDDocumento');
    }

    public function archivos($tipo = 'ultimo')
    {
        if ($tipo === 'ultimo') {
            return Archivos::where('IDObjetoPadreArchivo', $this->IDDocumento)
                ->orderBy('NumArchivo', 'desc')
                ->first();
        } else { // {{ edit_3 }}
            return Archivos::where('IDObjetoPadreArchivo', $this->IDDocumento)->get();
        }
    }

    public function archivosCargados()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDDocumento')
            ->orderBy('fechaCreacionObjeto', 'desc');
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

    public function getGerarquiaAttribute()
    {
        return collect([
            optional(optional(optional($this->tipoDocumento)->categoria)->grupo)->NombreGrupoDoc,
            optional(optional($this->tipoDocumento)->categoria)->NombreCategoriaDoc,
            optional($this->tipoDocumento)->NombreTipoDocumento,
        ])->filter()->implode(' - ');
    }

    public function getImageAttribute()
    {

        // /images/upload-img-1.jpg
        $archivos = Archivos::select('NombreOriginalArchivo', 'ExtensionArchivo', 's3')->where('IDObjetoPadreArchivo', $this->IDDocumento)->get();

        if (count($archivos) == 0) {
            return url('/images/defaut_ticket.jpg');
        } else {

            $url = MetikHelper::getImageAwsS3($archivos[0]);

            return $url;
        }
    }

    public function getExtensionAttribute()
    {

        // /images/upload-img-1.jpg
        $archivos = Archivos::select('NombreOriginalArchivo', 'ExtensionArchivo', 's3')->where('IDObjetoPadreArchivo', $this->IDDocumento)->orderby('fechaCreacionObjeto', 'DESC')->get();

        if (count($archivos) == 0) {
            return null;
        } else {
            return $archivos[0]->ExtensionArchivo;
        }
    }

    public function getFollowAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDDocumento],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : false;
    }

    public function getFollowAppAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDDocumento],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : null;
    }
}
