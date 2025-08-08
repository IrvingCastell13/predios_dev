<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Predios extends BaseModel
{
    protected static $usuarioAutenticado;

    protected $table = 'conf_predios';

    protected $primaryKey = 'IDPredio';

    protected $guarded = [];

    protected $appends = ['usuarios', 'equipos', 'lat', 'lng', 'image', 'plano', 'editar_predio', 'puede_crear_edificio', 'puede_crear_nivel', 'puede_crear_zona', 'ver_usuarios', 'fecha_creacion', 'fecha_actualizacion', 'puede_eliminar_edificio', 'puede_eliminar_nivel', 'puede_eliminar_zona'];

    protected static ?string $campoOrdenAlfabetico = 'NombrePrediso';

    protected static function booted()
    {

        static::creating(function ($model) {
            $model->IDPredio = (string) Str::uuid();
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();
    }

    public function getUsuariosAttribute()
    {

        $usuarios = MsRolesPredios::where('IDPredio', $this->IDPredio)
            ->groupBy('IDPersona')
            ->get()->count();

        return $usuarios;
    }

    public function getFechaActualizacionAttribute()
    {

        $fecha_actualizacion = Carbon::parse($this->FechaActualizacionObjeto)->format('d/m/Y H:i');

        return $fecha_actualizacion;
    }

    public function getFechaCreacionAttribute()
    {
        $fecha_creacion = Carbon::parse($this->FechaCreacionObjeto)->format('d/m/Y H:i');

        return $fecha_creacion;
    }

    public function getEquiposAttribute() {}

    public function getLatAttribute()
    {
        $this->GeolocalizacionPredio = $this->GeolocalizacionPredio === '' ? null : $this->GeolocalizacionPredio;

        if (! is_null($this->GeolocalizacionPredio)) {

            $geolocalizacion = explode(',', $this->GeolocalizacionPredio);

            return (float) $geolocalizacion[0];
        } else {
            return '';
        }
    }

    public function getLngAttribute()
    {
        $this->GeolocalizacionPredio = $this->GeolocalizacionPredio === '' ? null : $this->GeolocalizacionPredio;

        if (! is_null($this->GeolocalizacionPredio)) {
            $geolocalizacion = explode(',', $this->GeolocalizacionPredio);

            return (float) $geolocalizacion[1];
        } else {
            return '';
        }
    }

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDPredio');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'IDPredio', 'IDPredio');
    }

    public function datos_generales()
    {
        return $this->hasMany(DatosGenerales::class, 'IDPredio', 'IDPredio');
    }

    public function tipo()
    {

        return $this->belongsTo(TiposPredios::class, 'IDTipoPredio', 'IDTipoPredio');
    }

    public function modeloAdministrativo()
    {

        return $this->belongsTo(ModelosAdministrativos::class, 'IDModeloAdministrativo', 'IDModeloAdministrativo');
    }

    public function nichoNegocio()
    {

        return $this->belongsTo(NichosNegocios::class, 'IDNichoNegocio', 'IDNichoNegocio');
    }

    public function getImageAttribute()
    {

        if (! $this->IDImagenLogoPredio) {
            return '/images/default_predio.png';
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->IDImagenLogoPredio,
            now()->addMinutes(20)
        );

        return $url;
    }

    public function getPlanoAttribute()
    {
        if (! $this->IDImagenPlanoPredio) {
            return null;
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->IDImagenPlanoPredio,
            now()->addMinutes(20)
        );

        return $url;
    }

    public function tienePermisoSobrePredio(string $permisoNombre): bool
    {
        $roles = Role::whereHas('permissions', function ($query) use ($permisoNombre) {
            $query->where('name', $permisoNombre);
        })->pluck('id');

        $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente ?? null)
            ->where('IDPersona', static::$usuarioAutenticado->IDPersona ?? null)
            ->where('IDPredio', $this->IDPredio)
            ->pluck('IDPredio');

        if (count($IDpredios) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getEditarPredioAttribute(): bool
    {


         if (MetikHelper::CheckPermisoCliente('Editar predios')) {
            return true;
        } else {
            return $this->tienePermisoSobrePredio('Crear edificios');
        }

    }

    public function getPuedeCrearEdificioAttribute()
    {

        if (MetikHelper::CheckPermisoCliente('Crear edificios')) {
            return true;
        } else {
            return $this->tienePermisoSobrePredio('Crear edificios');
        }
    }

    public function getPuedeCrearNivelAttribute()
    {

        if (MetikHelper::CheckPermisoCliente('Crear niveles')) {
            return true;
        } else {
            return $this->tienePermisoSobrePredio('Crear niveles');
        }

    }

    public function getPuedeCrearZonaAttribute()
    {
        if (MetikHelper::CheckPermisoCliente('Crear zonas')) {
            return true;
        } else {
            return $this->tienePermisoSobrePredio('Crear zonas');
        }

    }

    public function getVerUsuariosAttribute()
    {
        if (MetikHelper::CheckPermisoCliente('ver personas')) {
            return true;
        }else {
            return $this->tienePermisoSobrePredio('ver personas');
        }

    }

    public function getPuedeEliminarEdificioAttribute()
    {
        if (MetikHelper::CheckPermisoCliente('Eliminar edificios')) {
            return true;
        }else {
            return $this->tienePermisoSobrePredio('Eliminar edificios');
        }


    }

    public function getPuedeEliminarNivelAttribute()
    {
        if (MetikHelper::CheckPermisoCliente('Eliminar niveles')) {
            return true;
        }else {
            return $this->tienePermisoSobrePredio('Eliminar niveles');
        }


    }

    public function getPuedeEliminarZonaAttribute()
    {
         if (MetikHelper::CheckPermisoCliente('Eliminar zonas')) {
            return true;
        }else {
            return $this->tienePermisoSobrePredio('Eliminar zonas');
        }

    }
}
