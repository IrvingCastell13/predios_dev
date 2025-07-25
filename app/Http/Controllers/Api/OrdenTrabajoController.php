<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $query->whereBetween('ot.FechaIniOrdenTrabajo', [
                $request->input('fecha_inicio'),
                $request->input('fecha_fin')
            ]);
        }
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
            // Unimos con ms_personas para obtener el nombre del responsable
            ->join('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->select(
                // Concatenamos nombre y apellido para tener el nombre completo
                DB::raw("CONCAT(p.NombrePersona, ' ', p.ApellidoPaternoPersona) as responsable"),
                DB::raw('COUNT(ot.IDOT) as total')
            )
            ->groupBy('responsable') // Agrupamos por el nombre completo
            ->orderBy('total', 'desc'); // Ordenamos de mayor a menor

        // Aplicamos los filtros globales (predio, tipo, fecha)
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
            ->join('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->select(
                DB::raw("CONCAT(p.NombrePersona, ' ', p.ApellidoPaternoPersona) as responsable"),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal <= ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS a_tiempo'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal > ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS fuera_tiempo')
            )
            ->groupBy('responsable')
            ->orderBy('responsable');

        // Reutilizamos la función de filtros
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
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal <= ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS a_tiempo'),
                DB::raw('SUM(CASE WHEN ot.FechaInicioReal IS NOT NULL AND ot.FechaInicioReal > ot.FechaIniOrdenTrabajo THEN 1 ELSE 0 END) AS fuera_tiempo')
            )
            ->groupBy('t.NombreTipoOT')
            ->orderBy('t.NombreTipoOT');

        // Reutilizamos la función de filtros
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
}
