<?php

namespace App\Models;

use App\Http\Traits\PermisosTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class Opciones extends BaseModel
{
    use HasFactory;
    use PermisosTrait;

    protected static $usuarioAutenticado;

    protected $table = 'ms_opciones';

    protected $primaryKey = 'IDOpcion';

    protected $guarded = [];

    protected $appends = ['activo'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->IDOpcion = (string) Str::uuid();
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();
    }

    public function funciones()
    {
        return $this->hasMany(Funciones::class, 'IDOpcion');
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
            $permiso = Permission::find($this->PermisoOpcion);

            if ($permiso && $rol) {
                return $rol->hasPermissionTo($permiso->name);

            } else {

                return false;
            }

        }

    }
}
