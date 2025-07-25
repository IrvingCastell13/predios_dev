<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ReportePorPrediosRequest;

class ReporteDocumentosController extends Controller
{

    /**
     * Devuelve una lista de Predios.
     */
    public function listarPredios()
    {
        $predios = DB::table('conf_predios')
            ->select('IDPredio', 'NombrePredio')
            ->where('Borrado', 0)
            ->orderBy('NombrePredio')
            ->get();
        return response()->json($predios);
    }

    /**
     * Devuelve una lista de Categorías de Documentos filtrada por un Grupo.
     */
    public function listarGruposDoc()
    {
        $grupos = DB::table('gd_grupos_doc')
            ->select('IDGrupoDoc', 'NombreGrupoDoc')
            ->where('Borrado', 0)
            ->orderBy('NombreGrupoDoc')
            ->get();
        return response()->json($grupos);
    }

    /**
     * Devuelve una lista de Categorías de Documentos (Subcategorías) filtrada por un Grupo.
     */
    public function listarCategoriasDoc(Request $request)
    {
        $request->validate(['grupo_ids' => 'required|array']);
        $grupoIds = $request->input('grupo_ids');

        if (empty($grupoIds)) {
            return response()->json([]);
        }

        $categorias = DB::table('gd_categorias_doc')
            ->whereIn('IDGrupoDoc', $grupoIds) // Usa whereIn
            ->select('IDCategoriaDoc', 'NombreCategoriaDoc', 'IDGrupoDoc') // Devolvemos IDGrupoDoc para la lógica del frontend
            ->where('Borrado', 0)
            ->orderBy('NombreCategoriaDoc')
            ->get();
        return response()->json($categorias);
    }

    /**
     * Devuelve una lista de Tipos de documentos.
     */
    public function listarTiposDocumento()
    {
        $items = DB::table('gd_tipos_documento')
            ->select('IDTipoDocumento', 'NombreTipoDocumento')
            ->where('Borrado', 0)
            ->orderBy('NombreTipoDocumento')
            ->get();
        return response()->json($items);
    }

    /**
     * Devuelve una lista de Tipos de imbuebles.
     */
    public function listarTiposInmueble()
    {
        // Nota: Basado en tus queries, la tabla correcta es conf_tipos_predio
        $items = DB::table('conf_tipos_predio')
            ->select('IDTipoPredio as IDTipoInmueble', 'NombreTipoPredio as NombreTipoInmueble')
            ->where('Borrado', 0)
            ->orderBy('NombreTipoInmueble')
            ->get();
        return response()->json($items);
    }

    /**
     * Esta funcion se llama normalmente cuando se hacen filtros en Dashnboar Reportes.
     */
    private function aplicarFiltrosComunes($query, Request $request)
    {
        $predioIds = $request->input('predio_ids', []);
        $grupoIds = $request->input('grupo_ids', []);
        $categoriaIds = $request->input('categoria_ids', []);
        $tipoDocIds = $request->input('tipo_doc_ids', []); // <-- Nuevo
        $tipoInmuebleIds = $request->input('tipo_inmueble_ids', []); // <-- Nuevo

        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }
        if (!empty($grupoIds)) {
            $query->whereIn('g.IDGrupoDoc', $grupoIds);
        }
        if (!empty($categoriaIds)) {
            $query->whereIn('cat.IDCategoriaDoc', $categoriaIds);
        }
        if (!empty($tipoDocIds)) {
            $query->whereIn('td.IDTipoDocumento', $tipoDocIds);
        }
        if (!empty($tipoInmuebleIds)) {
            $query->whereIn('p.IDTipoPredio', $tipoInmuebleIds);
        }

        return $query;
    }

    //INICIO ENPOINTS
    public function estadoPorPredio(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        // Inicia la consulta desde la tabla 'conf_predios' y le asigna el alias 'p'.
        $documentos = DB::table('conf_predios as p')

            // Une con la tabla 'gd_obligatorios_tipo_inmueble' (alias 'do') para
            // encontrar qué documentos son obligatorios para el tipo de predio.
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')

            // Une con 'gd_tipos_documento' (alias 'td') para obtener el nombre
            // de cada documento obligatorio.
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')

            // Usa un LEFT JOIN para buscar coincidencias en la tabla 'gd_documentos' (alias 'd').
            // Este join es "izquierdo" para mantener todos los documentos obligatorios en la lista,
            // incluso si no encuentran una coincidencia en la tabla de documentos creados.
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->whereIn('p.IDPredio', $predioIds) // El filtro se mantiene igual
            // Define las columnas que se van a mostrar en el resultado.
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'td.IDTipoDocumento as tipo_documento_id',
                'td.NombreTipoDocumento as nombre_documento',

                // Inserta una lógica SQL pura: si se encontró un ID de documento ('d.IDDocumento'),
                // la columna 'estado' será 'CREADO'. Si no (es NULL), será 'FALTANTE'.
                DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado")
            )

            // Ordena los resultados para una mejor visualización.
            ->orderBy('p.IDPredio')
            ->orderBy('td.IDTipoDocumento')

            // Ejecuta la consulta y devuelve todos los resultados.
            ->get();


        return response()->json($documentos);
    }

    public function documentosPorPredioConArchivos(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];
        // La base del query es la misma que el query de la funcion estadoPorPredio()
        $documentos  = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })

            // Añadimos un nuevo LEFT JOIN a la tabla de archivos ('arch_archivos' con alias 'a').
            // La unión se hace donde el ID del documento ('d.IDDocumento') coincida
            // con el campo que lo relaciona en la tabla de archivos ('a.IDObjetoPadreArchivo').
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')

            ->whereIn('p.IDPredio', $predioIds)
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'td.IDTipoDocumento as tipo_documento_id',
                'td.NombreTipoDocumento as nombre_documento',
                DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado"),


                // Añadimos una segunda lógica CASE para determinar el estado del archivo.
                DB::raw("CASE 
                    WHEN d.IDDocumento IS NULL THEN 'SIN DOCUMENTO' 
                    WHEN a.IDArchivo IS NOT NULL THEN 'CON ARCHIVO' 
                    ELSE 'SIN ARCHIVO' 
                 END as archivo")

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
            // Joins iniciales para obtener los documentos obligatorios
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')


            // Unimos con las categorías de documento...
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            // ...y luego con los grupos de documento (el nivel más alto).
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')


            // Left Join para verificar cuáles documentos existen en el inventario.
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
                // COUNT(*) cuenta el total de documentos requeridos en el grupo.
                // Cuenta cuántos documentos obligatorios le corresponden a un predio específico dentro de un grupo específico.
                DB::raw('COUNT(*) as total_esperados'),
                // COUNT(d.IDDocumento) solo cuenta los que encontraron coincidencia (los creados).
                DB::raw('COUNT(d.IDDocumento) as creados'),
                // La resta nos da los faltantes.
                DB::raw('COUNT(*) - COUNT(d.IDDocumento) as faltantes')
            )
            // Agrupa todas las filas en un solo resumen por cada combinación de predio y grupo.
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
            // La selección de columnas ahora se enfoca en la categoría
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio_nombre',
                'cat.IDCategoriaDoc as categoria_id',
                'cat.NombreCategoriaDoc as nombre_categoria',
                DB::raw('COUNT(*) as total_esperados'),
                DB::raw('COUNT(d.IDDocumento) as creados'),
                DB::raw('COUNT(*) - COUNT(d.IDDocumento) as faltantes'),


                // Se añade el cálculo del porcentaje de cumplimiento.
                // (Creados * 100) / Total Esperados, redondeado a 2 decimales.
                DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_porcentaje')

            )
            // La agrupación ahora es por predio y por categoría de documento.
            ->groupBy('p.IDPredio', 'p.NombrePredio', 'cat.IDCategoriaDoc', 'cat.NombreCategoriaDoc')
            ->orderBy('p.IDPredio')
            ->orderBy('cat.IDCategoriaDoc')
            ->get();

        return response()->json($resumen);
    }

    public function cumplimientoPonderadoTotal(ReportePorPrediosRequest $request)
    {
        $predioIds = $request->validated()['predio_ids'];

        $ponderado  = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'do.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')

            ->whereIn('p.IDPredio', $predioIds)

            // Usamos selectRaw para escribir toda la cláusula SELECT con sus cálculos.
            // No hay GROUP BY, por lo que los conteos se aplican a todo el conjunto de datos.
            ->selectRaw("
                COUNT(*) as total_esperados,
                COUNT(d.IDDocumento) as creados,
                COUNT(DISTINCT a.IDArchivo) as con_archivo,
                ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_creados,
                ROUND((COUNT(DISTINCT a.IDArchivo) * 100.0) / COUNT(*), 2) as cumplimiento_anexados
            ")
            // Usamos first() porque sabemos que esta consulta SIEMPRE devolverá una sola fila.
            ->first();


        return response()->json($ponderado);
    }
    //FIN ENPOINTS

    //INICIO DE ENPOINTS PARA DASHBOARD REPORTES (TAB CUMPLIMIENTO GRAFICAS)
    public function matrizPorSubcategoria(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            });

        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc')
            ->get();

        return response()->json($resultados->map(fn($item) => [
            'x' => $item->subcategoria,
            'y' => $item->predio,
            'v' => (float) $item->cumplimiento
        ]));
    }

    public function matrizArchivosPorSubcategoria(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');

        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc')
            ->get();

        return response()->json($resultados->map(fn($item) => [
            'x' => $item->subcategoria,
            'y' => $item->predio,
            'v' => (int) $item->cumplimiento
        ]));
    }

    public function matrizPorGrupo(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            });

        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'g.NombreGrupoDoc')
            ->get();

        return response()->json($resultados->map(fn($item) => [
            'x' => $item->grupo,
            'y' => $item->predio,
            'v' => (float) $item->cumplimiento
        ]));
    }

    public function matrizArchivosPorGrupo(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');

        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'g.NombreGrupoDoc')
            ->get();

        return response()->json($resultados->map(fn($item) => [
            'x' => $item->grupo,
            'y' => $item->predio,
            'v' => (int) $item->cumplimiento
        ]));
    }

    public function calificacionesPonderadasPorPredio(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'do.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', function ($join) {
                $join->on('a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');
            });

        $query = $this->aplicarFiltrosComunes($query, $request);

        $datos = $query->select(
            'p.NombrePredio as predio',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_creados'),
            DB::raw('ROUND((COUNT(DISTINCT a.IDArchivo) * 100.0) / COUNT(*), 2) as cumplimiento_anexados')
        )
            ->groupBy('p.IDPredio', 'p.NombrePredio')
            ->orderBy('p.NombrePredio')
            ->get();

        $labels = $datos->pluck('predio');
        $creados = $datos->pluck('cumplimiento_creados');
        $anexados = $datos->pluck('cumplimiento_anexados');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => '% Documentos creados', 'data' => $creados, 'backgroundColor' => '#4ade80'],
                ['label' => '% Con archivo', 'data' => $anexados, 'backgroundColor' => '#60a5fa']
            ]
        ]);
    }
    //FIN DE ENPOINTS PARA DASHBOARD REPORTES (TAB CUMPLIMIENTO GRAFICAS)


    //INICIO DE ENPOINTS PARA DASHBOARD RENOVACION (3 GRAFICAS)
    public function estadoAccionesPorRenovacion(Request $request)
    {
        $request->validate([
            'predio_ids'    => 'sometimes|array',
            'grupo_ids'      => 'sometimes|array',
            'categoria_ids'  => 'sometimes|array',
            'fecha_inicio'  => 'sometimes|nullable|date',
            'fecha_fin'     => 'sometimes|nullable|date',
        ]);

        $predioIds = $request->input('predio_ids', []);
        $grupoIds = $request->input('grupo_ids', []);
        $categoriaIds = $request->input('categoria_ids', []);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = DB::table('plan_acciones_renovacion as ar')
            ->join('gd_documentos as d', 'd.IDDocumento', '=', 'ar.IDDocumento')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'd.IDTipoDocumento')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'd.IDPredio')
            // Nuevos joins para poder filtrar
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'ar.IDOrdenTrabajo')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        // Aplicamos todos los filtros si se proporcionan
        // Usa whereIn para filtrar por múltiples grupos y categorías
        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }
        if (!empty($grupoIds)) {
            $query->whereIn('g.IDGrupoDoc', $grupoIds);
        }
        if (!empty($categoriaIds)) {
            $query->whereIn('cat.IDCategoriaDoc', $categoriaIds);
        }
        if ($fechaInicio) {
            $query->where('ar.FechaInicioAccion', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->where('ar.FechaFinAccion', '<=', $fechaFin);
        }


        $resultados = $query->select(
            'p.NombrePredio as predio',
            'td.NombreTipoDocumento as documento',
            'ar.IDAccionesRenovacion as id_accion',
            's.NombreEstado as estado',
            's.IDEstado as estado_id',
            'ar.FechaInicioAccion as fecha_inicio',
            'ar.FechaFinAccion as fecha_fin',
            DB::raw("CASE WHEN ar.IDOrdenTrabajo IS NOT NULL THEN 1 ELSE 0 END as tiene_ot")
        )
            ->groupBy(
                'p.NombrePredio',
                'td.NombreTipoDocumento',
                'ar.IDAccionesRenovacion',
                's.NombreEstado',
                's.IDEstado',
                'ar.FechaInicioAccion',
                'ar.FechaFinAccion',
                'ar.IDOrdenTrabajo'
            )
            ->orderBy('p.NombrePredio')->orderBy('td.NombreTipoDocumento')
            ->get();

        $data = $resultados->map(fn($r) => [
            'x' => $r->documento,
            'y' => $r->predio,
            'v' => $r->estado_id,
            'estado' => $r->estado ?? 'Sin estado',
            'id_accion' => $r->id_accion,
            'tiene_ot' => (int) $r->tiene_ot,
            'fecha_inicio' => $r->fecha_inicio,
            'fecha_fin' => $r->fecha_fin
        ]);

        return response()->json($data);
    }
    //FIN DE ENPOINTS PARA DASHBOARD RENOVACION  (3 GRAFICAS)


    //INCIO DE ENPOINTS PARA DASHBOARD REPORTES (TAB VIGENCIA GRAFICAS)
    public function documentosConEstadoPorCategoria(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        // Reutilizamos la función que ya teníamos para aplicar los filtros
        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as categoria',
            'td.NombreTipoDocumento as documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.IDEstado as estado_id' // Obtenemos el ID del estado para el valor 'v'
        )
            ->orderBy('p.NombrePredio')
            ->orderBy('g.NombreGrupoDoc')
            ->orderBy('td.NombreTipoDocumento')
            ->get();

        // Mapeamos los resultados al formato {x, y, v} que la gráfica de matriz espera.
        $data = $resultados->map(function ($item) {

            $estado = 'FALTANTE';
            if ($item->estado_documento === 'CREADO') {
                // Si el documento está creado, usamos su estado de acción.
                // Si no tiene estado de acción, lo marcamos como 'Pendiente' por defecto.
                $estado = $item->estado_accion ?? 'Pendiente';
            }

            return [
                'x' => $item->documento, // Eje X de la matriz
                'y' => $item->predio,    // Eje Y de la matriz
                'v' => $item->estado_id, // Valor numérico/ID para el color
                'estado' => $estado      // Texto para el tooltip
            ];
        });

        return response()->json($data);
    }

    public function documentosPorSubcategoria(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        // Reutilizamos la misma función para aplicar todos los filtros
        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria', // El cambio principal es agrupar por subcategoría
            'td.NombreTipoDocumento as documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion',
            's.IDEstado as estado_id'
        )
            ->orderBy('p.NombrePredio')
            ->orderBy('cat.NombreCategoriaDoc')
            ->orderBy('td.NombreTipoDocumento')
            ->get();

        // Mapeamos los resultados al formato {x, y, v} que la matriz necesita
        $data = $resultados->map(function ($item) {
            $estado = 'FALTANTE';
            if ($item->estado_documento === 'CREADO') {
                $estado = $item->estado_accion ?? 'Pendiente';
            }

            return [
                'x' => $item->documento,
                'y' => $item->predio,
                'v' => $item->estado_id,
                'estado' => $estado,
                // Añadimos la subcategoría para poder agrupar en el frontend si es necesario
                'subcategoria' => $item->subcategoria
            ];
        });

        return response()->json($data);
    }

    public function tablaDetalladaVigencia(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', 'd.IDDocumento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        // Reutilizamos la función de filtros comunes
        $query = $this->aplicarFiltrosComunes($query, $request);

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as categoria',
            'cat.NombreCategoriaDoc as subcategoria',
            'td.NombreTipoDocumento as tipo_documento',
            DB::raw("CASE WHEN d.IDDocumento IS NOT NULL THEN 'CREADO' ELSE 'FALTANTE' END as estado_documento"),
            's.NombreEstado as estado_accion'
        )
            ->orderBy('p.NombrePredio')
            ->orderBy('g.NombreGrupoDoc')
            ->orderBy('cat.NombreCategoriaDoc')
            ->orderBy('td.NombreTipoDocumento')
            ->get();

        // Para la tabla, no necesitamos mapear el resultado, lo enviamos tal cual.
        return response()->json($resultados);
    }

    public function porcentajeVigenciaPorPredio(Request $request)
    {
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as oti', 'p.IDTipoPredio', '=', 'oti.IDTipoInmueble')
            ->join('gd_tipos_documento as td', 'oti.IDTipoDocumento', '=', 'td.IDTipoDocumento') // Necesario para filtrar por grupo/categoría
            ->join('gd_categorias_doc as cd', 'td.IDCategoriaDocumento', '=', 'cd.IDCategoriaDoc') // Necesario para filtrar por grupo
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('p.IDPredio', '=', 'd.IDPredio')
                    ->on('oti.IDTipoDocumento', '=', 'd.IDTipoDocumento');
            })
            ->leftJoin('track_instancias as ti', 'd.IDDocumento', '=', 'ti.IDInstancia')
            ->leftJoin('track_estados as te', 'ti.IDEstadoActualInstancia', '=', 'te.IDEstado');

        // Aplicar filtros dinámicamente ANTES de ejecutar la consulta
        $query->when($request->filled('id_predios'), function ($q) use ($request) {
            return $q->whereIn('p.IDPredio', $request->id_predios);
        });

        $query->when($request->filled('id_grupos_doc'), function ($q) use ($request) {
            return $q->whereIn('cd.IDGrupoDoc', $request->id_grupos_doc);
        });

        $query->when($request->filled('id_categorias_doc'), function ($q) use ($request) {
            return $q->whereIn('cd.IDCategoriaDoc', $request->id_categorias_doc);
        });

        $query->when($request->filled('id_tipos_documento'), function ($q) use ($request) {
            return $q->whereIn('td.IDTipoDocumento', $request->id_tipos_documento);
        });

        $query->when($request->filled('id_tipos_inmueble'), function ($q) use ($request) {
            return $q->whereIn('p.IDTipoPredio', $request->id_tipos_inmueble);
        });

        // Seleccionar, agrupar y obtener los resultados
        $data = $query->select(
            'p.NombrePredio',
            DB::raw('COUNT(DISTINCT oti.IDTipoDocumento) AS TotalObligatorios'), // Usamos DISTINCT para evitar duplicados por joins
            DB::raw('COUNT(d.IDDocumento) AS TotalCreados'),
            DB::raw("SUM(CASE WHEN te.ClaveEstado = 'VENC' THEN 1 ELSE 0 END) AS TotalVencidos"),
            DB::raw("SUM(CASE WHEN d.IDDocumento IS NOT NULL AND (te.ClaveEstado IS NULL OR te.ClaveEstado <> 'VENC') THEN 1 ELSE 0 END) AS TotalVigentes")
        )
            ->groupBy('p.NombrePredio')
            ->having('TotalCreados', '>', 0)
            ->orderBy('p.NombrePredio')
            ->get();

        // Formatear la respuesta
        $labels = $data->pluck('NombrePredio');

        $datasets = [
            [
                'label' => 'Vigentes',
                'backgroundColor' => '#4ade80',
                'data' => $data->map(function ($item) {
                    if ($item->TotalCreados == 0) return 0;
                    return round(($item->TotalVigentes / $item->TotalCreados) * 100, 2);
                })
            ],
            [
                'label' => 'Vencidos',
                'backgroundColor' => '#f87171',
                'data' => $data->map(function ($item) {
                    if ($item->TotalCreados == 0) return 0;
                    return round(($item->TotalVencidos / $item->TotalCreados) * 100, 2);
                })
            ]
        ];

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }
    //FIN DE ENPOINTS PARA DASHBOARD REPORTES (TAB VIGENCIA GRAFICAS)
}
