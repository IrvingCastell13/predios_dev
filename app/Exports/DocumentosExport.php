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

class DocumentosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filtros;

    public function __construct(Request $request)
    {
        $this->filtros = $request;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Ajustado el rango para las columnas actuales
            'A2:L' . ($sheet->getHighestRow()) => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function headings(): array
    {
        // Nuevos encabezados para la matriz de cumplimiento
        return [
            'País',
            'Estado',
            'Municipio',
            'Predio',
            'Categoría (Grupo)',
            'Subcategoría',
            'Tipo de Documento Requerido',
            'Estatus General',
            'Tiene Archivos?',
            'ID Documento Existente',
            'Descripción',
            'Fecha de Emisión',
            'Fecha de Vencimiento',
            'Días para Vencer',
            'Estatus de Vigencia',
        ];
    }

    /**
     * Reconstruimos el query para que parta de los documentos OBLIGATORIOS
     * y así poder identificar los que faltan.
     */
    public function query()
    {
        // Lógica basada en tu función `construirQueryBasePredios`
        $query = DB::table('conf_predios as p')
            ->join('gd_obligatorios_tipo_inmueble as do', 'do.IDTipoInmueble', '=', 'p.IDTipoPredio')
            ->join('gd_tipos_documento as td', 'td.IDTipoDocumento', '=', 'do.IDTipoDocumento')
            ->join('gd_categorias_doc as cat', 'cat.IDCategoriaDoc', '=', 'td.IDCategoriaDocumento')
            ->join('gd_grupos_doc as g', 'g.IDGrupoDoc', '=', 'cat.IDGrupoDoc')
            // LEFT JOIN es la clave para encontrar los faltantes
            ->leftJoin('gd_documentos as d', function ($join) {
                $join->on('d.IDPredio', '=', 'p.IDPredio')
                    ->on('d.IDTipoDocumento', '=', 'td.IDTipoDocumento');
            })
            ->leftJoin('arch_archivos as a', 'a.IDObjetoPadreArchivo', '=', 'd.IDDocumento')
            ->leftJoin('conf_municipios as mun', 'p.IDMunicipio', '=', 'mun.IDMunicipio')
            ->leftJoin('conf_estados as est', 'mun.IDEstado', '=', 'est.IDEstado')
            ->leftJoin('conf_paises as pais', 'est.IDPais', '=', 'pais.IDPais')
            ->select(
                'pais.NombrePais',
                'est.NombreEstado',
                'mun.NombreMunicipio',
                'p.NombrePredio',
                'g.NombreGrupoDoc',
                'cat.NombreCategoriaDoc',
                'td.NombreTipoDocumento as DocumentoRequerido',
                // Campos del documento existente (serán NULL si falta)
                'd.IDDocumento',
                'd.DescripcionDocumento',
                'd.FechaEmisionDocumento',
                'd.FechaVencimientoDocumento',
                DB::raw('DATEDIFF(d.FechaVencimientoDocumento, CURDATE()) as dias_para_vencer'),
                DB::raw('COUNT(DISTINCT a.IDArchivo) as total_archivos')

            );

        $this->aplicarFiltros($query, $this->filtros);

        return $query->orderBy('p.NombrePredio')->orderBy('g.NombreGrupoDoc')->orderBy('cat.NombreCategoriaDoc');
    }

    /**
     * Mapea cada fila para el Excel, determinando el estatus.
     */
    public function map($row): array
    {
        $estatusGeneral = $row->IDDocumento ? 'Creado' : 'Faltante';
        $tieneArchivos = $row->total_archivos > 0 ? 'Sí' : 'No';
        $estatusVigencia = '-';

        if ($row->IDDocumento) { // Solo si el documento existe
            if (!$row->FechaVencimientoDocumento) {
                $estatusVigencia = 'No Aplica';
            } elseif ($row->FechaVencimientoDocumento < now()->toDateString()) {
                $estatusVigencia = 'Vencido';
            } elseif ($row->dias_para_vencer <= 30) {
                $estatusVigencia = 'Por Vencer';
            } else {
                $estatusVigencia = 'Vigente';
            }
        }

        return [
            $row->NombrePais ?? '-',
            $row->NombreEstado ?? '-',
            $row->NombreMunicipio ?? '-',
            $row->NombrePredio ?? '-',
            $row->NombreGrupoDoc ?? '-',
            $row->NombreCategoriaDoc ?? '-',
            $row->DocumentoRequerido ?? '-',
            $estatusGeneral,
            $tieneArchivos,
            $row->IDDocumento ?? '-',
            $row->DescripcionDocumento ?? '-',
            $row->FechaEmisionDocumento ?? '-',
            $row->FechaVencimientoDocumento ?? '-',
            $row->dias_para_vencer ?? '-',
            $estatusVigencia,
        ];
    }



    // --- FUNCIÓN DE FILTROS ACTUALIZADA ---
    private function aplicarFiltros($query, Request $request)
    {
        // Función auxiliar para obtener el valor del request.
        $obtenerFiltro = fn($key) => $request->input($key);

        // --- Filtros de Ubicación (con la corrección) ---
        if ($paisIds = $obtenerFiltro('id_paises')) {
            $query->whereIn('p.IDPais', $paisIds); // Correcto, usa 'p'
        }
        if ($estadoIds = $obtenerFiltro('id_estados')) {
            $query->whereIn('p.IDEstado', $estadoIds); // Correcto, usa 'p'
        }
        if ($municipioIds = $obtenerFiltro('id_municipios')) {
            $query->whereIn('p.IDMunicipio', $municipioIds); // Cambiado de 'mun.IDMunicipio' a 'p.IDMunicipio'
        }
        if ($predioIds = $obtenerFiltro('predio_ids')) {
            $query->whereIn('d.IDPredio', $predioIds);
        }
        if ($edificioIds = $obtenerFiltro('id_edificios')) {
            $query->whereIn('d.IDEdificio', $edificioIds);
        }
        if ($nivelIds = $obtenerFiltro('id_niveles')) {
            $query->whereIn('d.IDNivel', $nivelIds);
        }
        if ($zonaIds = $obtenerFiltro('id_zonas')) {
            $query->whereIn('d.IDZona', $zonaIds);
        }

        // --- Filtros de Documento ---
        if ($grupoIds = $obtenerFiltro('grupo_ids')) {
            $query->whereIn('g.IDGrupoDoc', $grupoIds);
        }
        if ($categoriaIds = $obtenerFiltro('categoria_ids')) {
            $query->whereIn('cat.IDCategoriaDoc', $categoriaIds);
        }
        if ($tipoDocIds = $obtenerFiltro('tipo_doc_ids')) {
            $query->whereIn('d.IDTipoDocumento', $tipoDocIds);
        }
        if ($tipoInmuebleIds = $obtenerFiltro('tipo_inmueble_ids')) {
            // Asumiendo que 'p.IDTipoPredio' es la columna correcta para tipo de inmueble
            $query->whereIn('p.IDTipoPredio', $tipoInmuebleIds);
        }

        // --- Filtros de Fecha y Estatus ---
        // (Estos se mantienen igual)
        if ($fechaInicio = $obtenerFiltro('fecha_inicio_vigencia')) {
            $query->where('d.FechaEmisionDocumento', '>=', $fechaInicio);
        }
        if ($fechaFin = $obtenerFiltro('fecha_fin_vigencia')) {
            $query->where('d.FechaVencimientoDocumento', '<=', $fechaFin);
        }
        if ($estatus = $obtenerFiltro('estatus_documento')) {
            if ($estatus === 'Vigente') {
                $query->where('d.FechaVencimientoDocumento', '>', DB::raw('CURDATE()'))
                    ->where(DB::raw('DATEDIFF(d.FechaVencimientoDocumento, CURDATE())'), '>', 30);
            } elseif ($estatus === 'Por Vencer') {
                $query->whereBetween(DB::raw('DATEDIFF(d.FechaVencimientoDocumento, CURDATE())'), [0, 30]);
            } elseif ($estatus === 'Vencido') {
                $query->where('d.FechaVencimientoDocumento', '<', DB::raw('CURDATE()'));
            }
        }
    }
}
