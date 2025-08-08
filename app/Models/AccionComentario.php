<?php

namespace App\Models;

class AccionComentario extends BaseModel
{
    protected $table = 'plan_def_accion_comentario'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDComentarioAccion'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];
}
