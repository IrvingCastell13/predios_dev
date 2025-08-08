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
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Asegúrate de importar Alignment

class OrdenesTrabajoExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filtros;

    public function __construct(Request $request)
    {
        $this->filtros = $request;
    }

    /**
     * Aplica los estilos a la hoja de cálculo.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // 1. Estilo para la primera fila (los encabezados)
            1    => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // 2. Estilo para todas las demás celdas (desde la fila 2 hacia abajo)
            'A2:V' . ($sheet->getHighestRow()) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define las cabeceras de las columnas.
     */
    public function headings(): array
    {
        return [
            'ID Orden de Trabajo',
            'Número De Orden de Trabajo',
            'Descripcion',
            'Comentarios',
            'Tipo Orden de Trabajo',
            'Personal Externo',
            'Hay Orden Compra',
            'Nombre Persona Asingada',
            'Predio',
            'Edificio',
            'Nivel',
            'Nombre de Zona',
            'Nombre de Equipo',
            'Nombre de Cliente',
            'Descripcion de Documento',
            'Fecha de Inicio',
            'Fecha de Fin',
            'Fecha Inicio Real',
            'Horas Reales',
            'Fecha Duracion Estimada',
            'Fecha Creacion Objeto',
            'Fecha Actualizacion Objeto',
        ];
    }

    /**
     * La consulta principal a la base de datos.
     */
    public function query()
    {
        // El query no necesita cambios, se mantiene igual que en el paso anterior.
        $query = DB::table('ot_orden_trabajo as ot')
            ->leftJoin('ot_tipo_orden_trabajo as t', 'ot.IDTipoOT', '=', 't.IDTipoOT')
            ->leftJoin('ms_personas as p', 'ot.IDPersona', '=', 'p.IDPersona')
            ->leftJoin('conf_predios as pred', 'ot.IDPredio', '=', 'pred.IDPredio')
            ->leftJoin('conf_edificios as ed', 'ot.IDEdificio', '=', 'ed.IDEdificio')
            ->leftJoin('conf_niveles as niv', 'ot.IDNivel', '=', 'niv.IDNivel')
            ->leftJoin('conf_zonas as zon', 'ot.IDZona', '=', 'zon.IDZona')
            // Se corrige el nombre de la tabla según tu retroalimentación
            ->leftJoin('conf_clientes as cli', 'ot.IDCliente', '=', 'cli.IDCliente')
            // Se corrige el nombre de la tabla según tu retroalimentación
            ->leftJoin('in_equipos as eq', 'ot.IDEquipo', '=', 'eq.IDEquipo')
            ->leftJoin('gd_documentos as doc', 'ot.IDDocumento', '=', 'doc.IDDocumento')
            ->select(
                'ot.IDOT',
                'ot.NumOT',
                'ot.DescripcionOt',
                'ot.ComentariosOt',
                't.NombreTipoOT',
                'ot.PersonalExternoOt',
                'ot.HayOrdenCompraOT',
                DB::raw("TRIM(CONCAT(COALESCE(p.NombrePersona, ''), ' ', COALESCE(p.ApellidoPaternoPersona, ''), ' ', COALESCE(p.ApellidoMaternoPersona, ''))) as nombre_completo_persona"),
                'pred.NombrePredio',
                'ed.NombreEdificio',
                'niv.NombreNivel',
                'zon.NombreZona',
                'eq.NombreEquipo',
                'cli.NombreCliente',
                'doc.DescripcionDocumento',
                'ot.FechaIniOrdenTrabajo',
                'ot.FechaFinOT',
                'ot.FechaInicioReal',
                'ot.HorasRealesOT',
                'ot.duracion_estimada',
                'ot.FechaCreacionObjeto',
                'ot.FechaActualizacionObjeto'
            )->orderBy('ot.IDOT', 'desc');

        $this->aplicarFiltrosOT($query, $this->filtros);

        return $query;
    }

    /**
     * Mapea cada fila y se asegura de que los datos vacíos se muestren como un guion.
     */
    public function map($ot): array
    {
        // Usamos el operador de fusión de null (??) para poner un '-' si el dato es nulo.
        return [
            $ot->IDOT ?? '-',
            ($ot->NumOT ? "Nº " . $ot->NumOT : '-'),
            $ot->DescripcionOt ?? '-',
            $ot->ComentariosOt ?? '-',
            $ot->NombreTipoOT ?? '-',
            $ot->PersonalExternoOt ?? '-',
            $ot->HayOrdenCompraOT ?? '-',
            // Para el nombre de persona, si el resultado del CONCAT es un espacio vacío, ponemos un guion.
            (trim($ot->nombre_completo_persona) ?: '-'),
            $ot->NombrePredio ?? '-',
            $ot->NombreEdificio ?? '-',
            $ot->NombreNivel ?? '-',
            $ot->NombreZona ?? '-',
            $ot->NombreEquipo ?? '-',
            $ot->NombreCliente ?? '-',
            $ot->DescripcionDocumento ?? '-',
            $ot->FechaIniOrdenTrabajo ?? '-',
            $ot->FechaFinOT ?? '-',
            $ot->FechaInicioReal ?? '-',
            $ot->HorasRealesOT ?? '-',
            $ot->duracion_estimada ?? '-',
            $ot->FechaCreacionObjeto ?? '-',
            $ot->FechaActualizacionObjeto ?? '-',
        ];
    }

    /**
     * Copia del método de tu controlador para que funcione aquí.
     */
    private function aplicarFiltrosOT($query, Request $request)
    {
        // Función auxiliar para obtener el valor del request, sin importar el formato de la llave
        $obtenerFiltro = function ($keySnake, $keyCamel) use ($request) {
            return $request->input($keySnake, $request->input($keyCamel));
        };

        $predioIds = $obtenerFiltro('predio_ids', 'predioIds');
        if (!empty($predioIds)) {
            $query->whereIn('ot.IDPredio', $predioIds);
        }

        $tipoOtIds = $obtenerFiltro('tipo_ot_ids', 'tipoOtIds');
        if (!empty($tipoOtIds)) {
            $query->whereIn('ot.IDTipoOT', $tipoOtIds);
        }

        $fechaInicio = $obtenerFiltro('fecha_inicio', 'fechaInicio');
        $fechaFin = $obtenerFiltro('fecha_fin', 'fechaFin');
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $query->whereBetween('ot.FechaInicioReal', [$fechaInicio, $fechaFin]);
        }
        
        $edificioIds = $obtenerFiltro('edificio_ids', 'edificioIds');
        if (!empty($edificioIds)) {
             $query->whereIn('ot.IDEdificio', $edificioIds);
        }

        $nivelIds = $obtenerFiltro('nivel_ids', 'nivelIds');
        if (!empty($nivelIds)) {
            $query->whereIn('ot.IDNivel', $nivelIds);
        }

        $zonaIds = $obtenerFiltro('zona_ids', 'zonaIds');
        if (!empty($zonaIds)) {
            $query->whereIn('ot.IDZona', $zonaIds);
        }

        $responsableIds = $obtenerFiltro('responsable_ids', 'responsableIds');
        if (!empty($responsableIds)) {
            $query->whereIn('ot.IDPersona', $responsableIds);
        }
        
    }
}