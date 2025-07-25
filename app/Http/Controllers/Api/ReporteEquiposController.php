<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $request->validate(['sistema_ids' => 'required|array']);
        $sistemaIds = $request->input('sistema_ids');

        if (empty($sistemaIds)) {
            return response()->json([]);
        }

        $subsistemas = DB::table('in_subsistemas')
            ->whereIn('IDSistema', $sistemaIds) // Usa whereIn para buscar en un arreglo
            ->select('IDSubsistema', 'NombreSubsistema')
            ->where('Borrado', 0)
            ->orderBy('NombreSubsistema')
            ->get();
        return response()->json($subsistemas);
    }

    //INICIO DE ENPOINTS PARA DASHBOARD MANTENIMIENTO (3 GRAFICAS)
    public function estadoAccionesPorMantenimiento(Request $request)
    {

        $request->validate([
            'predio_ids' => 'sometimes|array',
            'sistema_ids' => 'sometimes|array',
            'subsistema_ids' => 'sometimes|array',
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date',
        ]);

        $predioIds = $request->input('predio_ids', []);
        $sistemaIds = $request->input('sistema_ids', []); // Acepta arreglo
        $subsistemaIds = $request->input('subsistema_ids', []); // Acepta arreglo
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = DB::table('conf_predios as p')
            ->join('conf_edificios as ed', 'ed.IDPredio', '=', 'p.IDPredio')
            ->join('conf_niveles as n', 'n.IDEdificio', '=', 'ed.IDEdificio')
            ->join('conf_zonas as z', 'z.IDNivel', '=', 'n.IDNivel')
            ->join('in_equipos as e', 'e.IDZona', '=', 'z.IDZona')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('plan_acciones_mantenimiento as am', 'am.IDEquipo', '=', 'e.IDEquipo')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->leftJoin('track_instancias as ti', 'ti.IDInstancia', '=', 'e.IDEquipo')
            ->leftJoin('track_estados as s', 's.IDEstado', '=', 'ti.IDEstadoActualInstancia');


        if (!empty($predioIds)) {
            $query->whereIn('p.IDPredio', $predioIds);
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
            $query->where('am.FechaFinAccion', '<=', $fechaFin);
        }

        $resultados = $query->select(
            'p.NombrePredio as predio',
            'te.NombreTipoEquipo as equipo',
            'am.IDAccionesMantenimiento as id_accion',
            's.NombreEstado as estado',
            's.IDEstado as estado_id',
            DB::raw("CASE WHEN am.IDOrdenTrabajo IS NOT NULL THEN 1 ELSE 0 END as tiene_ot"),
            'am.FechaInicioAccion as fecha_inicio',
            'am.FechaFinAccion as fecha_fin'
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
            'fecha_fin' => $r->fecha_fin
        ]);

        return response()->json($data);
    }
    //FIN DE ENPOINTS PARA DASHBOARD MANTENIMIENTO (3 GRAFICAS)

}
