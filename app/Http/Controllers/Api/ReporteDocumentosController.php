<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportePorPrediosRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DocumentosExport;
use App\Exports\ReporteRenovacionExport;

/**
 * Controlador para gestionar los reportes de documentos, cumplimiento y vigencia.
 */
class ReporteDocumentosController extends Controller
{
    //======================================================================
    // MÉTODOS DE LISTADO DE RECURSOS (CATÁLOGOS)
    //======================================================================

    /**
     * Devuelve una lista de recursos genéricos (países, estados, etc.).
     *
     * @param string $tabla El nombre de la tabla en la base de datos.
     * @param array $columnas Las columnas a seleccionar.
     * @param string $columnaOrden La columna por la cual ordenar los resultados.
     * @param Request $request La solicitud HTTP para filtros opcionales.
     * @param string|null $llavePadre El nombre de la columna de la clave foránea para filtrar (ej. 'IDPais').
     * @param string|null $paramPadre El nombre del parámetro en el request (ej. 'pais_id').
     * @return JsonResponse
     */
    private function listarRecurso(string $tabla, array $columnas, string $columnaOrden, Request $request, ?string $llavePadre = null, ?string $paramPadre = null): JsonResponse
    {
        $query = DB::table($tabla)
            ->select($columnas)
            ->where('Borrado', 0);

        if ($llavePadre && $paramPadre && $request->filled($paramPadre)) {
            $query->whereIn($llavePadre, (array)$request->input($paramPadre));
        }

        $resultados = $query->orderBy($columnaOrden)->get();
        return response()->json($resultados);
    }

    public function listarPaises()
    {
        return $this->listarRecurso('conf_paises', ['IDPais', 'NombrePais'], 'NombrePais', new Request());
    }

    public function listarEstados(Request $request)
    {
        return $this->listarRecurso('conf_estados', ['IDEstado', 'NombreEstado', 'IDPais'], 'NombreEstado', $request, 'IDPais', 'pais_id');
    }

    public function listarMunicipios(Request $request)
    {
        return $this->listarRecurso('conf_municipios', ['IDMunicipio', 'NombreMunicipio', 'IDEstado'], 'NombreMunicipio', $request, 'IDEstado', 'estado_id');
    }

    public function listarPredios(Request $request)
    {
        $query = DB::table('conf_predios')
            ->select('IDPredio', 'NombrePredio', 'IDMunicipio', 'IDEstado', 'IDPais')
            ->where('Borrado', 0);

        $query->when($request->filled('pais_id'), fn($q) => $q->whereIn('IDPais', (array)$request->pais_id));
        $query->when($request->filled('estado_id'), fn($q) => $q->whereIn('IDEstado', (array)$request->estado_id));
        $query->when($request->filled('municipio_id'), fn($q) => $q->whereIn('IDMunicipio', (array)$request->municipio_id));

        $predios = $query->orderBy('NombrePredio')->get();
        return response()->json($predios);
    }

    public function listarEdificios(Request $request)
    {
        return $this->listarRecurso('conf_edificios', ['IDEdificio', 'NombreEdificio', 'IDPredio'], 'NombreEdificio', $request, 'IDPredio', 'predio_id');
    }

    public function listarNiveles(Request $request)
    {
        return $this->listarRecurso('conf_niveles', ['IDNivel', 'NombreNivel', 'IDEdificio'], 'NombreNivel', $request, 'IDEdificio', 'edificio_id');
    }

    public function listarZonas(Request $request)
    {
        return $this->listarRecurso('conf_zonas', ['IDZona', 'NombreZona', 'IDNivel'], 'NombreZona', $request, 'IDNivel', 'nivel_id');
    }

    public function listarGruposDoc()
    {
        return $this->listarRecurso('gd_grupos_doc', ['IDGrupoDoc', 'NombreGrupoDoc'], 'NombreGrupoDoc', new Request());
    }

    public function listarCategoriasDoc(Request $request)
    {
        $request->validate(['grupo_ids' => 'sometimes|array']);
        $grupoIds = $request->input('grupo_ids', []);

        if (empty($grupoIds)) {
            return response()->json([]);
        }

        return $this->listarRecurso('gd_categorias_doc', ['IDCategoriaDoc', 'NombreCategoriaDoc', 'IDGrupoDoc'], 'NombreCategoriaDoc', $request, 'IDGrupoDoc', 'grupo_ids');
    }

    public function listarTiposDocumento()
    {
        return $this->listarRecurso('gd_tipos_documento', ['IDTipoDocumento', 'NombreTipoDocumento'], 'NombreTipoDocumento', new Request());
    }

    public function listarTiposInmueble()
    {
        return $this->listarRecurso('conf_tipos_predio', [DB::raw('IDTipoPredio as IDTipoInmueble'), DB::raw('NombreTipoPredio as NombreTipoInmueble')], 'NombreTipoInmueble', new Request());
    }


    public function listarPlanesRenovacion()
    {
        $planes = DB::table('plan_planes')
            ->select('IDPlan', 'NombrePlan')
            ->where('IDTipoPlan', 5)
            ->where('Borrado', 0)
            ->orderBy('NombrePlan')
            ->get();
        return response()->json($planes);
    }

    //======================================================================
    // MÉTODOS PRIVADOS DE CONSTRUCCIÓN DE QUERYS Y FILTROS
    //======================================================================

    /**
     * Construye la consulta base para reportes centrados en Predios.
     * Incluye todos los JOINS necesarios para relacionar predios con su ubicación,
     * documentos, categorías, grupos, archivos y estados.
     *
     * @return Builder
     */
    private function construirQueryBasePredios(): Builder
    {
        return DB::table('conf_predios as p')
            // Relaciona el predio con los documentos obligatorios según su tipo.
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            // Obtiene la información del tipo de documento.
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            // Obtiene la categoría del documento.
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            // Obtiene el grupo al que pertenece la categoría.
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            // Relaciona con los documentos existentes (si los hay).
            ->leftJoin('gd_documentos as d', fn($j) => $j->on('d.IDPredio', '=', 'p.IDPredio')->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento'))
            // Relaciona con los archivos adjuntos (si los hay).
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            // Relaciona con la ubicación física (edificios, niveles, zonas).
            ->leftJoin('conf_edificios as e', 'e.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('conf_niveles as n', 'n.IDEdificio', '=', 'e.IDEdificio')
            ->leftJoin('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            // Relaciona con el estado de workflow (vigencia).
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia')
        ;
    }

    /**
     * Construye la consulta base para reportes centrados en Zonas.
     * Similar a la de predios, pero el punto de partida son las zonas y sus tipos.
     *
     * @return Builder
     */
    private function construirQueryBaseZonas(): Builder
    {
        return DB::table('conf_zonas as z')
            // Relaciona la zona con su ubicación física superior.
            ->join('conf_niveles as n', 'n.IDNivel', '=', 'z.IDNivel')
            ->join('conf_edificios as e', 'e.IDEdificio', '=', 'n.IDEdificio')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'e.IDPredio')
            // Relaciona la zona con sus documentos obligatorios según su tipo.
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'z.IDTipoZona')
            // Obtiene la información de tipos, categorías y grupos de documentos.
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            // Relaciona con documentos y archivos existentes para la zona.
            ->leftJoin('gd_documentos as d', fn($j) => $j->on('d.IDZona', '=', 'z.IDZona')->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento'))
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            // Relaciona con el estado de workflow (vigencia).
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia')
            ->where('d.Borrado', 0);
    }


    /**
     * Aplica filtros comunes a una consulta de reportes basada en Predios.
     *
     * @param Builder $query La consulta a la que se aplicarán los filtros.
     * @param Request $request La solicitud con los parámetros de filtro.
     * @return Builder La consulta con los filtros aplicados.
     */
    private function aplicarFiltrosComunes(Builder $query, Request $request): Builder
    {
        // Filtros de ubicación
        $query->when($request->filled('id_paises'), fn($q) => $q->whereIn('p.IDPais', (array)$request->id_paises));
        $query->when($request->filled('id_estados'), fn($q) => $q->whereIn('p.IDEstado', (array)$request->id_estados));
        $query->when($request->filled('id_municipios'), fn($q) => $q->whereIn('p.IDMunicipio', (array)$request->id_municipios));
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', (array)$request->predio_ids));
        $query->when($request->filled('id_edificios'), fn($q) => $q->whereIn('e.IDEdificio', (array)$request->id_edificios));
        $query->when($request->filled('id_niveles'), fn($q) => $q->whereIn('n.IDNivel', (array)$request->id_niveles));
        $query->when($request->filled('id_zonas'), fn($q) => $q->whereIn('z.IDZona', (array)$request->id_zonas));

        // Filtros de documentos
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', (array)$request->grupo_ids));
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cat.IDCategoriaDoc', (array)$request->categoria_ids));
        $query->when($request->filled('tipo_doc_ids'), fn($q) => $q->whereIn('td.IDTipoDocumento', (array)$request->tipo_doc_ids));
        $query->when($request->filled('tipo_inmueble_ids'), fn($q) => $q->whereIn('p.IDTipoPredio', (array)$request->tipo_inmueble_ids));

        // Filtro de estado de archivo
        $query->when($request->input('estado_archivo') === 'con', fn($q) => $q->whereNotNull('a.IDArchivo'));

        return $query;
    }

    /**
     * Aplica filtros comunes a una consulta de reportes basada en Zonas.
     *
     * @param Builder $query La consulta a la que se aplicarán los filtros.
     * @param Request $request La solicitud con los parámetros de filtro.
     * @return Builder La consulta con los filtros aplicados.
     */
    private function aplicarFiltrosComunesZona(Builder $query, Request $request): Builder
    {
        // Filtros de ubicación
        $query->when($request->filled('id_paises'), fn($q) => $q->whereIn('p.IDPais', (array)$request->id_paises));
        $query->when($request->filled('id_estados'), fn($q) => $q->whereIn('p.IDEstado', (array)$request->id_estados));
        $query->when($request->filled('id_municipios'), fn($q) => $q->whereIn('p.IDMunicipio', (array)$request->id_municipios));
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', (array)$request->predio_ids));
        $query->when($request->filled('id_edificios'), fn($q) => $q->whereIn('e.IDEdificio', (array)$request->id_edificios));
        $query->when($request->filled('id_niveles'), fn($q) => $q->whereIn('n.IDNivel', (array)$request->id_niveles));
        $query->when($request->filled('id_zonas'), fn($q) => $q->whereIn('z.IDZona', (array)$request->id_zonas));

        // Filtros de documentos
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', (array)$request->grupo_ids));
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cat.IDCategoriaDoc', (array)$request->categoria_ids));
        $query->when($request->filled('tipo_doc_ids'), fn($q) => $q->whereIn('td.IDTipoDocumento', (array)$request->tipo_doc_ids));
        // Para zonas, el tipo de inmueble se filtra por `z.IDTipoZona`
        $query->when($request->filled('tipo_inmueble_ids'), fn($q) => $q->whereIn('z.IDTipoZona', (array)$request->tipo_inmueble_ids));

        // Filtro de estado de archivo
        $query->when($request->input('estado_archivo') === 'con', fn($q) => $q->whereNotNull('a.IDArchivo'));

        return $query;
    }

    //======================================================================
    // ENDPOINTS DE REPORTES BÁSICOS
    //======================================================================

    public function estadoPorPredio(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        $documentos = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->whereIn('p.IDPredio', $predioIds)
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'td.IDTipoDocumento as tipo_documento_id',
                'td.NombreTipoDocumento as nombre_documento',
                DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado")
            )
            ->orderBy('p.IDPredio')
            ->orderBy('td.IDTipoDocumento')
            ->get();

        return response()->json($documentos);
    }

    public function documentosPorPredioConArchivos(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        $documentos = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            ->whereIn('p.IDPredio', $predioIds)
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'td.IDTipoDocumento as tipo_documento_id',
                'td.NombreTipoDocumento as nombre_documento',
                DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado"),
                DB::raw("CASE WHEN d.IDDocumento IS NULL THEN 'SIN DOCUMENTO' WHEN a.IDArchivo IS NOT NULL THEN 'CON ARCHIVO' ELSE 'SIN ARCHIVO' END as archivo")
            )
            ->orderBy('p.IDPredio')
            ->orderBy('td.IDTipoDocumento')
            ->get();

        return response()->json($documentos);
    }

    public function resumenPorGrupo(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        $resumen = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->whereIn('p.IDPredio', $predioIds)
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'g.IDGrupoDoc as grupo_id',
                'g.NombreGrupoDoc as nombre_grupo',
                DB::raw('COUNT(*) as total_esperados'),
                DB::raw('COUNT(d.IDDocumento) as creados'),
                DB::raw('COUNT(*) - COUNT(d.IDDocumento) as faltantes')
            )
            ->groupBy('p.IDPredio', 'p.NombrePredio', 'g.IDGrupoDoc', 'g.NombreGrupoDoc')
            ->orderBy('p.IDPredio')
            ->orderBy('g.IDGrupoDoc')
            ->get();

        return response()->json($resumen);
    }

    public function resumenPorSubcategoria(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        $resumen = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->whereIn('p.IDPredio', $predioIds)
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'cat.IDCategoriaDoc as categoria_id',
                'cat.NombreCategoriaDoc as nombre_categoria',
                DB::raw('COUNT(*) as total_esperados'),
                DB::raw('COUNT(d.IDDocumento) as creados'),
                DB::raw('COUNT(*) - COUNT(d.IDDocumento) as faltantes'),
                DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_porcentaje')
            )
            ->groupBy('p.IDPredio', 'p.NombrePredio', 'cat.IDCategoriaDoc', 'cat.NombreCategoriaDoc')
            ->orderBy('p.IDPredio')
            ->orderBy('cat.IDCategoriaDoc')
            ->get();

        return response()->json($resumen);
    }

    public function cumplimientoPonderadoTotal(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        $ponderado = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'do.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            ->whereIn('p.IDPredio', $predioIds)
            ->selectRaw("
                COUNT(*) as total_esperados,
                COUNT(d.IDDocumento) as creados,
                COUNT(DISTINCT a.IDArchivo) as con_archivo,
                ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_creados,
                ROUND((COUNT(DISTINCT a.IDArchivo) * 100.0) / COUNT(*), 2) as cumplimiento_anexados
            ")
            ->first();

        return response()->json($ponderado);
    }

    //======================================================================
    // ENDPOINTS DASHBOARD: CUMPLIMIENTO (GRÁFICAS DE MATRIZ)
    //======================================================================

    public function matrizPorSubcategoria(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(DISTINCT td.IDTipoDocumento) = 0 THEN 0 ELSE ROUND((COUNT(DISTINCT d.IDDocumento) * 100.0) / COUNT(DISTINCT td.IDTipoDocumento), 2) END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->subcategoria, 'y' => $i->predio, 'v' => (float)$i->cumplimiento]));
    }

    public function matrizPorSubcategoriaZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $query->select(
            'z.NombreZona as zona',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(DISTINCT td.IDTipoDocumento) = 0 THEN 0 ELSE ROUND((COUNT(DISTINCT d.IDDocumento) * 100.0) / COUNT(DISTINCT td.IDTipoDocumento), 2) END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('z.NombreZona', 'cat.NombreCategoriaDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->subcategoria, 'y' => $i->zona, 'v' => (float)$i->cumplimiento]));
    }

    public function matrizArchivosPorSubcategoria(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(DISTINCT a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->subcategoria, 'y' => $i->predio, 'v' => (int)$i->cumplimiento]));
    }

    public function matrizArchivosPorSubcategoriaZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $query->select(
            'z.NombreZona as zona',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(DISTINCT a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('z.NombreZona', 'cat.NombreCategoriaDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->subcategoria, 'y' => $i->zona, 'v' => (int)$i->cumplimiento]));
    }

    public function matrizPorGrupo(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(DISTINCT td.IDTipoDocumento) = 0 THEN 0 ELSE ROUND((COUNT(DISTINCT d.IDDocumento) * 100.0) / COUNT(DISTINCT td.IDTipoDocumento), 2) END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('p.NombrePredio', 'g.NombreGrupoDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->grupo, 'y' => $i->predio, 'v' => (float)$i->cumplimiento]));
    }

    public function matrizPorGrupoZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $query->select(
            'z.NombreZona as zona',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(DISTINCT td.IDTipoDocumento) = 0 THEN 0 ELSE ROUND((COUNT(DISTINCT d.IDDocumento) * 100.0) / COUNT(DISTINCT td.IDTipoDocumento), 2) END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('z.NombreZona', 'g.NombreGrupoDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->grupo, 'y' => $i->zona, 'v' => (float)$i->cumplimiento]));
    }

    public function matrizArchivosPorGrupo(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(DISTINCT a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('p.NombrePredio', 'g.NombreGrupoDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->grupo, 'y' => $i->predio, 'v' => (int)$i->cumplimiento]));
    }

    public function matrizArchivosPorGrupoZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $query->select(
            'z.NombreZona as zona',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(DISTINCT a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')
        )->groupBy('z.NombreZona', 'g.NombreGrupoDoc');

        $query->when($request->input('estado_archivo') === 'sin', fn($q) => $q->having('total_archivos', '=', 0));

        $resultados = $query->get();
        return response()->json($resultados->map(fn($i) => ['x' => $i->grupo, 'y' => $i->zona, 'v' => (int)$i->cumplimiento]));
    }

    public function calificacionesPonderadasPorPredio(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $datos = $query->select(
            'p.NombrePredio as predio',
            DB::raw('COUNT(DISTINCT td.IDTipoDocumento) as total_obligatorios'),
            DB::raw('COUNT(DISTINCT d.IDDocumento) as total_creados'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_con_archivo')
        )->groupBy('p.IDPredio', 'p.NombrePredio')->orderBy('p.NombrePredio')->get();

        $labels = $datos->pluck('predio');
        $creadosData = $datos->map(fn($item) => $item->total_obligatorios == 0 ? 0 : round(($item->total_creados * 100) / $item->total_obligatorios, 2));
        $conArchivoData = $datos->map(fn($item) => $item->total_creados == 0 ? 0 : round(($item->total_con_archivo * 100) / $item->total_creados, 2));

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => '% Documentos creados', 'data' => $creadosData],
                ['label' => '% Con archivo', 'data' => $conArchivoData]
            ]
        ]);
    }

    public function calificacionesPonderadasPorZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $datos = $query->select(
            'z.NombreZona as zona',
            DB::raw('COUNT(DISTINCT td.IDTipoDocumento) as total_obligatorios'),
            DB::raw('COUNT(DISTINCT d.IDDocumento) as total_creados'),
            DB::raw('COUNT(DISTINCT a.IDArchivo) as total_con_archivo')
        )->groupBy('z.IDZona', 'z.NombreZona')->orderBy('z.NombreZona')->get();

        $labels = $datos->pluck('zona');
        $creadosData = $datos->map(fn($item) => $item->total_obligatorios == 0 ? 0 : round(($item->total_creados * 100) / $item->total_obligatorios, 2));
        $conArchivoData = $datos->map(fn($item) => $item->total_creados == 0 ? 0 : round(($item->total_con_archivo * 100) / $item->total_creados, 2));

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => '% Documentos creados', 'data' => $creadosData],
                ['label' => '% Con archivo', 'data' => $conArchivoData]
            ]
        ]);
    }


    //======================================================================
    // ENDPOINTS DASHBOARD: VIGENCIA (GRÁFICAS Y TABLAS)
    //======================================================================

    public function documentosConEstadoPorCategoria(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'td.NombreTipoDocumento as documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.ClaveEstado as v'
        )->orderBy('p.NombrePredio')->orderBy('td.NombreTipoDocumento')->get();

        $data = $resultados->map(function ($item) {
            $estado = ($item->estado_documento === 'CREADO') ? ($item->estado_accion ?? 'Pendiente') : 'FALTANTE';
            return ['x' => $item->documento, 'y' => $item->predio, 'v' => $item->v, 'estado' => $estado];
        });

        return response()->json($data);
    }

    public function documentosConEstadoPorCategoriaZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $resultados = $query->select(
            'z.NombreZona as zona',
            'td.NombreTipoDocumento as documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.ClaveEstado as v'
        )->orderBy('z.NombreZona')->orderBy('td.NombreTipoDocumento')->get();

        $data = $resultados->map(function ($item) {
            $estado = ($item->estado_documento === 'CREADO') ? ($item->estado_accion ?? 'Pendiente') : 'FALTANTE';
            return ['x' => $item->documento, 'y' => $item->zona, 'v' => $item->v, 'estado' => $estado];
        });

        return response()->json($data);
    }

    public function documentosPorSubcategoria(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'td.NombreTipoDocumento as documento',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.ClaveEstado as v'
        )->orderBy('p.NombrePredio')->orderBy('td.NombreTipoDocumento')->get();

        $data = $resultados->map(function ($item) {
            $estado = ($item->estado_documento === 'CREADO') ? ($item->estado_accion ?? 'Pendiente') : 'FALTANTE';
            return ['x' => $item->documento, 'y' => $item->predio, 'v' => $item->v, 'estado' => $estado, 'subcategoria' => $item->subcategoria];
        });

        return response()->json($data);
    }

    public function documentosPorSubcategoriaZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $resultados = $query->select(
            'z.NombreZona as zona',
            'td.NombreTipoDocumento as documento',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.ClaveEstado as v'
        )->orderBy('z.NombreZona')->orderBy('td.NombreTipoDocumento')->get();

        $data = $resultados->map(function ($item) {
            $estado = ($item->estado_documento === 'CREADO') ? ($item->estado_accion ?? 'Pendiente') : 'FALTANTE';
            return ['x' => $item->documento, 'y' => $item->zona, 'v' => $item->v, 'estado' => $estado, 'subcategoria' => $item->subcategoria];
        });

        return response()->json($data);
    }

    public function porcentajeVigenciaPorPredio(Request $request)
    {
        // La consulta base ya tiene todos los joins, solo necesitamos aplicar filtros.
        // Nota: La consulta original tenía joins ligeramente diferentes. Se unifica al estándar.
        $query = DB::table('conf_predios as p')
            ->leftJoin('gd_obligatorios_tipo_inmueble as oti', 'p.IDTipoPredio', '=', 'oti.IDTipoInmueble')
            ->leftJoin('gd_tipos_documento as td', 'oti.IDTipoDocumento', '=', 'td.IDTipoDocumento')
            ->leftJoin('gd_categorias_doc as cd', 'td.IDCategoriaDocumento', '=', 'cd.IDCategoriaDoc')
            ->leftJoin('gd_grupos_doc as g', 'cd.IDGrupoDoc', 'g.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', fn($j) => $j->on('p.IDPredio', '=', 'd.IDPredio')->on('oti.IDTipoDocumento', '=', 'd.IDTipoDocumento'))
            ->leftJoin('track_instancias as ti', 'd.IDDocumento', '=', 'ti.IDInstancia')
            ->leftJoin('track_estados as te', 'ti.IDEstadoActualInstancia', '=', 'te.IDEstado')
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            ->leftJoin('conf_edificios as e', 'e.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('conf_niveles as n', 'n.IDEdificio', '=', 'e.IDEdificio')
            ->leftJoin('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel');

        // Aplicamos filtros WHERE
        $query->when($request->filled('id_paises'), fn($q) => $q->whereIn('p.IDPais', $request->id_paises));
        $query->when($request->filled('id_estados'), fn($q) => $q->whereIn('p.IDEstado', $request->id_estados));
        $query->when($request->filled('id_municipios'), fn($q) => $q->whereIn('p.IDMunicipio', $request->id_municipios));
        $query->when($request->filled('id_edificios'), fn($q) => $q->whereIn('e.IDEdificio', $request->id_edificios));
        $query->when($request->filled('id_niveles'), fn($q) => $q->whereIn('n.IDNivel', $request->id_niveles));
        $query->when($request->filled('id_zonas'), fn($q) => $q->whereIn('z.IDZona', $request->id_zonas));
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', $request->predio_ids)); // Corregido de id_predios a predio_ids
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', $request->grupo_ids)); // Corregido
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cd.IDCategoriaDoc', $request->categoria_ids)); // Corregido
        $query->when($request->filled('tipo_doc_ids'), fn($q) => $q->whereIn('td.IDTipoDocumento', $request->tipo_doc_ids)); // Corregido
        $query->when($request->filled('tipo_inmueble_ids'), fn($q) => $q->whereIn('p.IDTipoPredio', $request->tipo_inmueble_ids)); // Corregido

        $dataQuery = $query->select(
            'p.NombrePredio',
            DB::raw('COUNT(DISTINCT d.IDDocumento) AS TotalCreados'),
            DB::raw("SUM(CASE WHEN te.ClaveEstado = 'VENC' THEN 1 ELSE 0 END) AS TotalVencidos"),
            DB::raw("SUM(CASE WHEN d.IDDocumento IS NOT NULL AND te.ClaveEstado != 'VENC' THEN 1 ELSE 0 END) AS TotalVigentes"),
            DB::raw('COUNT(DISTINCT a.IDArchivo) AS TotalArchivos')
        )->groupBy('p.IDPredio', 'p.NombrePredio');

        // Aplicamos filtros HAVING
        $dataQuery->when($request->estado_archivo === 'con', fn($q) => $q->having('TotalArchivos', '>', 0));
        $dataQuery->when($request->estado_archivo === 'sin', fn($q) => $q->having('TotalArchivos', '=', 0));

        $data = $dataQuery->orderBy('p.NombrePredio')->get();

        $labels = $data->pluck('NombrePredio');
        $datasets = [
            ['label' => 'Vigentes', 'backgroundColor' => '#4ade80', 'data' => $data->map(fn($item) => $item->TotalCreados == 0 ? 0 : round(($item->TotalVigentes / $item->TotalCreados) * 100, 2))],
            ['label' => 'Vencidos', 'backgroundColor' => '#f87171', 'data' => $data->map(fn($item) => $item->TotalCreados == 0 ? 0 : round(($item->TotalVencidos / $item->TotalCreados) * 100, 2))]
        ];

        return response()->json(['labels' => $labels, 'datasets' => $datasets]);
    }

    public function porcentajeVigenciaPorZona(Request $request)
    {
        $query = DB::table('conf_zonas as z')
            ->leftJoin('conf_niveles as n', 'z.IDNivel', '=', 'n.IDNivel')
            ->leftJoin('conf_edificios as e', 'n.IDEdificio', '=', 'e.IDEdificio')
            ->leftJoin('conf_predios as p', 'e.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('gd_obligatorios_tipo_inmueble as oti', 'z.IDTipoZona', '=', 'oti.IDTipoInmueble')
            ->leftJoin('gd_tipos_documento as td', 'oti.IDTipoDocumento', '=', 'td.IDTipoDocumento')
            ->leftJoin('gd_categorias_doc as cd', 'td.IDCategoriaDocumento', '=', 'cd.IDCategoriaDoc')
            ->leftJoin('gd_grupos_doc as g', 'cd.IDGrupoDoc', 'g.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', fn($j) => $j->on('z.IDZona', '=', 'd.IDZona')->on('oti.IDTipoDocumento', '=', 'd.IDTipoDocumento'))
            ->leftJoin('track_instancias as ti', 'd.IDDocumento', '=', 'ti.IDInstancia')
            ->leftJoin('track_estados as te', 'ti.IDEstadoActualInstancia', '=', 'te.IDEstado')
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');

        // Filtros WHERE
        $query->when($request->filled('id_paises'), fn($q) => $q->whereIn('p.IDPais', $request->id_paises));
        $query->when($request->filled('id_estados'), fn($q) => $q->whereIn('p.IDEstado', $request->id_estados));
        $query->when($request->filled('id_municipios'), fn($q) => $q->whereIn('p.IDMunicipio', $request->id_municipios));
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', $request->predio_ids));
        $query->when($request->filled('id_edificios'), fn($q) => $q->whereIn('e.IDEdificio', $request->id_edificios));
        $query->when($request->filled('id_niveles'), fn($q) => $q->whereIn('n.IDNivel', $request->id_niveles));
        $query->when($request->filled('id_zonas'), fn($q) => $q->whereIn('z.IDZona', $request->id_zonas));
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', $request->grupo_ids));
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cd.IDCategoriaDoc', $request->categoria_ids));
        $query->when($request->filled('tipo_doc_ids'), fn($q) => $q->whereIn('td.IDTipoDocumento', $request->tipo_doc_ids));
        $query->when($request->filled('tipo_inmueble_ids'), fn($q) => $q->whereIn('z.IDTipoZona', $request->tipo_inmueble_ids));

        $dataQuery = $query->select(
            'z.NombreZona',
            DB::raw('COUNT(DISTINCT d.IDDocumento) AS TotalCreados'),
            DB::raw("SUM(CASE WHEN te.ClaveEstado = 'VENC' THEN 1 ELSE 0 END) AS TotalVencidos"),
            DB::raw("SUM(CASE WHEN d.IDDocumento IS NOT NULL AND te.ClaveEstado != 'VENC' THEN 1 ELSE 0 END) AS TotalVigentes"),
            DB::raw('COUNT(DISTINCT a.IDArchivo) AS TotalArchivos')
        )->groupBy('z.IDZona', 'z.NombreZona');

        // Filtros HAVING
        $dataQuery->when($request->estado_archivo === 'con', fn($q) => $q->having('TotalArchivos', '>', 0));
        $dataQuery->when($request->estado_archivo === 'sin', fn($q) => $q->having('TotalArchivos', '=', 0));

        $data = $dataQuery->orderBy('z.NombreZona')->get();

        $labels = $data->pluck('NombreZona');
        $datasets = [
            ['label' => 'Vigentes', 'backgroundColor' => '#4ade80', 'data' => $data->map(fn($item) => $item->TotalCreados == 0 ? 0 : round(($item->TotalVigentes / $item->TotalCreados) * 100, 2))],
            ['label' => 'Vencidos', 'backgroundColor' => '#f87171', 'data' => $data->map(fn($item) => $item->TotalCreados == 0 ? 0 : round(($item->TotalVencidos / $item->TotalCreados) * 100, 2))]
        ];

        return response()->json(['labels' => $labels, 'datasets' => $datasets]);
    }

    public function tablaDetalladaVigencia(Request $request)
    {
        $query = $this->construirQueryBasePredios();
        $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as categoria',
            'cat.NombreCategoriaDoc as subcategoria',
            'td.NombreTipoDocumento as tipo_documento',
            'd.DescripcionDocumento as nombre_documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            DB::raw("CASE WHEN a.IDObjetoPadreArchivo IS NOT NULL THEN 1 ELSE 0 END as archivo_adjunto")
        )
            ->orderBy('p.NombrePredio')
            ->orderBy('g.NombreGrupoDoc')
            ->orderBy('cat.NombreCategoriaDoc')
            ->orderBy('td.NombreTipoDocumento')
            ->get();

        return response()->json($resultados);
    }

    public function tablaDetalladaVigenciaPorZona(Request $request)
    {
        $query = $this->construirQueryBaseZonas();
        $this->aplicarFiltrosComunesZona($query, $request);

        $resultados = $query->select(
            'z.NombreZona as zona',
            'g.NombreGrupoDoc as categoria',
            'cat.NombreCategoriaDoc as subcategoria',
            'td.NombreTipoDocumento as tipo_documento',
            'd.DescripcionDocumento as nombre_documento',

            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            DB::raw("CASE WHEN a.IDObjetoPadreArchivo IS NOT NULL THEN 1 ELSE 0 END as archivo_adjunto")
        )
            ->orderBy('z.NombreZona')
            ->orderBy('g.NombreGrupoDoc')
            ->orderBy('cat.NombreCategoriaDoc')
            ->orderBy('td.NombreTipoDocumento')
            ->get();

        return response()->json($resultados);
    }


    public function exportarReporteDocumentos(Request $request)
    {
        $fecha = now()->format('Y-m-d');

        return Excel::download(new DocumentosExport($request), "reporte_documentos_{$fecha}.xlsx");
    }


    //======================================================================
    // ENDPOINTS PARA DASHBOARD DE RENOVACIÓN
    //======================================================================


    //======================================================================
    // LÓGICA DE CONSULTAS Y FILTROS REUTILIZABLE
    //======================================================================

    public function exportarExcelRenovacion(Request $request)
    {
        $fecha = now()->format('Y-m-d');
        $nombreArchivo = "reporte_renovacion_{$fecha}.xlsx";

        // Pasamos la request completa a la clase que se encargará de generar el Excel.
        return Excel::download(new ReporteRenovacionExport($request), $nombreArchivo);
    }

    private function buildBaseQuery($focus)
    {
        if ($focus === 'documentos') {
            return DB::table('gd_documentos as d')
                ->join('conf_predios as p', 'p.IDPredio', '=', 'd.IDPredio')
                ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'd.IDTipoDocumento')
                ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
                ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
                ->join('plan_acciones_renovacion as ar', 'ar.IDDocumento', '=', 'd.IDDocumento')
                ->join('plan_planes as pp', 'ar.IDPlan', '=', 'pp.IDPlan')
                ->leftJoin('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
                ->leftJoin('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
                ->leftJoin('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel');
        }

        // Default to actions query
        return DB::table('plan_acciones_renovacion as ar')
            ->join('gd_documentos as d', 'd.IDDocumento', '=', 'ar.IDDocumento')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'd.IDPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'd.IDTipoDocumento')
            ->join('plan_planes as pp', 'ar.IDPlan', '=', 'pp.IDPlan')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->leftJoin('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel');
    }

    private function applyCommonFilters($query, Request $request)
    {
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', $request->predio_ids));
        $query->when($request->filled('edificio_ids'), fn($q) => $q->whereIn('ed.IDEdificio', $request->edificio_ids));
        $query->when($request->filled('nivel_ids'), fn($q) => $q->whereIn('n.IDNivel', $request->nivel_ids));
        $query->when($request->filled('zona_ids'), fn($q) => $q->whereIn('z.IDZona', $request->zona_ids));
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', $request->grupo_ids));
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cat.IDCategoriaDoc', $request->categoria_ids));
        $query->when($request->filled('plan_ids'), fn($q) => $q->whereIn('pp.IDPlan', $request->plan_ids));

        // El filtro de fecha se aplica a la acción, que es el evento principal
        $query->when($request->filled('fecha_inicio'), fn($q) => $q->where('ar.FechaInicioAccion', '>=', $request->fecha_inicio));
        $query->when($request->filled('fecha_fin'), fn($q) => $q->where('ar.FechaInicioAccion', '<=', $request->fecha_fin));

        return $query;
    }


    /**
     * [GRÁFICA ACCIONES]
     */
    public function resumenEstadoAccionesRenovacion(Request $request)
    {
        $query = $this->buildBaseQuery('acciones')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'ar.IDAccionesRenovacion')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        $this->applyCommonFilters($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            DB::raw("COALESCE(s.NombreEstado, 'Sin estado') as estado"),
            DB::raw('COUNT(DISTINCT ar.IDAccionesRenovacion) as acciones')
        )->groupBy('p.NombrePredio', 's.NombreEstado')->orderBy('p.NombrePredio')->get();

        return response()->json($resultados);
    }

    /**
     * [TABLA ACCIONES]
     */
    public function detalleEstadoAccionesRenovacion(Request $request)
    {
        $query = $this->buildBaseQuery('acciones')
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'ar.IDOrdenTrabajo')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'ar.IDAccionesRenovacion')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        $this->applyCommonFilters($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'd.DescripcionDocumento as nombre_documento',
            'td.NombreTipoDocumento as tipo_documento',
            'pp.NombrePlan as nombre_plan_accion',
            'ot.NumOT as num_ot',
            'ar.FechaInicioAccion as fecha_inicio',
            'ar.FechaFinAccion as fecha_fin',
            DB::raw("COALESCE(s.NombreEstado, 'Sin estado') as estado")
        )->distinct()->orderBy('p.NombrePredio')->orderBy('d.DescripcionDocumento')->get();

        return response()->json($resultados);
    }

    /**
     * [GRÁFICA OTS]
     */
    public function resumenEstadoOTsRenovacion(Request $request)
    {
        $query = $this->buildBaseQuery('acciones')
            ->join('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'ar.IDOrdenTrabajo')
            ->leftJoin('track_instancias as ti_ot', 'ti_ot.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as s_ot', fn($j) => $j->on('s_ot.IDEstado', '=', 'ti_ot.IDEstadoActualInstancia')->where('s_ot.IDFlujo', 3));

        $this->applyCommonFilters($query, $request);

        // Aplicamos los filtros de fecha manualmente, apuntando a la tabla 'ot'
        $query->when($request->filled('fecha_inicio'), fn($q) => $q->where('ot.FechaIniOrdenTrabajo', '>=', $request->fecha_inicio));
        $query->when($request->filled('fecha_fin'), fn($q) => $q->where('ot.FechaIniOrdenTrabajo', '<=', $request->fecha_fin));

        $resultados = $query->select(
            'p.NombrePredio as predio',
            DB::raw("COALESCE(s_ot.NombreEstado, 'Sin estado OT') as estado"),
            DB::raw('COUNT(DISTINCT ot.IDOT) as acciones')
        )->groupBy('p.NombrePredio', 's_ot.NombreEstado')->orderBy('p.NombrePredio')->get();

        return response()->json($resultados);
    }

    /**
     * [TABLA OTS]
     */
    public function detalleEstadoOTsRenovacion(Request $request)
    {
        $query = $this->buildBaseQuery('acciones')
            ->join('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'ar.IDOrdenTrabajo')
            ->leftJoin('ot_tipo_orden_trabajo as tot', 'tot.IDTipoOT', '=', 'ot.IDTipoOT')
            ->leftJoin('track_instancias as ti_ot', 'ti_ot.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as s_ot', fn($j) => $j->on('s_ot.IDEstado', '=', 'ti_ot.IDEstadoActualInstancia')->where('s_ot.IDFlujo', 3));

        // Aplicamos filtros comunes, ignorando las fechas de la acción para usar las de la OT
        $this->applyCommonFilters($query, $request, ['fecha_inicio', 'fecha_fin']);

        // Aplicamos los filtros de fecha manualmente, apuntando a la tabla 'ot'
        $query->when($request->filled('fecha_inicio'), fn($q) => $q->where('ot.FechaIniOrdenTrabajo', '>=', $request->fecha_inicio));
        $query->when($request->filled('fecha_fin'), fn($q) => $q->where('ot.FechaIniOrdenTrabajo', '<=', $request->fecha_fin));

        $detalles = $query->select(
            'p.NombrePredio as predio',
            'ot.DescripcionOt as nombre_ot', // Corregido para usar NombreOT
            'ot.DescripcionOt as descripcion_ot',
            'tot.NombreTipoOT as tipo_ot',
            'pp.NombrePlan as nombre_accion',
            'ot.NumOT as numero_ot',
            'd.DescripcionDocumento as nombre_documento',
            DB::raw("COALESCE(s_ot.NombreEstado, 'Sin estado OT') as estado"),
            'ot.FechaIniOrdenTrabajo as fecha_inicio',
            'ot.FechaFinOT as fecha_fin'
        )->distinct()->orderBy('p.NombrePredio')->orderBy('ot.NumOT')->get();

        // Mapeo final para que coincida 1:1 con la estructura de Mantenimiento
        $data = $detalles->map(fn($r) => [
            'predio' => $r->predio,
            'nombre' => $r->nombre_ot, // 'nombre' viene de 'nombre_ot'
            'descripcion_ot' => $r->descripcion_ot,
            'tipo_ot' => $r->tipo_ot ?? 'No especificado',
            'nombre_accion' => $r->nombre_accion,
            'numero_ot' => $r->numero_ot,
            'estado' => $r->estado,
            'fecha_inicio' => $r->fecha_inicio,
            'fecha_fin' => $r->fecha_fin,
            'nombre_documento' => $r->nombre_documento
        ]);

        return response()->json($data);
    }


    /**
     * [GRÁFICA DOCUMENTOS]
     */
    public function resumenEstadoDocumentos(Request $request)
    {
        $query = $this->buildBaseQuery('documentos')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        $this->applyCommonFilters($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            DB::raw("COALESCE(s.NombreEstado, 'Sin estado de vigencia') as estado"),
            DB::raw('COUNT(DISTINCT d.IDDocumento) as acciones')
        )->groupBy('p.NombrePredio', 's.NombreEstado')->orderBy('p.NombrePredio')->get();

        return response()->json($resultados);
    }

    /**
     * [TABLA DOCUMENTOS] Detalle de estado de vigencia.
     */
    /**
     * [TABLA DOCUMENTOS] Detalle de estado de vigencia (versión corregida y lógica).
     */
    public function detalleEstadoDocumentos(Request $request)
    {
        $query = $this->buildBaseQuery('documentos')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        $this->applyCommonFilters($query, $request);

        $resultados = $query->select(
            // Campos de Ubicación (igual que en Mantenimiento)
            'p.NombrePredio as predio',
            'ed.NombreEdificio as edificio',
            'n.NombreNivel as nivel',
            'z.NombreZona as zona',

            // Campos Lógicos (equivalentes a Mantenimiento)
            'td.ClaveTipoDocumento as codigo_documento',     // Equivalente a 'codigo_equipo'
            'd.DescripcionDocumento as nombre_documento', // Equivalente a 'equipo'
            'td.NombreTipoDocumento as tipo_documento', // Equivalente a 'tipo_equipo'
            'cat.NombreCategoriaDoc as subcategoria',
            'g.NombreGrupoDoc as categoria',
            'pp.NombrePlan as plan',
            's.IDEstado as estado_id',
            DB::raw("COALESCE(s.NombreEstado, 'Sin estado') as estado")

        )->distinct()->orderBy('p.NombrePredio')->orderBy('d.DescripcionDocumento')->get();

        // No es necesario un mapeo adicional, la consulta ya genera la estructura correcta.
        return response()->json($resultados);
    }
}
