<?php

namespace App\Models;

use Illuminate\Support\Str;

class TiposDocumentos extends BaseModel
{
    protected $table = 'gd_tipos_documento';

    protected $primaryKey = 'IDTipoDocumento';

    protected $guarded = [];

    protected $appends = ['region', 'IDGrupoDoc', 'obligatorio', 'unique_id', 'unique_uuid', 'IDPersonas', 'cantidad_documentos'];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'IDPais');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'IDEstado');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'IDMunicipio');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriasDocumentos::class, 'IDCategoriaDocumento');
    }

    public function getRegionAttribute()
    {
        if ($this->IDMunicipio) {
            $query = Municipio::find($this->IDMunicipio);
            if ($query) {
                return $query->NombreMunicipio;
            }
        }
        if ($this->IDEstado) {
            $query = Estado::find($this->IDEstado);
            if ($query) {
                return $query->NombreEstado;
            }
        }
        if ($this->IDPais) {
            $query = Pais::find($this->IDPais);
            if ($query) {
                return $query->NombrePais;
            }
        }

        return null;
    }

    public function getIDGrupoDocAttribute()
    {
        return $this->categoria->IDGrupoDoc ?? null;
    }

    public function getObligatorioAttribute()
    {
        $obligatoriosTipoPredio = ObligatoriosTipoPredio::where('IDTipoDocumento', $this->IDTipoDocumento)->first();

        if ($obligatoriosTipoPredio) {
            return $obligatoriosTipoPredio->ObligatorioNichosNegocio == 1 ? true : false;
        }

        return false;
    }

    public function getUniqueIdAttribute()
    {
        return Str::random(110);
    }

    public function documentos()
    {
        return $this->hasMany(Documentos::class, 'IDTipoDocumento', 'IDTipoDocumento');
    }

    public function getIDPersonasAttribute()
    {
        return Follow::where('IDObjeto', $this->IDTipoDocumento)->pluck('IDpersona')->toArray();
    }

    public function getCantidadDocumentosAttribute()
    {
        return $this->documentos()->count();
    }

    public function getUniqueUuidAttribute()
    {
        return (string) Str::uuid();
    }

    // public function getUbicacionAttribute()
    // {
    //     return collect([
    //         optional($this->predio)->NombrePredio,
    //         optional($this->edificio)->NombreEdificio,
    //         optional($this->nivel)->NombreNivel,
    //         optional($this->zona)->NombreZona,
    //     ])->filter()->implode(' - ');
    // }

}
