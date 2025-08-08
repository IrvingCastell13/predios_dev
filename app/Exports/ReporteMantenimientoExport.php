<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReporteMantenimientoExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'A2:Z' . ($sheet->getHighestRow()) => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'ID Acción Mantenimiento',
            'Predio',
            'Edificio',
            'Nivel',
            'Zona',
            'Código Equipo',
            'Nombre Equipo',
            'Tipo Equipo',
            'Sistema',
            'Subsistema',
            'Plan de Mantenimiento',
            'Estado Acción',
            'Fecha Inicio Acción',
            'Fecha Fin Acción',
            'Tiene OT Asociada',
            'Número OT',
            'Descripción OT',
            'Tipo OT',
            'Estado OT',
            'Fecha Inicio OT',
            'Fecha Fin OT',
        ];
    }

    /**
     * Construye la consulta maestra para la exportación.
     */
    public function query()
    {
        $query = DB::table('plan_acciones_mantenimiento as am')
            ->join('in_equipos as e', 'e.IDEquipo', '=', 'am.IDEquipo')
            ->join('in_tipos_equipo as te', 'te.IDTipoEquipo', '=', 'e.IDTipoEquipo')
            ->join('in_subsistemas as sub', 'sub.IDSubsistema', '=', 'e.IDSubsistema')
            ->join('in_sistemas as sis', 'sis.IDSistema', '=', 'sub.IDSistema')
            ->join('conf_zonas as z', 'z.IDZona', '=', 'e.IDZona')
            ->join('conf_niveles as n', 'n.IDNivel', '=', 'z.IDNivel')
            ->join('conf_edificios as ed', 'ed.IDEdificio', '=', 'n.IDEdificio')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'ed.IDPredio')
            ->join('plan_planes as pp', 'pp.IDPlan', '=', 'am.IDPlan')
            // Estado de la Acción de Mantenimiento
            ->leftJoin('track_instancias as ti_am', 'ti_am.IDInstancia', '=', 'am.IDAccionesMantenimiento')
            ->leftJoin('track_estados as s_am', 's_am.IDEstado', '=', 'ti_am.IDEstadoActualInstancia')
            // Datos de la Orden de Trabajo (si existen)
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'am.IDOrdenTrabajo')
            ->leftJoin('ot_tipo_orden_trabajo as tot', 'tot.IDTipoOT', '=', 'ot.IDTipoOT')
            // Estado de la Orden de Trabajo
            ->leftJoin('track_instancias as ti_ot', 'ti_ot.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as s_ot', 's_ot.IDEstado', '=', 'ti_ot.IDEstadoActualInstancia')
            ->select(
                'am.IDAccionesMantenimiento',
                'p.NombrePredio', 'ed.NombreEdificio', 'n.NombreNivel', 'z.NombreZona',
                'e.ClaveEquipo', 'e.NombreEquipo', 'te.NombreTipoEquipo',
                'sis.NombreSistema', 'sub.NombreSubsistema',
                'pp.NombrePlan',
                's_am.NombreEstado as estado_accion',
                'am.FechaInicioAccion', 'am.FechaFinAccion',
                'ot.IDOT', 'ot.NumOT', 'ot.DescripcionOt',
                'tot.NombreTipoOT',
                's_ot.NombreEstado as estado_ot',
                'ot.FechaIniOrdenTrabajo', 'ot.FechaFinOT'
            )->orderBy('p.NombrePredio')->orderBy('e.NombreEquipo');

        $this->aplicarFiltros($query, $this->request);

        return $query;
    }

    /**
     * Mapea cada fila de la consulta a un formato de array para el Excel.
     */
    public function map($row): array
    {
        return [
            $row->IDAccionesMantenimiento ?? '-',
            $row->NombrePredio ?? '-',
            $row->NombreEdificio ?? '-',
            $row->NombreNivel ?? '-',
            $row->NombreZona ?? '-',
            $row->ClaveEquipo ?? '-',
            $row->NombreEquipo ?? '-',
            $row->NombreTipoEquipo ?? '-',
            $row->NombreSistema ?? '-',
            $row->NombreSubsistema ?? '-',
            $row->NombrePlan ?? '-',
            $row->estado_accion ?? 'Sin estado',
            $row->FechaInicioAccion ?? '-',
            $row->FechaFinAccion ?? '-',
            $row->IDOT ? 'Sí' : 'No',
            $row->NumOT ?? '-',
            $row->DescripcionOt ?? '-',
            $row->NombreTipoOT ?? '-',
            $row->estado_ot ?? '-',
            $row->FechaIniOrdenTrabajo ?? '-',
            $row->FechaFinOT ?? '-',
        ];
    }

    /**
     * Aplica los filtros de la request a la consulta de Eloquent.
     */
    private function aplicarFiltros($query, Request $request)
    {
        // Filtros de ubicación
        if ($request->filled('predio_ids')) $query->whereIn('p.IDPredio', $request->predio_ids);
        if ($request->filled('edificio_ids')) $query->whereIn('ed.IDEdificio', $request->edificio_ids);
        if ($request->filled('nivel_ids')) $query->whereIn('n.IDNivel', $request->nivel_ids);
        if ($request->filled('zona_ids')) $query->whereIn('z.IDZona', $request->zona_ids);

        // Filtros de taxonomía de equipos
        if ($request->filled('tipo_equipo_ids')) $query->whereIn('te.IDTipoEquipo', $request->tipo_equipo_ids);
        if ($request->filled('sistema_ids')) $query->whereIn('sis.IDSistema', $request->sistema_ids);
        if ($request->filled('subsistema_ids')) $query->whereIn('sub.IDSubsistema', $request->subsistema_ids);

        // Otros filtros
        if ($request->filled('plan_ids')) $query->whereIn('pp.IDPlan', $request->plan_ids);

        // Filtro de fecha (se puede aplicar a la acción o a la OT según se necesite)
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('am.FechaInicioAccion', [$request->fecha_inicio, $request->fecha_fin]);
        }
    }
}