<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdenesTrabajoExport;

class OrdenTrabajoController extends Controller
{
    /**
     * Aplica filtros comunes a las consultas de reportes de Órdenes de Trabajo.
     */
    private function aplicarFiltrosOT($query, Request $request)
    {
        if ($request->filled('predio_ids')) {
            $query->whereIn('ot.IDPredio', $request->input('predio_ids'));
        }
        if ($request->filled('tipo_ot_ids')) {
            $query->whereIn('ot.IDTipoOT', $request->input('tipo_ot_ids'));
        }
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('ot.FechaInicioReal', [
                $request->input('fecha_inicio'),
                $request->input('fecha_fin')
            ]);
        }

        // NUEVOS filtros de ubicación
        $query->when($request->filled('edificio_ids'), fn($q) => $q->whereIn('ot.IDEdificio', $request->edificio_ids));
        $query->when($request->filled('nivel_ids'), fn($q) => $q->whereIn('ot.IDNivel', $request->nivel_ids));
        $query->when($request->filled('zona_ids'), fn($q) => $q->whereIn('ot.IDZona', $request->zona_ids));

        $query->when($request->filled('responsable_ids'), fn($q) => $q->whereIn('ot.IDPersona', $request->responsable_ids));

        return $query;
    }

    // Asumo que ya tienes estos métodos en otro controlador, si no, aquí están
    public function listarEdificios(Request $request)
    {
        $query = DB::table('conf_edificios as ed')
            ->select('ed.IDEdificio', 'ed.NombreEdificio', 'ed.IDPredio')
            ->where('ed.Borrado', 0);
        if ($request->filled('predio_ids')) {
            $query->whereIn('ed.IDPredio', (array)$request->input('predio_ids'));
        }
        return response()->json($query->orderBy('ed.NombreEdificio')->get());
    }

    public function listarNiveles(Request $request)
    {
        $query = DB::table('conf_niveles as n')
            ->select('n.IDNivel', 'n.NombreNivel', 'n.IDEdificio')
            ->where('n.Borrado', 0);
        if ($request->filled('edificio_ids')) {
            $query->whereIn('n.IDEdificio', (array)$request->input('edificio_ids'));
        }
        return response()->json($query->orderBy('n.NombreNivel')->get());
    }

    public function listarZonas(Request $request)
    {
        $query = DB::table('conf_zonas as z')
            ->select('z.IDZona', 'z.NombreZona', 'z.IDNivel')
            ->where('z.Borrado', 0);
        if ($request->filled('nivel_ids')) {
            $query->whereIn('z.IDNivel', (array)$request->input('nivel_ids'));
        }
        return response()->json($query->orderBy('z.NombreZona')->get());
    }

    public function listarResponsables()
    {
        $responsables = DB::table('ms_personas')
            ->select(
                'IDPersona',
                // Concatenamos nombre y apellidos para mostrar el nombre completo en el filtro
                DB::raw("TRIM(CONCAT(COALESCE(NombrePersona, ''), ' ', COALESCE(ApellidoPaternoPersona, ''), ' ', COALESCE(ApellidoMaternoPersona, ''))) as NombreCompleto")
            )
            ->where('Borrado', 0)
            ->orderBy('NombreCompleto')
            ->get();
        return response()->json($responsables);
    }

    private function addLocationJoins($query)
    {
        // El join con predio ya suele estar, pero lo aseguramos.
        // Asumimos que la OT siempre tiene un IDPredio.
        // Los joins a niveles inferiores son LEFT JOIN por si la OT es a nivel de Predio o Edificio.
        $query->leftJoin('conf_edificios as ed', function ($join) {
            $join->on('ed.IDEdificio', '=', 'ot.IDEdificio');
        })
            ->leftJoin('conf_niveles as n', function ($join) {
                $join->on('n.IDNivel', '=', 'ot.IDNivel');
            })
            ->leftJoin('conf_zonas as z', function ($join) {
                $join->on('z.IDZona', '=', 'ot.IDZona');
            });

        return $query;
    }

    /**
     * Devuelve una lista detallada de OTs para el visor de la Gráfica 1.
     * NO agrupa los resultados.
     */
    public function getOtDetallePorPredio(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('conf_predios as p', 'ot.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as te', 'te.IDEstado', '=', 'ti.IDEstadoActualInstancia')
            ->select(
                'p.NombrePredio',
                'ot.IDOT',
                'ot.DescripcionOt',
                'ot.FechaIniOrdenTrabajo',
                'ot.FechaInicioReal',
                'ot.FechaFinOT',
                'te.NombreEstado as estado'
            )
            ->orderBy('p.NombrePredio')
            ->orderBy('ot.FechaIniOrdenTrabajo');

        // Reutilizamos la misma función de filtros
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de Órdenes de Trabajo por predio y por estado.
     * Basado en la Gráfica 2 del documento.
     */
    public function getOtPorPredioEstado(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('conf_predios as p', 'ot.IDPredio', '=', 'p.IDPredio')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as te', 'te.IDEstado', '=', 'ti.IDEstadoActualInstancia')
            ->select(
                'p.NombrePredio as predio',
                DB::raw('COALESCE(te.NombreEstado, "Sin estado") as estado'),

                DB::raw('COUNT(ot.IDOT) as total')
            )
            ->groupBy('p.NombrePredio', 'te.NombreEstado')
            ->orderBy('p.NombrePredio');
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }


    /**
     * Devuelve el conteo TOTAL de OTs A Tiempo vs. Fuera de Tiempo.
     * Basado en la Gráfica 3 del documento.
     */
    public function getOtGlobalATiempo(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot');

        // Reutilizamos los filtros existentes
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->selectRaw("
                SUM(CASE WHEN FechaInicioReal IS NOT NULL AND FechaInicioReal <= FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS total_a_tiempo,
                SUM(CASE WHEN FechaInicioReal IS NOT NULL AND FechaInicioReal > FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS total_fuera_tiempo
            ")
            ->first();

        // Formateamos la respuesta exactamente como la espera la gráfica
        return response()->json([
            ['estado' => 'A Tiempo', 'total' => $resultados->total_a_tiempo ?? 0],
            ['estado' => 'Fuera de Tiempo', 'total' => $resultados->total_fuera_tiempo ?? 0]
        ]);
    }

    /**
     * Devuelve el conteo de OTs iniciadas A Tiempo vs. Fuera de Tiempo, agrupadas por predio.
     * Basado en la Gráfica 4 del documento.
     */
    public function getOtATiempoPorPredio(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('conf_predios as p', 'ot.IDPredio', '=', 'p.IDPredio')
            ->select(
                'p.NombrePredio as predio',
                // Adaptamos la lógica del docx a los nombres de columna correctos
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal <= ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS a_tiempo'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal > ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS fuera_tiempo')
            )
            ->groupBy('p.NombrePredio')
            ->orderBy('p.NombrePredio');

        // Reutilizamos la función de filtros que ya tenemos
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de OTs agrupadas por persona responsable.
     * Basado en la Gráfica 5 del documento.
     */
    public function getOtPorResponsable(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->leftJoin('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->select(
                // La función COALESCE se asegura de que si el nombre es nulo, se use "No Asignado"
                DB::raw('COALESCE(CONCAT(p.NombrePersona, " ", p.ApellidoPaternoPersona), "No Asignado") as responsable'),
                DB::raw('COUNT(ot.IDOT) as total')
            )
            // --- CAMBIO CLAVE: Agrupamos directamente por el alias 'responsable' ---
            ->groupBy('responsable')
            ->orderBy('total', 'desc');

        // La función de filtros se sigue aplicando correctamente
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de OTs por responsable y por estado.
     * Basado en la Gráfica 6 del documento.
     */
    public function getOtPorResponsableEstado(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as te', 'te.IDEstado', '=', 'ti.IDEstadoActualInstancia')
            ->select(
                DB::raw("CONCAT(p.NombrePersona, ' ', p.ApellidoPaternoPersona) as responsable"),
                DB::raw('COALESCE(te.NombreEstado, "Sin estado") as estado'),
                DB::raw('COUNT(ot.IDOT) as total')
            )
            ->groupBy('responsable', 'estado')
            ->orderBy('responsable');

        // Reutilizamos la función de filtros
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de OTs A Tiempo vs. Fuera de Tiempo por cada responsable.
     * Basado en la Gráfica 7 del documento.
     */
    public function getOtATiempoPorResponsable(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            // CAMBIO 1: Usamos LEFT JOIN para incluir OTs "No asignadas"
            ->leftJoin('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->select(
                // CAMBIO 2: Usamos COALESCE para manejar los responsables NULL
                DB::raw('COALESCE(CONCAT(p.NombrePersona, " ", p.ApellidoPaternoPersona), "No Asignado") as responsable'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal <= ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS a_tiempo'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal > ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS fuera_tiempo')
            )
            // CAMBIO 3: Agrupamos por el nombre final para consistencia
            ->groupBy('responsable')
            ->orderBy('responsable');
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de OTs agrupadas por tipo.
     * Basado en la Gráfica 8 del documento.
     */
    public function getOtPorTipo(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('ot_tipo_orden_trabajo as t', 'ot.IDTipoOT', '=', 't.IDTipoOT')
            ->select(
                't.NombreTipoOT as tipo',
                DB::raw('COUNT(ot.IDOT) as total')
            )
            ->groupBy('t.NombreTipoOT')
            ->orderBy('total', 'desc');

        // Reutilizamos la función de filtros
        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve el conteo de OTs A Tiempo vs. Fuera de Tiempo por cada tipo de OT.
     * Basado en la Gráfica 9 del documento.
     */
    public function getOtATiempoPorTipo(Request $request)
    {
        $query = DB::table('ot_orden_trabajo as ot')
            ->join('ot_tipo_orden_trabajo as t', 'ot.IDTipoOT', '=', 't.IDTipoOT')
            ->select(
                't.NombreTipoOT as tipo',
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal <= ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) as a_tiempo'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal > ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) as fuera_tiempo'),

                // --- CAMBIO CLAVE: Añadimos el conteo de las OTs sin fecha de inicio real ---
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NULL THEN 1 ELSE 0 END) as pendiente_iniciar')
            )
            ->groupBy('t.NombreTipoOT')
            ->orderBy('t.NombreTipoOT');

        $this->addLocationJoins($query);
        $this->aplicarFiltrosOT($query, $request);

        $resultados = $query->get();
        return response()->json($resultados);
    }

    /**
     * Devuelve la lista de tipos de OT para los filtros.
     */
    public function listarTiposOT()
    {
        $tipos = DB::table('ot_tipo_orden_trabajo')
            ->select('IDTipoOT', 'NombreTipoOT')
            ->where('Borrado', 0)
            ->orderBy('NombreTipoOT')
            ->get();
        return response()->json($tipos);
    }

    public function exportarExcel(Request $request)
    {
        $fecha = now()->format('Y-m-d');
        $nombreArchivo = "ordenes_de_trabajo_{$fecha}.xlsx";

        return Excel::download(new OrdenesTrabajoExport($request), $nombreArchivo);
    }
}
