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

class ReporteRenovacionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            'ID Acción Renovación',
            'Predio',
            'Edificio',
            'Nivel',
            'Zona',
            'Nombre Documento',
            'Tipo Documento',
            'Categoría Documento',
            'Subcategoría Documento',
            'Plan de Renovación',
            'Estado Acción',
            'Fecha Inicio Acción',
            'Fecha Fin Acción',
            'Estado Vigencia Documento',
            'Tiene OT Asociada',
            'Número OT',
            'Descripción OT',
            'Tipo OT',
            'Estado OT',
            'Fecha Inicio OT',
            'Fecha Fin OT',
        ];
    }

    public function query()
    {
        // Consulta Maestra Unificada
        $query = DB::table('plan_acciones_renovacion as ar')
            ->join('gd_documentos as d', 'd.IDDocumento', '=', 'ar.IDDocumento')
            ->join('plan_planes as pp', 'pp.IDPlan', '=', 'ar.IDPlan')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'd.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            ->join('conf_predios as p', 'p.IDPredio', '=', 'd.IDPredio')
            ->leftJoin('conf_edificios as ed', 'ed.IDEdificio', '=', 'd.IDEdificio')
            ->leftJoin('conf_niveles as n', 'n.IDNivel', '=', 'd.IDNivel')
            ->leftJoin('conf_zonas as z', 'z.IDZona', '=', 'd.IDZona')
            // Estado de la Acción de Renovación
            ->leftJoin('track_instancias as ti_ar', 'ti_ar.IDInstancia', '=', 'ar.IDAccionesRenovacion')
            ->leftJoin('track_estados as s_ar', 's_ar.IDEstado', '=', 'ti_ar.IDEstadoActualInstancia')
            // Estado de Vigencia del Documento
            ->leftJoin('track_instancias as ti_doc', 'ti_doc.IDInstancia', '=', 'd.IDDocumento')
            ->leftJoin('track_estados as s_doc', 's_doc.IDEstado', '=', 'ti_doc.IDEstadoActualInstancia')
            // Datos de la Orden de Trabajo
            ->leftJoin('ot_orden_trabajo as ot', 'ot.IDOT', '=', 'ar.IDOrdenTrabajo')
            ->leftJoin('ot_tipo_orden_trabajo as tot', 'tot.IDTipoOT', '=', 'ot.IDTipoOT')
            // Estado de la OT
            ->leftJoin('track_instancias as ti_ot', 'ti_ot.IDInstancia', '=', 'ot.IDOT')
            ->leftJoin('track_estados as s_ot', 's_ot.IDEstado', '=', 'ti_ot.IDEstadoActualInstancia')
            ->select(
                'ar.IDAccionesRenovacion',
                'p.NombrePredio', 'ed.NombreEdificio', 'n.NombreNivel', 'z.NombreZona',
                'd.DescripcionDocumento as nombre_documento',
                'td.NombreTipoDocumento', 'g.NombreGrupoDoc', 'cat.NombreCategoriaDoc',
                'pp.NombrePlan',
                's_ar.NombreEstado as estado_accion',
                'ar.FechaInicioAccion', 'ar.FechaFinAccion',
                's_doc.NombreEstado as estado_documento',
                'ot.IDOT', 'ot.NumOT', 'ot.DescripcionOt', 'tot.NombreTipoOT',
                's_ot.NombreEstado as estado_ot',
                'ot.FechaIniOrdenTrabajo', 'ot.FechaFinOT'
            )->orderBy('p.NombrePredio')->orderBy('d.DescripcionDocumento');

        // Aplicar los filtros de la solicitud
        $this->aplicarFiltros($query, $this->request);

        return $query;
    }

    public function map($row): array
    {
        return [
            $row->IDAccionesRenovacion ?? '-',
            $row->NombrePredio ?? '-',
            $row->NombreEdificio ?? '-',
            $row->NombreNivel ?? '-',
            $row->NombreZona ?? '-',
            $row->nombre_documento ?? '-',
            $row->NombreTipoDocumento ?? '-',
            $row->NombreGrupoDoc ?? '-',
            $row->NombreCategoriaDoc ?? '-',
            $row->NombrePlan ?? '-',
            $row->estado_accion ?? 'Sin estado',
            $row->FechaInicioAccion ?? '-',
            $row->FechaFinAccion ?? '-',
            $row->estado_documento ?? 'Sin vigencia',
            $row->IDOT ? 'Sí' : 'No',
            $row->NumOT ?? '-',
            $row->DescripcionOt ?? '-',
            $row->NombreTipoOT ?? '-',
            $row->estado_ot ?? '-',
            $row->FechaIniOrdenTrabajo ?? '-',
            $row->FechaFinOT ?? '-',
        ];
    }

    private function aplicarFiltros($query, Request $request)
    {
        $query->when($request->filled('predio_ids'), fn($q) => $q->whereIn('p.IDPredio', $request->predio_ids));
        $query->when($request->filled('edificio_ids'), fn($q) => $q->whereIn('ed.IDEdificio', $request->edificio_ids));
        $query->when($request->filled('nivel_ids'), fn($q) => $q->whereIn('n.IDNivel', $request->nivel_ids));
        $query->when($request->filled('zona_ids'), fn($q) => $q->whereIn('z.IDZona', $request->zona_ids));
        $query->when($request->filled('grupo_ids'), fn($q) => $q->whereIn('g.IDGrupoDoc', $request->grupo_ids));
        $query->when($request->filled('categoria_ids'), fn($q) => $q->whereIn('cat.IDCategoriaDoc', $request->categoria_ids));
        $query->when($request->filled('plan_ids'), fn($q) => $q->whereIn('pp.IDPlan', $request->plan_ids));
        
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('ar.FechaInicioAccion', [$request->fecha_inicio, $request->fecha_fin]);
        }
    }
}