<?php

namespace App\Models;

class MetadatosGenerales extends BaseModel
{
    protected $table = 'conf_metadatos_generales';

    protected $primaryKey = 'IDMetadatoGeneral';

    protected $guarded = [];

    protected $appends = ['regex', 'tipo'];

    public function tipo_metadato()
    {
        return $this->belongsTo(TiposMetadatos::class, 'IDTipoMetadato', 'IDTipoMetadato');
    }

    public function getRegexAttribute()
    {
        $tipo = TiposMetadatos::where('IDTipoMetadato', $this->IDTipoMetadato)->first();

        if ($tipo) {
            return $tipo->ExpRegularTipoMetadato;
        } else {
            return '';
        }
    }

    public function getTipoAttribute()
    {
        $tipo = TiposMetadatos::where('IDTipoMetadato', $this->IDTipoMetadato)->first();

        if ($tipo) {
            return $tipo->NombreTipoMetadato;
        } else {
            return '';
        }
    }
}
