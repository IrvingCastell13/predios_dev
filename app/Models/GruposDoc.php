<?php

namespace App\Models;

use Illuminate\Support\Str;

class GruposDoc extends BaseModel
{
    protected $table = 'gd_grupos_doc';

    protected $primaryKey = 'IDGrupoDoc';

    protected $guarded = [];

    protected $appends = ['obligatorios', 'opcionales', 'cantidad_documentos', 'unique_uuid'];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($grupo) {
            if ($grupo->isDirty('borrado') && $grupo->borrado == 1) {
                // Obtener todas las categorías relacionadas
                $categorias = CategoriasDocumentos::where('IDGrupoDoc', $grupo->IDGrupoDoc)->get();

                foreach ($categorias as $categoria) {
                    // Marcar la categoría como borrado = 1
                    $categoria->update(['borrado' => 1]);

                    // Marcar los tipos de documentos asociados a la categoría como borrado = 1
                    TiposDocumentos::where('IDCategoriaDocumento', $categoria->IDCategoriaDoc)->update(['borrado' => 1]);
                }
            }
        });
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
        // Subconsulta para categorías relacionadas al grupo
        $categoriasSubquery = CategoriasDocumentos::where('IDGrupoDoc', $this->IDGrupoDoc)
            ->select('IDCategoriaDoc');

        // Subconsulta para tipos de documentos en esas categorías
        $tiposSubquery = TiposDocumentos::whereIn('IDCategoriaDocumento', $categoriasSubquery)
            ->select('IDTipoDocumento');

        // Consulta principal usando subconsultas
        return ObligatoriosTipoinmueble::whereIn('IDTipoDocumento', $tiposSubquery)
            ->where('ObligatorioNichosNegocio', $obligatorio)
            ->count();
    }

    protected function getDocumentEstado($estado)
    {
        // Subconsulta para categorías relacionadas al grupo
        $categoriasSubquery = CategoriasDocumentos::where('IDGrupoDoc', $this->IDGrupoDoc)
            ->select('IDCategoriaDoc');

        // Subconsulta para tipos de documentos en esas categorías
        $tiposSubquery = TiposDocumentos::whereIn('IDCategoriaDocumento', $categoriasSubquery)
            ->select('IDTipoDocumento');

        // Consulta principal usando subconsultas
        return ObligatoriosTipoinmueble::whereIn('IDTipoDocumento', $tiposSubquery)
            ->where('ObligatorioNichosNegocio', $estado)
            ->count();
    }

    public function getCantidadDocumentosAttribute()
    {
        // Subconsulta para categorías relacionadas al grupo
        $categoriasSubquery = CategoriasDocumentos::where('IDGrupoDoc', $this->IDGrupoDoc)
            ->select('IDCategoriaDoc');

        // Subconsulta para tipos de documentos en esas categorías
        $tiposSubquery = TiposDocumentos::whereIn('IDCategoriaDocumento', $categoriasSubquery)
            ->select('IDTipoDocumento');

        return Documentos::whereIn('IDTipoDocumento', $tiposSubquery)
            ->count();
    }

    public function getUniqueUuidAttribute()
    {
        return (string) Str::uuid();
    }
}
