<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ReporteMantenimientoExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteEquiposController extends Controller
{
    /**
     * Devuelve una lista de Sistemas.
     */
    public function listarSistemas()
    {
        $sistemas = DB::table('in_sistemas')
            ->select('IDSistema', 'NombreSistema')
            ->where('Borrado', 0)
            ->orderBy('NombreSistema')
            ->get();
        return response()->json($sistemas);
    }

    /**
     * Devuelve una lista de Subsistemas.
     */

    public function listarSubsistemas(Request $request)
    {
        // Cambiamos 'required|array' por 'sometimes|array' para que sea opcional.
        $request->validate(['sistema_ids' => 'sometimes|array']);
        $sistemaIds = $request->input('sistema_ids', []);

        $query = DB::table('in_subsistemas')
            // Es importante que también devuelvas el IDSistema para el filtro en el frontend.
            ->select('IDSubsistema', 'NombreSubsistema', 'IDSistema')
            ->where('Borrado', 0);

        // Solo aplicamos el filtro si el array de IDs no está vacío.
        if (!empty($sistemaIds)) {
            $query->whereIn('IDSistema', $sistemaIds);
        }

        $subsistemas = $query->orderBy('NombreSubsistema')->get();
        return response()->json($subsistemas);
    }

    public function listarPredios()
    {
        $predios = DB::table('conf_predios')
            ->select('IDPredio', 'NombrePredio')
            ->where('Borrado', 0)
            ->orderBy('NombrePredio')
            ->get();
        return response()->json($predios);
    }


    public function listarEdificios(Request $request)
    {
        $request->validate(['predio_ids' => 'sometimes|array']);
        $predioIds = $request->input('predio_ids', []);

        $query = DB::table('conf_edificios as ed')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'ed.IDPredio')
            ->select('ed.IDEdificio', 'ed.NombreEdificio', 'ed.IDPredio')
            ->where('ed.Borrado', 0);

        if (!empty($predioIds)) {
            $query->whereIn('ed.IDPredio', $predioIds);
        }

        $edificios = $query->orderBy('ed.NombreEdificio')->get();
        return response()->json($edificios);
    }

    /**
     * Devuelve una lista de Niveles. Opcionalmente filtrados por edificio.
     */
    public function listarNiveles(Request $request)
    {
        $request->validate(['edificio_ids' => 'sometimes|array']);
        $edificioIds = $request->input('edificio_ids', []);

        $query = DB::table('conf_niveles as n')
            ->join('conf_edificios as ed', 'ed.IDEdificio', '=', 'n.IDEdificio')
            ->select('n.IDNivel', 'n.NombreNivel', 'n.IDEdificio')
            ->where('n.Borrado', 0);

        if (!empty($edificioIds)) {
            $query->whereIn('n.IDEdificio', $edificioIds);
        }

        $niveles = $query->orderBy('n.NombreNivel')->get();
        return response()->json($niveles);
    }

    /**
     * Devuelve una lista de Zonas. Opcionalmente filtradas por nivel.
     */
    public function listarZonas(Request $request)
    {
        $request->validate(['nivel_ids' => 'sometimes|array']);
        $nivelIds = $request->input('nivel_ids', []);

        $query = DB::table('conf_zonas as z')
            ->join('conf_niveles as n', 'n.IDNivel', '=', 'z.IDNivel')
            ->select('z.IDZona', 'z.NombreZona', 'z.IDNivel')
            ->where('z.Borrado', 0);

        if (!empty($nivelIds)) {
            $query->whereIn('z.IDNivel', $nivelIds);
        }

        $zonas = $query->orderBy('z.NombreZona')->get();
        return response()->json($zonas);
    }

    public function listarTiposEquipoConUbicacion()
    {
        $tipos = DB::table('in_tipos_equipo as te')
            ->join('in_equipos as e', 'e.IDTipoEquipo', '=', 'te.IDTipoEquipo')
            ->join('conf_zonas as z', 'z.IDZona', '=', 'e.IDZona')
            ->join('conf_niveles as n', 'n.IDNivel', '=', 'z.IDNivel')
            ->join('conf_edificios as ed', 'ed.IDEdificio', '=', 'n.IDEdificio')
            ->select(
                'te.IDTipoEquipo',
                'te.NombreTipoEquipo',
                'ed.IDPredio', // Obtenemos el IDPredio desde la tabla de edificios
                'n.IDEdificio',
                'z.IDNivel',
                'e.IDZona'
            )
            ->where('te.Borrado', 0)
            ->where('e.Borrado', 0)
            ->distinct()
            ->get();
        return response()->json($tipos);
    }

    public function listarPlanes()
    {
        $planes = DB::table('plan_planes')
            ->select('IDPlan', 'NombrePlan')
            ->where('IDTipoPlan', 6)
            ->where('Borrado', 0)
            ->orderBy('NombrePlan')
            ->get();
        return response()->json($planes);
    }

    public function exportarReporteMantenimiento(Request $request)
    {
        $fecha = now()->format('Y-m-d');
        $nombreArchivo = "reporte_mantenimiento_{$fecha}.xlsx";

        // Pasamos la request completa a la clase que genera el Excel
        return Excel::download(new ReporteMantenimientoExport($request), $nombreArchivo);
    }

    public function estadoAcciones(Request $request)
    {
        $request->validate([
            'predio_ids' => 'sometimes|array',
            'edificio_ids' => 'sometimes|array',
            'nivel_ids' => 'sometimes|array',
            'zona_ids' => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids' => 'sometimes|array',
            'subsistema_ids' => 'sometimes|array',
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date',
            'pivot' => 'sometimes|boolean', // opcional: para formato pivote
        ]);

        $planesIds = $request->input('plan_ids', []);
        $predioIds     = $request->input('predio_ids', []);
        $edificioIds   = $request->input('edificio_ids', []);
        $nivelIds      = $request->input('nivel_ids', []);
        $zonaIds       = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds    = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio   = $request->input('fecha_inicio');
        $fechaFin      = $request->input('fecha_fin');
        $asPivot       = (bool) $request->boolean('pivot', false);

        $query = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'am.IDOrdenTrabajo')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'am.IDAccionesMantenimiento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');

        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }
        if (!empty($edificioIds)) {
            $query->whereIn('ed.IDEdificio', $edificioIds);
        }
        if (!empty($nivelIds)) {
            $query->whereIn('n.IDNivel', $nivelIds);
        }
        if (!empty($zonaIds)) {
            $query->whereIn('e.IDZona', $zonaIds);
        }
        if (!empty($tipoEquipoIds)) {
            $query->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        }
        if (!empty($sistemaIds)) {
            $query->whereIn('sub.IDSistema', $sistemaIds);
        }
        if (!empty($subsistemaIds)) {
            $query->whereIn('e.IDSubsistema', $subsistemaIds);
        }
        if (!empty($planesIds)) {
            $query->whereIn('pp.IDPlan', $planesIds);
        }
        if ($fechaInicio) {
            $query->where('am.FechaInicioAccion', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->where('am.FechaInicioAccion', '<=', $fechaFin);
        }

        // AGRUPADO: Predio x Estado con conteo de acciones
        $resultados = $query->select(
            'p.IDPredio as predio_id',
            'p.NombrePredio as predio',
            'pp.NombrePlan as plan_id',
            'pp.DescripcionPlan as DescripcionPlan',
            DB::raw('COALESCE(s.IDEstado, 0) as estado_id'),
            DB::raw("COALESCE(s.NombreEstado, 'Sin estado') as estado"),
            DB::raw('COUNT(DISTINCT am.IDAccionesMantenimiento) as acciones')
        )
            ->groupBy('p.IDPredio', 'p.NombrePredio', 's.IDEstado', 's.NombreEstado')
            ->orderBy('p.NombrePredio')
            ->get();

        // Formato por defecto: filas (predio, estado, acciones)
        if (!$asPivot) {
            return response()->json($resultados->map(fn($r) => [
                'predio_id' => (int) $r->predio_id,
                'predio'    => $r->predio,
                'estado_id' => (int) $r->estado_id,
                'estado'    => $r->estado,
                'acciones'  => (int) $r->acciones,
                'plan_id' => (int) $r->plan_id,
                'DescripcionPlan' => $r->DescripcionPlan
            ]));
        }

        // Formato pivote (opcional): un objeto por predio con sus estados como claves
        $pivot = $resultados
            ->groupBy('predio_id')
            ->map(function ($rows) {
                $predio = $rows->first();
                $estados = [];
                foreach ($rows as $r) {
                    // clave por estado_id para fácil graficación; también podrías usar $r->estado
                    $estados[(string) $r->estado_id] = (int) $r->acciones;
                }
                return [
                    'predio_id' => (int) $predio->predio_id,
                    'predio'    => $predio->predio,
                    'estados'   => $estados, // ej: { "0": 2, "1": 5, "3": 1 }
                    'total'     => array_sum($estados),
                ];
            })
            ->values();

        return response()->json($pivot);
    }

    public function estadoAccionesPorMantenimiento(Request $request)
    {

        $request->validate([
            'predio_ids' => 'sometimes|array',
            'edificio_ids' => 'sometimes|array',
            'nivel_ids' => 'sometimes|array',
            'zona_ids' => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids' => 'sometimes|array',
            'subsistema_ids' => 'sometimes|array',
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date',
        ]);

        $planesIds = $request->input('plan_ids', []);
        $predioIds = $request->input('predio_ids', []);
        $edificioIds = $request->input('edificio_ids', []);
        $nivelIds = $request->input('nivel_ids', []);
        $zonaIds = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'am.IDOrdenTrabajo')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'am.IDAccionesMantenimiento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');


        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
        }
        if (!empty($edificioIds)) {
            $query->whereIn('ed.IDEdificio', $edificioIds);
        }
        if (!empty($nivelIds)) {
            $query->whereIn('n.IDNivel', $nivelIds);
        }
        if (!empty($zonaIds)) {
            $query->whereIn('e.IDZona', $zonaIds); // El equipo pertenece a la zona
        }
        if (!empty($planesIds)) {
            $query->whereIn('pp.IDPlan', $planesIds);
        }
        if (!empty($tipoEquipoIds)) { // <-- AÑADIR ESTE BLOQUE
            $query->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        }

        if (!empty($sistemaIds)) {
            $query->whereIn('sub.IDSistema', $sistemaIds);
        }
        if (!empty($subsistemaIds)) {
            $query->whereIn('e.IDSubsistema', $subsistemaIds);
        }
        // --- FIN CORRECCIÓN ---

        if ($fechaInicio) {
            $query->where('am.FechaInicioAccion', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->where('am.FechaInicioAccion', '<=', $fechaFin);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'te.NombreTipoEquipo as equipo',
            'te.ClaveTipoEquipo as tipo_equipo',
            'am.IDAccionesMantenimiento as id_accion',
            's.NombreEstado as estado',
            's.IDEstado as estado_id',
            DB::raw("CASE WHEN am.IDOrdenTrabajo IS NOT NULL THEN 1 ELSE 0 END as tiene_ot"),
            'am.FechaInicioAccion as fecha_inicio',
            'am.FechaFinAccion as fecha_fin',
            'pp.NombrePlan as nombre_plan_accion',
            'ot.NumOT as num_ot'
        )
            ->distinct()
            ->orderBy('p.NombrePredio')
            ->orderBy('te.NombreTipoEquipo')
            ->get();

        $data = $resultados->map(fn($r) => [
            'x' => $r->equipo,
            'y' => $r->predio,
            'v' => $r->estado_id,
            'estado' => $r->estado ?? 'Sin estado',
            'id_accion' => $r->id_accion,
            'tiene_ot' => (int) $r->tiene_ot,
            'fecha_inicio' => $r->fecha_inicio,
            'fecha_fin' => $r->fecha_fin,
            'tipo_equipo' => $r->tipo_equipo,
            'num_ot' => $r->num_ot ?? '-',
            'nombre_plan_accion' => $r->nombre_plan_accion ?? 'Sin plan'
        ]);

        return response()->json($data);
    }

    public function estadoAccionesConOT(Request $request)
    {
        $request->validate([
            'predio_ids'      => 'sometimes|array',
            'edificio_ids'    => 'sometimes|array',
            'nivel_ids'       => 'sometimes|array',
            'zona_ids'        => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids'     => 'sometimes|array',
            'subsistema_ids'  => 'sometimes|array',
            'fecha_inicio'    => 'sometimes|nullable|date',
            'fecha_fin'       => 'sometimes|nullable|date',
            'pivot'           => 'sometimes|boolean', // 1 = resumen por estado OT
            'solo_flujo'      => 'sometimes|boolean', // 1 = en detalle, solo estados flujo=1
        ]);

        $planesIds = $request->input('plan_ids', []);

        $predioIds     = $request->input('predio_ids', []);
        $edificioIds   = $request->input('edificio_ids', []);
        $nivelIds      = $request->input('nivel_ids', []);
        $zonaIds       = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds    = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio   = $request->input('fecha_inicio');
        $fechaFin      = $request->input('fecha_fin');

        // Catálogo de estados del flujo (flujo = 1), para asegurar ceros
        $estadosFlujo = DB::table('track_estados')
            ->where('IDFlujo', 3)
            ->select('IDEstado', 'NombreEstado')
            ->orderBy('IDEstado') // ajusta si tienes una columna de orden específica
            ->get();

        // Base query (solo acciones con OT)
        $base = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->join('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'am.IDOrdenTrabajo') // asegura solo con OT
            // Estado de la acción (opcional)
            ->leftJoin('track_instancias as ti_am', 'ti_am.IDInstancia', '=', 'am.IDAccionesMantenimiento')
            ->leftJoin('track_estados as s_am', 's_am.IDEstado', '=', 'ti_am.IDEstadoActualInstancia')
            // Estado de la OT (instancia = IDOT). Filtramos flujo=1 EN EL JOIN.
            ->leftJoin('track_instancias as ti_ot', 'ti_ot.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as s_ot', function ($join) {
                $join->on('s_ot.IDEstado', '=', 'ti_ot.IDEstadoActualInstancia')
                    ->where('s_ot.IDFlujo', 3);
            });

        // Filtros
        if (!empty($planesIds))     $base->whereIn('pp.IDPlan', $planesIds);
        if (!empty($predioIds))     $base->whereIn('p.IDPredio', $predioIds);
        if (!empty($edificioIds))   $base->whereIn('ed.IDEdificio', $edificioIds);
        if (!empty($nivelIds))      $base->whereIn('n.IDNivel', $nivelIds);
        if (!empty($zonaIds))       $base->whereIn('e.IDZona', $zonaIds);
        if (!empty($tipoEquipoIds)) $base->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        if (!empty($sistemaIds))    $base->whereIn('sub.IDSistema', $sistemaIds);
        if (!empty($subsistemaIds)) $base->whereIn('e.IDSubsistema', $subsistemaIds);
        if ($fechaInicio)           $base->where('ot.FechaIniOrdenTrabajo', '>=', $fechaInicio);
        if ($fechaFin)              $base->where('ot.FechaIniOrdenTrabajo',   '<=', $fechaFin);

        // --------- DETALLE (lista) ---------

        $qDetalle = clone $base;

        $detalles = $qDetalle->select(
            'p.IDPredio as predio_id',
            'p.NombrePredio as predio',
            'am.IDAccionesMantenimiento as id_accion',
            'ot.IDOT as id_ot',
            // Fechas reales de OT (ajusta nombres si difieren)
            'ot.FechaIniOrdenTrabajo as ot_fecha_inicio',
            'ot.FechaFinOT as ot_fecha_fin',
            DB::raw('COALESCE(s_ot.IDEstado, 0) as ot_estado_id'),
            DB::raw("COALESCE(s_ot.NombreEstado, 'Sin estado') as ot_estado"),
            DB::raw('COUNT(DISTINCT am.IDAccionesMantenimiento) as acciones'),
            'te.NombreTipoEquipo as equipo',
            'pp.NombrePlan as nombre_plan_accion',
            'ot.NumOT as num_ot'
        )
            ->distinct()
            ->orderBy('ot_estado')           // acomodado por estado de la OT
            ->orderByDesc('ot.FechaIniOrdenTrabajo')  // dentro del estado, más nuevas primero
            ->get();

        return response()->json($detalles->map(fn($r) => [
            'predio_id' => (int) $r->predio_id,
            'predio'    => $r->predio,
            'estado_id' => (int) $r->ot_estado_id,
            'estado'    => $r->ot_estado,
            'acciones'  => (int) $r->acciones,
        ]));
    }


    public function detalleEstadoOTs(Request $request)
    {
        // 1. VALIDACIÓN DE PARÁMETROS
        $request->validate([
            'predio_ids'      => 'sometimes|array',
            'edificio_ids'    => 'sometimes|array',
            'nivel_ids'       => 'sometimes|array',
            'zona_ids'        => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids'     => 'sometimes|array',
            'subsistema_ids'  => 'sometimes|array',
            'fecha_inicio'    => 'sometimes|nullable|date',
            'fecha_fin'       => 'sometimes|nullable|date',
        ]);


        $planesIds = $request->input('plan_ids', []);

        $predioIds     = $request->input('predio_ids', []);
        $edificioIds   = $request->input('edificio_ids', []);
        $nivelIds      = $request->input('nivel_ids', []);
        $zonaIds       = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds    = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio   = $request->input('fecha_inicio');
        $fechaFin      = $request->input('fecha_fin');

        // 2. CONSTRUCCIÓN DE LA CONSULTA BASE
        $query = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            // JOIN estricto para asegurar que la acción tiene una OT asociada
            ->join('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'am.IDOrdenTrabajo')
            // ¡NUEVO JOIN! Para obtener el nombre del tipo de OT
            ->leftJoin('ot_tipo_orden_trabajo as tot', 'tot.IDTipoOT', '=', 'ot.IDTipoOT')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'am.IDAccionesMantenimiento')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');



        // 3. APLICACIÓN DE FILTROS
        if (!empty($planesIds))        $query->whereIn('pp.IDPlan', $planesIds);
        if (!empty($predioIds))     $query->whereIn('p.IDPredio', $predioIds);
        if (!empty($edificioIds))   $query->whereIn('ed.IDEdificio', $edificioIds);
        if (!empty($nivelIds))      $query->whereIn('n.IDNivel', $nivelIds);
        if (!empty($zonaIds))       $query->whereIn('e.IDZona', $zonaIds);
        if (!empty($tipoEquipoIds)) $query->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        if (!empty($sistemaIds))    $query->whereIn('sub.IDSistema', $sistemaIds);
        if (!empty($subsistemaIds)) $query->whereIn('e.IDSubsistema', $subsistemaIds);
        if ($fechaInicio)           $query->where('ot.FechaIniOrdenTrabajo', '>=', $fechaInicio);
        if ($fechaFin)              $query->where('ot.FechaIniOrdenTrabajo',    '<=', $fechaFin);

        // 4. SELECCIÓN DE COLUMNAS PARA LA NUEVA TABLA
        $detalles = $query->select(
            'p.NombrePredio as predio',
            'ot.DescripcionOt as nombre_ot',
            'ot.DescripcionOt as descripcion_ot',
            'tot.NombreTipoOT as tipo_ot',
            'pp.NombrePlan as nombre_accion',
            'ot.NumOT as numero_ot',
            's.NombreEstado as estado',
            'ot.FechaIniOrdenTrabajo as fecha_inicio',
            'ot.FechaFinOT as fecha_fin',
        )
            ->distinct()
            ->orderBy('p.NombrePredio')
            ->orderBy('ot.NumOT')
            ->get();

        // 5. FORMATEO DE LA RESPUESTA JSON (directo desde la consulta)
        $data = $detalles->map(fn($r) => [
            'predio' => $r->predio,
            'nombre' => $r->nombre_ot,
            'descripcion_ot' => $r->descripcion_ot,
            'tipo_ot' => $r->tipo_ot ?? 'No especificado',
            'nombre_accion' => $r->nombre_accion,
            'numero_ot' => $r->numero_ot,
            'estado' => $r->estado ?? 'Sin estado',
            'fecha_inicio' => $r->fecha_inicio,
            'fecha_fin' => $r->fecha_fin,

        ]);

        return response()->json($data);
    }


    public function graficaEstadoEquiposDePlan(Request $request)
    {
        $request->validate([
            'id_plan'         => 'sometimes|integer',
            'plan_ids'        => 'sometimes|array',
            'plan_ids.*'      => 'integer',
            'predio_ids'      => 'sometimes|array',
            'edificio_ids'    => 'sometimes|array',
            'nivel_ids'       => 'sometimes|array',
            'zona_ids'        => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids'     => 'sometimes|array',
            'subsistema_ids'  => 'sometimes|array',
            'fecha_inicio'    => 'sometimes|nullable|date',
            'fecha_fin'       => 'sometimes|nullable|date',
        ]);

        // -------- Params
        $planesIds = $request->input('plan_ids', []);
        $predioIds     = $request->input('predio_ids', []);
        $edificioIds   = $request->input('edificio_ids', []);
        $nivelIds      = $request->input('nivel_ids', []);
        $zonaIds       = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds    = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio   = $request->input('fecha_inicio');
        $fechaFin      = $request->input('fecha_fin');

        // -------- Base: SOLO equipos que están en el plan (join a acciones del plan)
        $base = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->leftJoin('track_instancias as ti_eq', function ($join) {
                $join->on('ti_eq.IDInstancia', '=', 'e.IDEquipo')
                    ->where('ti_eq.IDFlujo', 2);
            })
            ->leftJoin('track_estados as s_eq', 's_eq.IDEstado', '=', 'ti_eq.IDEstadoActualInstancia');

        // Aplicación de filtros

        if (!empty($planesIds))        $base->whereIn('pp.IDPlan', $planesIds);
        if (!empty($predioIds))     $base->whereIn('p.IDPredio', $predioIds);
        if (!empty($edificioIds))   $base->whereIn('ed.IDEdificio', $edificioIds);
        if (!empty($nivelIds))      $base->whereIn('n.IDNivel', $nivelIds);
        if (!empty($zonaIds))       $base->whereIn('e.IDZona', $zonaIds);
        if (!empty($tipoEquipoIds)) $base->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        if (!empty($sistemaIds))    $base->whereIn('sub.IDSistema', $sistemaIds);
        if (!empty($subsistemaIds)) $base->whereIn('e.IDSubsistema', $subsistemaIds);
        if ($fechaInicio) $base->where('am.FechaInicioAccion', '>=', $fechaInicio);
        if ($fechaFin)    $base->where('am.FechaInicioAccion',    '<=', $fechaFin);

        // ---------- RESUMEN PARA GRÁFICA DE BARRAS APILADAS
        $resumen = $base
            ->select(
                'p.IDPredio as predio_id',
                'p.NombrePredio as predio',

                'e.IDEquipo as IdEquipo',
                DB::raw("COALESCE(s_eq.IDEstado, 0) as estado_id"),
                DB::raw("COALESCE(s_eq.NombreEstado, 'Sin estado') as estado"),
                // Se cuenta el número de equipos distintos y se renombra a 'acciones'
                DB::raw('COUNT(DISTINCT e.IDEquipo) as acciones')
            )
            ->groupBy('p.IDPredio', 'p.NombrePredio', 's_eq.IDEstado', 's_eq.NombreEstado')
            ->orderBy('p.NombrePredio')
            ->get();

        // Mapeo final para asegurar que el conteo sea un número entero
        $payload = $resumen->map(fn($r) => [
            'predio_id' => (int) $r->predio_id,
            'predio'    => $r->predio,
            'estado_id' => (int) $r->estado_id,
            'estado'    => $r->estado,
            'id_equipo'    => $r->IdEquipo,
            'acciones'  => (int) $r->acciones, // Este valor es el CONTEO DE EQUIPOS
        ]);

        return response()->json($payload);
    }

    public function tablaEstadoEquiposDePlan(Request $request)
    {


        $request->validate([
            'id_plan'         => 'sometimes|integer',
            'plan_ids'        => 'sometimes|array',
            'predio_ids'      => 'sometimes|array',
            'edificio_ids'    => 'sometimes|array',
            'nivel_ids'       => 'sometimes|array',
            'zona_ids'        => 'sometimes|array',
            'tipo_equipo_ids' => 'sometimes|array',
            'sistema_ids'     => 'sometimes|array',
            'subsistema_ids'  => 'sometimes|array',
            'fecha_inicio'    => 'sometimes|nullable|date',
            'fecha_fin'       => 'sometimes|nullable|date',
        ]);

        // -------- Params
        $planesIds =  $request->input('plan_ids', []);
        $predioIds     = $request->input('predio_ids', []);
        $edificioIds   = $request->input('edificio_ids', []);
        $nivelIds      = $request->input('nivel_ids', []);
        $zonaIds       = $request->input('zona_ids', []);
        $tipoEquipoIds = $request->input('tipo_equipo_ids', []);
        $sistemaIds    = $request->input('sistema_ids', []);
        $subsistemaIds = $request->input('subsistema_ids', []);
        $fechaInicio   = $request->input('fecha_inicio');
        $fechaFin      = $request->input('fecha_fin');

        // -------- Base: SOLO equipos que están en el plan (join a acciones del plan)
        $base = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('in_sistemas as sis', 'sis.IDSistema', '=', 'sub.IDSistema')

            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo') // limita a equipos del plan
            ->join('plan_planes as pp', 'am.IDPlan', '=', 'pp.IDPlan')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            // Estado actual del EQUIPO (flujo=2)
            ->leftJoin('track_instancias as ti_eq', function ($join) {
                $join->on('ti_eq.IDInstancia', '=', 'e.IDEquipo')
                    ->where('ti_eq.IDFlujo', 2);
            })
            ->leftJoin('track_estados as s_eq', 's_eq.IDEstado', '=', 'ti_eq.IDEstadoActualInstancia');


        // Filtros jerárquicos / taxonómicos
        if (!empty($planesIds))     $base->whereIn('pp.IDPlan', $planesIds);
        if (!empty($predioIds))     $base->whereIn('p.IDPredio', $predioIds);
        if (!empty($edificioIds))   $base->whereIn('ed.IDEdificio', $edificioIds);
        if (!empty($nivelIds))      $base->whereIn('n.IDNivel', $nivelIds);
        if (!empty($zonaIds))       $base->whereIn('e.IDZona', $zonaIds);
        if (!empty($tipoEquipoIds)) $base->whereIn('te.IDTipoEquipo', $tipoEquipoIds);
        if (!empty($sistemaIds))    $base->whereIn('sis.IDSistema', $sistemaIds);
        if (!empty($subsistemaIds)) $base->whereIn('sub.IDSubsistema', $subsistemaIds);

        // Filtros por rango de fechas de la acción en el plan (opcional)
        if ($fechaInicio) $base->where('am.FechaInicioAccion', '>=', $fechaInicio);
        if ($fechaFin)    $base->where('am.FechaInicioAccion',    '<=', $fechaFin);

        // ---------- DETALLE
        $detalle = $base
            ->select(
                'p.NombrePredio as predio',
                'ed.NombreEdificio as edificio',
                'n.NombreNivel as nivel',
                'z.NombreZona as zona',
                'e.IDEquipo as equipo_id',
                'e.ClaveEquipo as codigo_equipo',
                'e.NombreEquipo as equipo',
                'te.NombreTipoEquipo as tipo_equipo',
                'sub.NombreSubsistema as subsistema',
                'pp.IDPlan as plan_id',
                'pp.NombrePlan as plan',
                'sis.NombreSistema as nombre_sistema',
                DB::raw('COALESCE(s_eq.IDEstado, 0) as estado_id'),
                DB::raw("COALESCE(s_eq.NombreEstado, 'Sin estado') as estado")
            )
            ->distinct()
            ->orderBy('p.NombrePredio')->orderBy('ed.NombreEdificio')->orderBy('n.NombreNivel')
            ->orderBy('z.NombreZona')->orderBy('e.NombreEquipo')
            ->get();

        $data = $detalle->map(fn($r) => [
            'predio'         => $r->predio,
            'edificio'       => $r->edificio,
            'nivel'          => $r->nivel,
            'zona'           => $r->zona,
            'equipo_id'      => (int)$r->equipo_id,
            'codigo_equipo'  => $r->codigo_equipo,
            'equipo'         => $r->equipo,
            'tipo_equipo'    => $r->tipo_equipo,
            'subsistema'     => $r->subsistema,
            'plan_id'        => (int)$r->plan_id,
            'plan'           => $r->plan,
            'estado_id'      => (int)$r->estado_id,
            'estado'         => $r->estado,
            'nombre_sistema' => $r->nombre_sistema,
        ]);

        return response()->json($data);
    }
}
