<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use App\Http\Traits\PermisosTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Funciones extends BaseModel
{
    use HasFactory;
    use PermisosTrait;

    protected static $usuarioAutenticado;

    protected $table = 'ms_funciones';

    protected $primaryKey = 'IDFuncion';

    protected $guarded = [];

    protected $appends = ['activo'];

    protected static function booted()
    {

        static::creating(function ($model) {
            $model->IDFuncion = (string) Str::uuid();
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();
    }

    public function permiso()
    {
        return $this->belongsTo(Permission::class, 'PermisoFuncion', 'id');
    }


    public function tienePermisoSobrePredio(string $permisoNombre): bool
    {
        $roles = Role::whereHas('permissions', function ($query) use ($permisoNombre) {
            $query->where('name', $permisoNombre);
        })->pluck('id');



        $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente ?? null)
            ->where('IDPersona', static::$usuarioAutenticado->IDPersona ?? null)

            ->pluck('IDPredio');

        if (count($IDpredios) == 0) {
            return false;
        } else {
            return true;
        }
    }


    public function getActivoAttribute()
    {

        // dd(MetikHelper::CheckPermisoCliente($this->NombreFuncion));
        if (MetikHelper::CheckPermisoCliente($this->NombreFuncion)) {
            return true;
        } else {
            return $this->tienePermisoSobrePredio($this->NombreFuncion);
        }


        // $tienePermisoCliente = MetikHelper::CheckPermisoCliente($this->NombreFuncion);

        // //* si tiene permiso cliente regresa true de una
        // if($tienePermisoCliente) {
        //     return true;
        // }else{

        //     $IDPersona = static::$usuarioAutenticado->IDPersona;

        //     $IDCliente = static::$usuarioAutenticado->IDCliente;

        //     $IDPredios = request('IDPredio'); // puede venir como array o null

        //     $query = MsRolesPredios::where('IDPersona', $IDPersona)
        //         ->where('IDCliente', $IDCliente);

        //     if (! empty($IDPredios)) {
        //         // Aseguramos que sea array
        //         $predios = is_array($IDPredios) ? $IDPredios : [$IDPredios];
        //         $query->whereIn('IDPredio', $predios);
        //     }

        //     $rolesPersona = $query->get();


        //     if ($rolesPersona->isEmpty()) {
        //         return false;
        //     }

        //     // Obtener el permiso a evaluar
        //     $permiso = Permission::find($this->PermisoFuncion);

        //     if (! $permiso) {
        //         return false;
        //     }

        //     // Evaluar si al menos uno de los roles tiene el permiso
        //     foreach ($rolesPersona as $personaRol) {
        //         $rol = Role::find($personaRol->IDRol);

        //         if ($rol && $rol->hasPermissionTo($permiso->name)) {
        //             return true;
        //         }
        //     }

        // return false;

        // }




    }


}
