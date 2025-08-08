<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    use HasFactory;

    protected $table = 'inmuebles';

    protected $guarded = [];

    protected $primaryKey = 'id_inmueble';

    public function usuarioInmuebles()
    {
        return $this->hasMany(UsuarioInmueble::class);
    }
}
