<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArchivoAdjuntosTicket extends BaseModel
{
    use HasFactory;

    protected $table = 'tk_archivos_adjuntos'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDArchivoAdjunto';

    protected $guarded = [];

    protected $appends = ['fecha_hora', 'image'];

    public function getFechaHoraAttribute()
    {
        return $this->FechaActualizacionObjeto->format('d/m/Y - H:i');
    }

    public function getImageAttribute()
    {

        if ($this->archivo->ExtensionArchivo !== 'pdf') {

            $url = MetikHelper::getImageAwsS3($this->archivo);
            return $url;

        } else {
            return url('/images/upload-img-1.jpg');
        }

    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }

    public function archivo()
    {
        return $this->belongsTo(Archivos::class, 'IDArchivo', 'IDArchivo');
    }
}
