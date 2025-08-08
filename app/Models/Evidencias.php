<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Evidencias extends BaseModel
{
    protected $table = 'tk_evidencias';

    protected $primaryKey = 'IDTicket';

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
