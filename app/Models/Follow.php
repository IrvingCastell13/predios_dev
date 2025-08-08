<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Follow extends BaseModel
{
    use HasFactory;

    protected $table = 'follows';

    protected $primaryKey = 'IDFollow';

    protected $guarded = [];

    protected $fillable = ['IDObjeto', 'IDPersona'];

    public static function storeIfNotExists($IDObjeto, $IDPersona)
    {
        return self::updateOrCreate(
            ['IDObjeto' => $IDObjeto, 'IDPersona' => $IDPersona],
            ['borrado' => 0] // Si ya existe, lo marcamos como activo
        );
    }

    public static function syncFollows($IDObjeto, $IDPersonas)
    {

        // Obtener los IDPersona actuales en la base de datos para este IDObjeto
        $personasActuales = self::where('IDObjeto', $IDObjeto)->pluck('IDPersona')->toArray();

        // **1️⃣ Insertar los nuevos (si no existen)**
        foreach ($IDPersonas as $IDPersona) {
            self::storeIfNotExists($IDObjeto, $IDPersona);
        }

        // **2️⃣ Eliminar los que ya no están en la nueva lista**
        self::where('IDObjeto', $IDObjeto)
            ->whereNotIn('IDPersona', $IDPersonas)
            ->update(['borrado' => 1]); // Aquí actualizamos en vez de eliminar

    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }
}
