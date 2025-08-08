<?php

namespace App\Models;

use App\Http\Traits\PermisosTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use PermisosTrait;
    use HasFactory;

    protected static $usuarioAutenticado;

    // Indicar que el modelo debe usar timestamps
    public $timestamps = true;

    // Definir los nombres personalizados de los timestamps
    const CREATED_AT = 'FechaCreacionObjeto';

    const UPDATED_AT = 'FechaActualizacionObjeto';

    // También puedes establecer los casts para las fechas
    protected $casts = [
        'FechaCreacionObjeto' => 'datetime',
        'FechaActualizacionObjeto' => 'datetime',
    ];

    // Indica que la clave primaria no es auto-incremental
    public $incrementing = false;

    // tipo de dato de PK
    protected $keyType = 'string';

    // Campo por el cual se ordenará alfabéticamente. Se puede sobrescribir en los modelos hijos.
    protected static ?string $campoOrdenAlfabetico = null;

    protected static function booted()
    {

        static::creating(function ($model) {
            // Verifica si la clave primaria está vacía y genera un UUID
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        static::addGlobalScope('borrado', function (Builder $builder) {
            $builder->where('borrado', 0);
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();

        static::addGlobalScope('orden_alfabetico', function (Builder $builder) {
            $campo = static::$campoOrdenAlfabetico;

            if ($campo) {
                $builder->orderBy($campo);
            }
        });

    }
}
