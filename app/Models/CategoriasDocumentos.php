<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class CategoriasDocumentos extends BaseModel
{
    use HasFactory;

    protected $table = 'gd_categorias_doc';

    protected $primaryKey = 'IDCategoriaDoc';

    protected $appends = ['obligatorios', 'opcionales', 'cantidad_documentos', 'unique_uuid'];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($categoria) {
            if ($categoria->isDirty('borrado') && $categoria->borrado == 1) {
                TiposDocumentos::where('IDCategoriaDocumento', $categoria->IDCategoriaDoc)->update(['borrado' => 1]);
            }
        });
    }

    public function grupo()
    {
        return $this->belongsTo(GruposDoc::class, 'IDGrupoDoc', 'IDGrupoDoc');
    }

    public function getObligatoriosAttribute()
    {
        return $this->getDocumentCount(1);
    }

    public function getOpcionalesAttribute()
    {
        return $this->getDocumentCount(0);
    }

    protected function getDocumentCount($obligatorio)
    {

        // Subconsulta para tipos de documentos en esas categorías
        $tiposSubquery = TiposDocumentos::where('IDCategoriaDocumento', $this->IDCategoriaDoc)
            ->select('IDTipoDocumento');

        // Consulta principal usando subconsultas
        return ObligatoriosTipoinmueble::whereIn('IDTipoDocumento', $tiposSubquery)
            ->where('ObligatorioNichosNegocio', $obligatorio)
            ->count();
    }

    protected function getDocumentEstado($estado)
    {

        // Subconsulta para tipos de documentos en esas categorías
        $tiposSubquery = TiposDocumentos::where('IDCategoriaDocumento', $this->IDCategoriaDoc)
            ->select('IDTipoDocumento');

        // Consulta principal usando subconsultas
        return ObligatoriosTipoinmueble::whereIn('IDTipoDocumento', $tiposSubquery)
            ->where('ObligatorioNichosNegocio', $estado)
            ->count();
    }

    public function getCantidadDocumentosAttribute()
    {
        $tiposSubquery = TiposDocumentos::where('IDCategoriaDocumento', $this->IDCategoriaDoc)
            ->select('IDTipoDocumento');

        return Documentos::whereIn('IDTipoDocumento', $tiposSubquery)
            ->count();
    }

    public function getUniqueUuidAttribute()
    {
        return (string) Str::uuid();
    }
}

// TODO si se hace update del nombre se cambie en el permiso
// TODO cuando se crea una categoria crear un permiso con el mismo nombre
