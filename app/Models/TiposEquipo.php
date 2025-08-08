<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiposEquipo extends Model
{
    protected $table = 'in_tipos_equipo'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDTipoEquipo'; // Ajusta según la clave primaria de tu tabla

    public $incrementing = false;

    protected $fillable = [
        'IDFabricante',
        'ClaveTipoEquipo',
        'NombreTipoEquipo',
        'DescripcionTipoEquipo',
        'MarcaTipoEquipo',
        'ModeloTipoEquipo',
        'VidaUtilTipoEquipo',
        'RecomendacionesTipoEquipo',
        'URLTipoEquipo',
        'ImagenTipoEquipo',
        'IDCliente',
        'FechaCreacionObjeto',
        'FechaActualizacionObjeto',
        'Borrado',
        'EsPublico',
    ];
}
