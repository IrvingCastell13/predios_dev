<?php

namespace App\Models;

use App\Http\Traits\PermisosTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Modulo extends BaseModel
{
    use HasFactory;
    use PermisosTrait;

    protected static $usuarioAutenticado;

    protected $table = 'ms_modulos';

    protected $primaryKey = 'IDModulo';

    protected $guarded = [];

    protected $appends = ['tiene_opciones', 'activo'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->IDModulo = (string) Str::uuid();
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();

    }

    public function opciones()
    {
        return $this->hasMany(Opciones::class, 'IDModulo');
    }

    public function funciones()
    {
        return $this->hasMany(Funciones::class, 'IDModulo');
    }

    public function catalogos()
    {
        return $this->hasMany(MsCatalogos::class, 'IDModulo')
            ->where('EsPublico', static::$usuarioAutenticado->IDRol == 1 ? 1 : 0);
    }

    public function getTieneOpcionesAttribute()
    {
        return $this->opciones()->count() > 0;
    }

    public function getActivoAttribute()
    {

        $personaPredio = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
            ->where('IDPredio', request('IDPredio'))
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
            ->first();

        if (! $personaPredio) {

            return false;

        } else {

            $rol = Role::find($personaPredio->IDRol);
            $permiso = Permission::find($this->IDPermiso);

            if ($permiso && $rol) {
                return $rol->hasPermissionTo($permiso->name);

            } else {

                return false;
            }

        }

    }
}
