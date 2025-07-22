<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ReportePorPrediosRequest;

class ReporteDocumentosController extends Controller
{
    public function listarPredios()
    {
        $predios = DB::table('conf_predios')
            ->select('IDPredio', 'NombrePredio')
            ->where('Borrado', 0)
            ->orderBy('NombrePredio')
            ->get();
        return response()->json($predios);
    }


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
        // Ahora valida un arreglo de IDs de grupo
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
            // Mismos joins que el query anterior para llegar hasta la jerarquía
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
        // La consulta une todas las tablas necesarias para obtener una visión completa.
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


    public function matrizPorSubcategoria(Request $request)
    {
        $predioIds = $request->input('predio_ids', []);

        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            });

        // Si se envían IDs, aplicamos el filtro
        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc')
            ->get();

        $data = $resultados->map(function ($item) {
            return [
                'x' => $item->subcategoria,
                'y' => $item->predio,
                'v' => (float) $item->cumplimiento,
            ];
        });

        return response()->json($data);
    }

    public function matrizArchivosPorSubcategoria(Request $request)
    {
        $predioIds = $request->input('predio_ids', []);

        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');

        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'cat.NombreCategoriaDoc as subcategoria',
            DB::raw('CASE WHEN COUNT(a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'cat.NombreCategoriaDoc')
            ->get();

        $data = $resultados->map(fn($item) => [
            'x' => $item->subcategoria,
            'y' => $item->predio,
            'v' => (int) $item->cumplimiento
        ]);

        return response()->json($data);
    }

    public function calificacionesPonderadasPorPredio(Request $request)
    {
        $predioIds = $request->input('predio_ids', []);

        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'do.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', function ($join) {
                $join->on('a.IDObjetoPadreArchivo', '=', 'd.IDDocumento');
            });

        // Si se envían IDs, aplicamos el filtro
        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }

        $datos = $query->select(
            'p.NombrePredio as predio',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento_creados'),
            DB::raw('ROUND((COUNT(DISTINCT a.IDArchivo) * 100.0) / COUNT(*), 2) as cumplimiento_anexados')
        )
            ->groupBy('p.IDPredio', 'p.NombrePredio')
            ->orderBy('p.NombrePredio')
            ->get();

        // La transformación de datos no cambia
        $labels = $datos->pluck('predio');
        $creados = $datos->pluck('cumplimiento_creados');
        $anexados = $datos->pluck('cumplimiento_anexados');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => '% Documentos creados',
                    'data' => $creados,
                    'backgroundColor' => '#4ade80' // Verde
                ],
                [
                    'label' => '% Con archivo',
                    'data' => $anexados,
                    'backgroundColor' => '#60a5fa' // Azul
                ]
            ]
        ]);
    }

    public function matrizPorGrupo(Request $request)
    {
        $predioIds = $request->input('predio_ids', []);

        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            });

        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('ROUND((COUNT(d.IDDocumento) * 100.0) / COUNT(*), 2) as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'g.NombreGrupoDoc')
            ->get();

        $data = $resultados->map(fn($item) => [
            'x' => $item->grupo,
            'y' => $item->predio,
            'v' => (float) $item->cumplimiento
        ]);

        return response()->json($data);
    }

    public function matrizArchivosPorGrupo(Request $request)
    {
        $predioIds = $request->input('predio_ids', []);

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

        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'g.NombreGrupoDoc as grupo',
            DB::raw('CASE WHEN COUNT(a.IDArchivo) > 0 THEN 100 ELSE 0 END as cumplimiento')
        )
            ->groupBy('p.NombrePredio', 'g.NombreGrupoDoc')
            ->get();

        $data = $resultados->map(fn($item) => [
            'x' => $item->grupo,
            'y' => $item->predio,
            'v' => (int) $item->cumplimiento
        ]);

        return response()->json($data);
    }

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
}
