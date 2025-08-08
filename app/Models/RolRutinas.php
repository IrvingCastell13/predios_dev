<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolRutinas extends BaseModel
{
    use HasFactory;

    protected $table = 'ms_roles_rutinas'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDRolRutina'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];
}
