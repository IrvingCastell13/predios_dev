<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;

class Personas extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'ms_personas';

    protected $primaryKey = 'IDPersona';

    protected $guarded = [];

    protected $appends = ['full_name', 'image', 'fecha_creacion_format', 'fecha_actualizacion_format', 'IDRolCliente' ];

    // protected $fillable = [
    //     'EmailPersona',
    //     'PasswordPersona',
    //     // Otros campos según sea necesario
    // ];

    protected $hidden = [
        'PasswordPersona',
    ];

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

    protected static function booted()
    {

        static::creating(function ($model) {


            if($model->NombrePersona === "null") {
                $model->NombrePersona = null;
            }
            if($model->ApellidoMaternoPersona === "null") {
                $model->ApellidoMaternoPersona = null;
            }
            if($model->ApellidoPaternoPersona === "null") {
                $model->ApellidoPaternoPersona = null;
            }
            // Verifica si la clave primaria está vacía y genera un UUID
            if (empty($model->IDPersona)) {
                $model->IDPersona = (string) Str::uuid();
            }
        });

        static::addGlobalScope('borrado', function (Builder $builder) {
            $builder->where('borrado', 0);
        });

    }

    public function rolesPredios()
    {
        return $this->hasMany(MsRolesPredios::class, 'IDPersona', 'IDPersona');
    }

    public function devices()
    {
        return $this->hasMany(DevicesPersona::class, 'IDPersona', 'IDPersona')
            ->where('Estado', 1);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'ms_roles_en_predios', 'IDPersona', 'IDRol');
    }

    public function rolesPorPredio()
    {
        return $this->belongsToMany(Role::class, 'ms_roles_predios', 'IDPersona', 'IDRol');
    }

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDPersona');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'IDAtiendeTicket', 'IDPersona');
    }

    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDPersona');
    }


    public function getNombrePersonaAttribute($value)
    {
        if ($value === null || trim($value) === "null") {
            return null;
        }
        return $value;
    }

    public function getApellidoPaternoPersonaAttribute($value)
    {
        if ($value === null || trim($value) === "null") {
            return null;
        }
        return $value;
    }

    public function getApellidoMaternoPersonaAttribute($value)
    {


         if ($value === null || trim($value) === "null") {
            return null;
        }
        return $value;

    }



    public function getFullNameAttribute()
    {
        $nombre = $this->NombrePersona == 'null' ? null : $this->NombrePersona;
        $apellidoPaterno = $this->ApellidoPaternoPersona == 'null' ? null : $this->ApellidoPaternoPersona;
        $apellidoMaterno = $this->ApellidoMaternoPersona == 'null' ? null : $this->ApellidoMaternoPersona;

        return "{$nombre} {$apellidoPaterno} {$apellidoMaterno}";
    }

    public function getImageAttribute()
    {

        if (! $this->logo) {
            return url('/images/avatar_default.jpg');
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->logo,
            now()->addMinutes(20)
        );

        return $url;
    }

    public function getFechaCreacionFormatAttribute()
    {
        return $this->FechaCreacionObjeto ? $this->FechaCreacionObjeto->format('Y/m/d') : null;
    }

    public function getFechaActualizacionFormatAttribute()
    {
        return $this->FechaActualizacionObjeto ? $this->FechaActualizacionObjeto->format('Y/m/d') : null;
    }

    public function getIDRolClienteAttribute()
    {
        $rol = MsRolesClientes::where('IDPersona', $this->IDPersona)->where('IDCliente', $this->IDCliente)->first();
        if ($rol) {
            return $rol->IDRol;
        } else {
            return null;
        }

    }
}
