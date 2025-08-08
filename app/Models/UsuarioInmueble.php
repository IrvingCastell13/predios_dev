<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class UsuarioInmueble extends Model
{
    use HasFactory, HasPermissions, HasRoles;

    protected $guard_name = 'api';

    protected $table = 'usuario_inmuebles';

    protected $guarded = [];

    protected $primaryKey = 'id_usuario_inmueble';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class);
    }
}
