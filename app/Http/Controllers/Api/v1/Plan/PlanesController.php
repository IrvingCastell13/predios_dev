<?php

namespace App\Http\Controllers\api\v1\Plan;

use App\Helpers\MetikHelper;
use App\Http\Controllers\BaseController;
use App\Http\Traits\PermisosTrait;
use App\Models\AccionesMantenimiento;
use App\Models\Documentos;
use App\Models\Equipos;
use App\Models\MsRolesPredios;
use App\Models\PlanAccionRenovacion;
use App\Models\PlanPlan;
use App\Models\Predios;
use App\Models\TipoEquipos;
use App\Models\TiposDocumentos;
use App\Models\TrackEstados;
use App\Models\TrackFlujos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class PlanesController extends BaseController
{
    private $IDTipoPlanRenovacion = '5';

    private $IDTipoPlanMantenimiento = '6';

    use PermisosTrait;
    /**
     *  @group Planes | Planes
     * Obtener planes
     *
     * @authenticated
     *
     * Este endpoint permite obtener la lista de todos los planes.
     *
     * @header Authorization string required El token de autorización Bearer. Example: "Bearer {token}"
     *

     * @queryParam IDPredio char required El ID del predio. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     * @queryParam es_publico bool required si se require obtener los registros publicos o privados. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     * @response 403 {
     *   "message": "No tienes permiso para realizar esta acción."
     * }
     * @response 500 {
     *   "message": "Error de servidor."
     * }
     */

    // Obtener todos los registros (GET /planes)
    public function index(Request $request)
    {

        try {

            $user = $this->obtenerPersonaLogueada();
            $query = PlanPlan::with('tipo', 'Instancia')->where('IDCliente', $user->IDCliente);

            if ($request->tipo == 'r') {
                $query->where('IDTipoPlan', $this->IDTipoPlanRenovacion);
            }

            if ($request->tipo == 'm') {
                $query->where('IDTipoPlan', $this->IDTipoPlanMantenimiento);
            }


            if (! is_null($request->search)) {
                $query->where('NombrePlan', 'LIKE', '%' . $request->search . '%');
            }

            $dataTable = DataTables::of($query)
                ->editColumn('IDPlan', function ($row) {
                    return $row->IDPlan;
                })
                ->addColumn('pendiente', function ($row) {
                    return 'pendiente';
                })
                // ->rawColumns(['tipo_predio', 'nombre', 'acciones', 'usuarios', 'acciones_editar_eliminar', 'logo'])
                ->make(true);

            return $dataTable;
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     *  @group Planes | Planes
     * Obtener planes por ID
     *
     * @authenticated
     *
     * Este endpoint permite obtener un plan específica por su ID.
     *
     * @header Authorization string required El token de autorización Bearer. Example: "Bearer {token}"
     *

     * @queryParam IDPredio char required El ID del predio. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @urlParam IDPlan char required El ID del plan. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @response 200 {
     *   "data": {
     *   "IDPlan": "1",
     * @response 403 {
     *   "message": "No tienes permiso para realizar esta acción."
     * }
     * @response 404 {
     *   "message": "Registro no encontrado."
     * }
     * @response 500 {
     *   "message": "Error de servidor."
     * }
     */

    // Obtener un registro específico (GET /planes/{id})
    public function show(Request $request, $id)
    {
        try {

            $plan = PlanPlan::with('tipo')->findOrfail($id);

            $request['IDPredio'] = $plan->IDPredioPlan;

            // if (!$this->verificarPermiso('ver plan')) return $this->errorResponse('No tienes permiso para realizar esta acción.', 403);

            if (! $plan) {
                return $this->errorResponse('Plan no encontrado', 404);
            }

            return $this->successResponse($plan, 'Detalle del plan.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerDocumentosPlan(Request $request, $id)
    {
        try {
            $agruparTipo = filter_var($request->input('agruparTipo'), FILTER_VALIDATE_BOOLEAN);
            if ($agruparTipo) {

                $accionRenovacion = PlanAccionRenovacion::where('IDPlan', $id)->pluck('IDDocumento');

                $documentos = Documentos::whereIn('IDDocumento', $accionRenovacion)->pluck('IDTipoDocumento');

                $tiposDocumentos = TiposDocumentos::with('categoria.grupo')->whereIn('IDTipoDocumento', $documentos)->get();

                foreach ($tiposDocumentos as $tipo) {

                    $tipo->numero_docs = Documentos::whereIn('IDDocumento', $accionRenovacion)->where('IDTipoDocumento', $tipo->IDTipoDocumento)->count();
                    // $tipo->fechaSiguente =  Documentos::whereIn('IDDocumento', $accionRenovacion)->where('IDTipoDocumento', $tipo->IDTipoDocumento)->count();
                }

                return $this->successResponse($tiposDocumentos, 'Acciones renovaciones.');
            } else {

                $accionRenovacion = PlanAccionRenovacion::where('IDPlan', $id)->pluck('IDDocumento');

                $documentos = Documentos::with('accionRenovacion', 'tipoDocumento.categoria.grupo')->whereIn('IDDocumento', $accionRenovacion)->get();

                return $this->successResponse($documentos, 'Acciones renovaciones.');
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function obtenerEquiposPlan(Request $request, $id)
    {
        try {



            $agruparTipo = filter_var($request->input('agruparTipo'), FILTER_VALIDATE_BOOLEAN);
            if ($agruparTipo) {

                $accionesMantenimiento = AccionesMantenimiento::where('IDPlan', $id)
                    ->pluck('IDEquipo')
                    ->unique()
                    ->values();

                $equipos = Equipos::whereIn('IDEquipo', $accionesMantenimiento)->pluck('IDTipoEquipo');


                $tiposEquipos = TipoEquipos::whereIn('IDTipoEquipo', $equipos)->get();

                foreach ($tiposEquipos as $tipo) {

                    $tipo->numero_equipos = Equipos::whereIn('IDEquipo', $accionesMantenimiento)->where('IDTipoEquipo', $tipo->IDTipoEquipo)->count();
                    // $tipo->fechaSiguente =  Documentos::whereIn('IDDocumento', $accionRenovacion)->where('IDTipoDocumento', $tipo->IDTipoDocumento)->count();
                }

                return $this->successResponse($tiposEquipos, 'Acciones renovaciones.');
            } else {

                $accionesMantenimiento = AccionesMantenimiento::where('IDPlan', $id)
                    ->pluck('IDEquipo')
                    ->unique()
                    ->values();

                $equipos = Equipos::with([
                    'accionMantenimiento' => function ($query) use ($id) {
                        $query->where('IDPlan', $id);
                    },
                    'tipo',
                    'subsistema.sistema',
                    'documentos'
                ])
                    ->whereIn('IDEquipo', $accionesMantenimiento)
                    ->get();

                // dd($equipos);
                return $this->successResponse($equipos, 'Acciones mantenimiento.');
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerDocumentosTipo(Request $request, $id)
    {
        try {

            $accionRenovacion = PlanAccionRenovacion::where('IDPlan', $id)->pluck('IDDocumento');

            $documentos = Documentos::whereIn('IDDocumento', $accionRenovacion)->where('IDTipoDocumento', $request->IDTipoDocumento)->get();

            return $this->successResponse($documentos, 'Acciones renovaciones.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function obtenerEquiposTipo(Request $request, $id)
    {
        try {

            $accionesMantenimiento = AccionesMantenimiento::where('IDPlan', $id)->pluck('IDEquipo');

            $equipos = Equipos::with([
                'accionMantenimiento' => function ($query) use ($id) {
                    $query->where('IDPlan', $id);
                },
                'tipo',
                'subsistema.sistema',
                'documentos'
            ])->whereIn('IDEquipo', $accionesMantenimiento)->where('IDTipoEquipo', $request->IDTipoEquipo)->get();

            return $this->successResponse($equipos, 'Acciones mante.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerAccionesPlanDocumentos(Request $request, $id)
    {
        try {

            $accionRenovacion = PlanAccionRenovacion::with('tareas', 'documento', 'orden.persona', 'orden.archivos', 'orden.tareas')->where('IDPlan', $id)->where('IDDocumento', $request->IDDocumento)->where('IDDefinicionAccion', '<>', 0)->get();

            return $this->successResponse($accionRenovacion, 'Acciones renovaciones Documentos.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function obtenerAccionesPlanEquipo(Request $request, $id)
    {
        try {


            $query = AccionesMantenimiento::with('acciones_definiciones', 'tareas', 'equipo.documentos', 'orden.persona', 'orden.archivos', 'orden.tareas')->where('IDPlan', $id)->where('IDEquipo', $request->IDEquipo);


            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('equipo', function ($q2) use ($search) {
                        $q2->where('DescripcionEquipo', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('acciones_definiciones', function ($q3) use ($search) {
                            $q3->where('NombreDefinicionAccion', 'LIKE', "%{$search}%");
                        });
                });
            }

            $query->where('IDDefinicionAccion', '<>', 0)->orderBy('FechaInicioAccion', 'ASC');

            $accionesMantenimiento = $query->get();
            // ->where('IDDefinicionAccion', '<>', 0)

            return $this->successResponse($accionesMantenimiento, 'Acciones renovaciones equipos.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerAccionesPlanTipo(Request $request, $id)
    {
        try {

            $documentos = Documentos::where('IDTipoDocumento', $request->IDTipoDocumento)->pluck('IDDocumento');

            $accionRenovacion = PlanAccionRenovacion::with('tareas', 'documento', 'orden.persona', 'orden.archivos', 'orden.tareas')->where('IDPlan', $id)->whereIn('IDDocumento', $documentos)->where('IDDefinicionAccion', '<>', 0)->get();

            return $this->successResponse($accionRenovacion, 'Acciones renovaciones Documentos.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerAccionesPlanTipoEquipo(Request $request, $id)
    {
        try {


            $equipos = Equipos::where('IDTipoEquipo', $request->IDTipoEquipo)->pluck('IDEquipo');

            $accionMant = AccionesMantenimiento::with('tareas', 'equipo.documentos', 'orden.persona', 'orden.archivos', 'orden.tareas')
                ->where('IDPlan', $id)
                ->where('IDDefinicionAccion', '<>', 0)
                ->whereIn('IDEquipo', $equipos);

            if ($request->search) {
                $search = $request->search;
                $accionMant->where(function ($q) use ($search) {
                    $q->whereHas('equipo', function ($q2) use ($search) {
                        $q2->where('DescripcionEquipo', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('acciones_definiciones', function ($q3) use ($search) {
                            $q3->where('NombreDefinicionAccion', 'LIKE', "%{$search}%");
                        });
                });
            }

            $accionMant->orderBy('FechaInicioAccion', 'ASC');
            // ->where('IDDefinicionAccion', '<>', 0)->
            $accionMant = $accionMant->get();

            return $this->successResponse($accionMant, 'Acciones equipos.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtnerTableroPlan(Request $request, $id)
    {
        try {
            $data = [];

            // Obtener el flujo de renovación
            $flujo = TrackFlujos::where('NombreFlujo', 'Renovacion')->first();

            // Validar si existe el flujo antes de continuar
            if (! $flujo) {
                return response()->json(['error' => 'Flujo no encontrado'], 404);
            }


            $filtro = $request->ver;


            switch ($filtro) {
                case '6 meses':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addMonths(6);

                    break;
                case '3 meses':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addMonths(3);
                    break;
                case '30 dias':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addDays(30);
                    break;
                case '7 dias':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addDays(7);
                    break;
                default:
                    $fechaInicio = null;
                    $fechaFin = null;
            }


            // Obtener los estados del flujo
            $EstadosRenovacion = TrackEstados::where('IDFlujo', $flujo->IDFlujo)->get();

            // --- PRIMERO: agregar 'Programado' ---
            $estadoProgramado = $EstadosRenovacion->firstWhere('NombreEstado', 'Programado');

            if ($estadoProgramado) {
                $accionRenovacion = PlanAccionRenovacion::with('tareas', 'documento', 'orden.persona', 'orden.archivos', 'orden.tareas')
                    ->whereNull('IDOrdenTrabajo')
                    ->where('IDPlan', $id)
                    ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereBetween('FechaInicioAccion', [$fechaInicio, $fechaFin]);
                    })
                    ->get();

                $data['Programado'] = $accionRenovacion;
            }

            // --- LUEGO: procesar el resto, excepto 'Programado' ---
            foreach ($EstadosRenovacion as $estado) {
                if ($estado->NombreEstado === 'Programado') {
                    continue; // ya se procesó
                }

                $data[$estado->NombreEstado] = []; // puedes ajustar según lógica futura
            }

            return $this->successResponse($data, 'Tablero.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function obtnerTableroPlanEquipos(Request $request, $id)
    {
        try {


            $data = [];

            // Obtener el flujo de renovación
            $flujo = TrackFlujos::where('NombreFlujo', 'Mantenimiento')->first();



            // Validar si existe el flujo antes de continuar
            if (! $flujo) {
                return response()->json(['error' => 'Flujo no encontrado'], 404);
            }


            $filtro = $request->ver;


            switch ($filtro) {
                case '6 meses':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addMonths(6);

                    break;
                case '3 meses':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addMonths(3);
                    break;
                case '30 dias':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addDays(30);
                    break;
                case '7 dias':
                    $fechaInicio = Carbon::now();
                    $fechaFin = Carbon::now()->addDays(7);
                    break;
                default:
                    $fechaInicio = null;
                    $fechaFin = null;
            }


            // Obtener los estados del flujo
            $EstadosMantenimiento = TrackEstados::where('IDFlujo', $flujo->IDFlujo)->get();

            // --- PRIMERO: agregar 'Programado' ---
            $estadoProgramado = $EstadosMantenimiento->firstWhere('NombreEstado', 'Programado');

            if ($estadoProgramado) {
                $accionRenovacion = AccionesMantenimiento::with('tareas', 'equipo.documentos', 'orden.persona', 'orden.archivos', 'orden.tareas')
                    ->whereNull('IDOrdenTrabajo')
                    ->where('IDPlan', $id)
                    ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereBetween('FechaInicioAccion', [$fechaInicio, $fechaFin]);
                    })
                    ->get();


                $data['Programado'] = $accionRenovacion;
            }

            // --- LUEGO: procesar el resto, excepto 'Programado' ---
            foreach ($EstadosMantenimiento as $estado) {
                if ($estado->NombreEstado === 'Programado') {
                    continue; // ya se procesó
                }

                $data[$estado->NombreEstado] = []; // puedes ajustar según lógica futura
            }

            return $this->successResponse($data, 'Tablero.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    // public function obtnerGanttPlanEquipos(Request $request, $id)
    // {
    //     try {
    //         // Fechas opcionales
    //         $fechaInicio = $request->input('fecha_inicio');
    //         $fechaFin = $request->input('fecha_fin');

    //         // Nuevos filtros opcionales
    //         $idPredio = $request->input('id_predio');           // Puede ser array
    //         $idEdificio = $request->input('id_edificio');
    //         $idNivel = $request->input('id_nivel');
    //         $idZona = $request->input('id_zona');
    //         $idSubsistema = $request->input('id_subsistema');

    //         // 1. Obtener todos los equipos del plan con filtros aplicados
    //         $equipos = Equipos::whereHas('accionesMantenimiento', function ($q) use ($id) {
    //             $q->where('IDPlan', $id);
    //         })
    //             ->with('tipo')
    //             ->when($idPredio, function ($q) use ($idPredio) {
    //                 $q->whereIn('IDPredio', is_array($idPredio) ? $idPredio : [$idPredio]);
    //             })
    //             ->when($idEdificio, function ($q) use ($idEdificio) {
    //                 $q->where('IDEdificio', $idEdificio);
    //             })
    //             ->when($idNivel, function ($q) use ($idNivel) {
    //                 $q->where('IDNivel', $idNivel);
    //             })
    //             ->when($idZona, function ($q) use ($idZona) {
    //                 $q->where('IDZona', $idZona);
    //             })
    //             ->when($idSubsistema, function ($q) use ($idSubsistema) {
    //                 $q->where('IDSubsistema', $idSubsistema);
    //             })
    //             ->orderBy('NombreEquipo') // ajusta si tienes otro criterio
    //             ->get();

    //         // 2. Obtener todas las acciones del plan con filtros de fecha
    //         $accionesQuery = AccionesMantenimiento::with(
    //             'orden',
    //             'orden.persona',
    //             'acciones_definiciones'
    //         )
    //             ->where('IDPlan', $id);

    //         if ($fechaInicio) {
    //             $accionesQuery->where('FechaInicioAccion', '>=', $fechaInicio)
    //                 ->whereNotNull('FechaInicioAccion');
    //         }

    //         if ($fechaFin) {
    //             $accionesQuery->where('FechaFinAccion', '<=', $fechaFin)
    //                 ->whereNotNull('FechaFinAccion');
    //         }

    //         $acciones = $accionesQuery->get()->groupBy('IDEquipo');

    //         // 3. Mapear equipos con sus acciones filtradas
    //         $equiposConAcciones = $equipos->map(function ($equipo) use ($acciones) {
    //             $accionesDelEquipo = $acciones->get($equipo->IDEquipo, collect())->map(function ($accion) {
    //                 if ($accion->relationLoaded('orden') && $accion->orden) {
    //                     $accion->orden->makeHidden('url');
    //                 }
    //                 unset($accion->equipo);
    //                 return $accion;
    //             });

    //             $equipo->acciones = $accionesDelEquipo;
    //             return $equipo;
    //         });

    //         $data['Equipos'] = $equiposConAcciones;

    //         return $this->successResponse($data, 'Gantt.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage(), 500);
    //     }
    // }




    public function obtnerGanttPlanEquipos(Request $request, $id)
    {

        try {

            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            $idPredio = $request->input('id_predio'); // Puede ser array
            $idEdificio = $request->input('id_edificio');
            $idNivel = $request->input('id_nivel');
            $idZona = $request->input('id_zona');
            $idSistema = $request->input('id_sistema');
            $idSubsistema = $request->input('id_subsistema');
            $idResponsable = $request->input('id_responsable');


            // 1. Obtener todos los equipos del plan con filtros aplicados

            $equipos = Equipos::whereHas('accionesMantenimiento', function ($q) use ($id) {

                $q->where('IDPlan', $id);
            })

                ->with('tipo')

                ->when($idPredio && count($idPredio), function ($q) use ($idPredio) {
                    $q->whereIn('IDPredio', $idPredio);
                })

                ->when($idEdificio, function ($q) use ($idEdificio) {

                    $q->whereIn('IDEdificio', $idEdificio);
                })

                ->when($idNivel, function ($q) use ($idNivel) {

                    $q->whereIn('IDNivel', $idNivel);
                })

                ->when($idZona, function ($q) use ($idZona) {

                    $q->whereIn('IDZona', $idZona);
                })

                ->when($idSistema && count($idSistema), function ($q) use ($idSistema) {
                    $q->whereHas('subsistema', function ($subQuery) use ($idSistema) {
                        $subQuery->whereIn('IDSistema', $idSistema);
                    });
                })

                ->when($idSubsistema && count($idSubsistema), function ($q) use ($idSubsistema) {
                    $q->whereIn('IDSubsistema', $idSubsistema);
                })

                ->orderBy('NombreEquipo') // ajusta si tienes otro criterio

                ->get();

            // 2. Obtener todas las acciones del plan con filtros de fecha

            $accionesQuery = AccionesMantenimiento::with(
                'orden',
                'orden.persona',
                'orden.Instancia.estadoActual',
                'acciones_definiciones',
                'Instancia.estadoActual'

            )->where('IDPlan', $id)
            ->when($idResponsable && count($idResponsable), function ($q) use ($idResponsable) {
                // Filtramos a través de la relación anidada
                $q->whereHas('orden.persona', function ($subQuery) use ($idResponsable) {
                    $subQuery->whereIn('ms_personas.IDPersona', $idResponsable);
                });
            });


            if ($fechaInicio && $fechaFin) {
                $accionesQuery->where(function ($query) use ($fechaInicio, $fechaFin) {
                    $query->where('FechaInicioAccion', '<=', $fechaFin)
                        ->where('FechaFinAccion', '>=', $fechaInicio);
                });
            }
            $acciones = $accionesQuery->get()->groupBy('IDEquipo');


            // 3. Mapear equipos con sus acciones filtradas

            $equiposConAcciones = $equipos->map(function ($equipo) use ($acciones) {

                $accionesDelEquipo = $acciones->get($equipo->IDEquipo, collect())->map(function ($accion) {

                    if ($accion->relationLoaded('orden') && $accion->orden) {

                        $accion->orden->makeHidden('url');
                    }

                    unset($accion->equipo);

                    $accion->makeHidden('acciones');

                    return $accion;
                });

                $equipo->acciones = $accionesDelEquipo;

                return $equipo;
            });

            $data['Equipos'] = $equiposConAcciones;

            return $this->successResponse($data, 'Gantt.');
        } catch (\Exception $e) {

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerGanttPlanDocumentos(Request $request, $id)
    {
        try {
            // --- Parámetros de entrada ---
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            $idPredio = $request->input('id_predio');
            $idEdificio = $request->input('id_edificio');
            $idNivel = $request->input('id_nivel');
            $idZona = $request->input('id_zona');
            $idCategoria = $request->input('id_categoria');
            $idSubcategoria = $request->input('id_subcategoria');
            $idResponsable = $request->input('id_responsable');

            // 1. Obtener todos los documentos del plan con filtros aplicados
            $documentos = Documentos::whereHas('accionesRenovacion', function ($q) use ($id) {
                $q->where('IDPlan', $id);
            })

                ->when($idPredio && count($idPredio), function ($q) use ($idPredio) {
                    $q->whereIn('IDPredio', $idPredio);
                })
                ->when($idEdificio, function ($q) use ($idEdificio) {
                    $q->whereIn('IDEdificio', $idEdificio);
                })
                ->when($idNivel, function ($q) use ($idNivel) {
                    $q->whereIn('IDNivel', $idNivel);
                })
                ->when($idZona, function ($q) use ($idZona) {
                    $q->whereIn('IDZona', $idZona);
                })
                ->when($idSubcategoria && count($idSubcategoria), function ($q) use ($idSubcategoria) {
                    $q->whereHas('tipoDocumento.categoria', function ($subQuery) use ($idSubcategoria) {
                        $subQuery->whereIn('IDCategoriaDoc', $idSubcategoria);
                    });
                })
                ->when($idCategoria && count($idCategoria), function ($q) use ($idCategoria) {
                    $q->whereHas('tipoDocumento.categoria.grupo', function ($subQuery) use ($idCategoria) {
                        $subQuery->whereIn('IDGrupoDoc', $idCategoria);
                    });
                })

                ->orderBy('IDDocumento')
                ->get();


            // 2. Obtener todas las acciones del plan con filtros de fecha (Lógica idéntica a Equipos)
            $accionesQuery = PlanAccionRenovacion::with([
                'orden',
                'orden.persona',
                'orden.Instancia.estadoActual',
                'renovaciones_definiciones',
                'Instancia.estadoActual' // Usamos la relación que confirmaste que existe
            ])
                ->where('IDPlan', $id)
                ->when($idResponsable && count($idResponsable), function ($q) use ($idResponsable) {
                    // Filtramos a través de la relación anidada
                    $q->whereHas('orden.persona', function ($subQuery) use ($idResponsable) {
                        $subQuery->whereIn('ms_personas.IDPersona', $idResponsable);
                    });
                });

            if ($fechaInicio && $fechaFin) {
                $accionesQuery->where(function ($query) use ($fechaInicio, $fechaFin) {
                    $query->where('FechaInicioAccion', '<=', $fechaFin)
                        ->where('FechaFinAccion', '>=', $fechaInicio);
                });
            }

            $acciones = $accionesQuery->get()->groupBy('IDDocumento');

            // 3. Mapear documentos con sus acciones filtradas (Lógica idéntica a Equipos)
            $documentosConAcciones = $documentos->map(function ($documento) use ($acciones) {
                $accionesDelDocumento = $acciones->get($documento->IDDocumento, collect())->map(function ($accion) {

                    if ($accion->relationLoaded('orden') && $accion->orden) {
                        $accion->orden->makeHidden('url');
                    }

                    // Limpiamos la relación inversa para no crear un ciclo.
                    unset($accion->documento);

                    // *** LA SOLUCIÓN DEFINITIVA ESTÁ AQUÍ ***
                    // Ocultamos el atributo 'acciones' que se añade automáticamente por el '$appends' del modelo.
                    $accion->makeHidden('acciones');

                    return $accion;
                });

                $documento->acciones = $accionesDelDocumento;
                $documento->NombreDocumento = $documento->DescripcionDocumento; // Añadimos el alias
                return $documento;
            });

            $data['Documentos'] = $documentosConAcciones;

            return $this->successResponse($data, 'Gantt.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     *  @group Planes | Planes
     * Crear nueva plan
     *
     * @authenticated
     *
     * Este endpoint permite crear un planes en el sistema.
     *
     * @header Authorization string required El token de autorización Bearer. Example: "Bearer {token}"
     *

     * @queryParam IDPredio char required El ID del predio. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @bodyParam IDFlujo char required El ID del flujo.
     * @bodyParam IDEstadoActualInstancia char required El ID del estado actual de la instancia.
     * @bodyParam IDEstadoAnteriorInstancia char required El ID del estado anterior de la instancia.
     * @bodyParam IDUltimoEventoInstancia char required El ID del último evento de la instancia.
     * @bodyParam FechaUltimoEventoInstancia string required La fecha del último evento de la instancia. (Formato: YYYY-MM-DDTHH:MM:SS)
     * @bodyParam IDPersonaTransitoInstancia char required El ID de la persona en tránsito de la instancia.
     * @bodyParam IDTipoPlan char required El ID del tipo de plan.
     * @bodyParam ClavePlan string required La clave del plan.
     * @bodyParam NombrePlan string required El nombre del plan.
     * @bodyParam DescripcionPlan string required La descripción del plan.
     * @bodyParam FechaInicioPlan string required La fecha de inicio del plan. (Formato: YYYY-MM-DD)
     * @bodyParam FechaFinPlan string required La fecha de fin del plan. (Formato: YYYY-MM-DD)
     * @bodyParam IDPredioPlan char required El ID del predio asociado al plan.
     * @bodyParam EsPublico bool required Indica si el plan es público.

     *
     * @response 200 {
     *   "data": {
     *  "IDPlan": "1",
     * @response 403 {
     *   "message": "No tienes permiso para realizar esta acción."
     * }
     * @response 500 {
     *   "message": "Error de servidor."
     * }
     */

    // Crear un nuevo registro (POST /planes)
    // public function store(Request $request)
    // {
    //     try {

    //         DB::beginTransaction();

    //         $usuario = $this->obtenerPersonaLogueada();
    //         // if (!$this->verificarPermiso('crear plan', $request->EsPublico)) return $this->errorResponse('No tienes permiso para realizar esta acción.', 403);

    //         $instancia = MetikHelper::trackInstancia($request->IDFlujo, $request->IDEstadoActualInstancia, $request->IDEstadoAnteriorInstancia, $request->IDUltimoEventoInstancia, $request->FechaUltimoEventoInstancia, $request->IDPersonaTransitoInstancia, $usuario->IDCliente);

    //         $plan = PlanPlan::create([

    //             'IDPlan' => $instancia,
    //             'IDTipoPlan' => $request->tipo == 'r' ? $this->IDTipoPlanRenovacion : $this->IDTipoPlanMantenimiento,
    //             'ClavePlan' => $request->clave,
    //             'NombrePlan' => $request->NombrePlan,
    //             'DescripcionPlan' => $request->NombrePlan,
    //             'FechaInicioPlan' => $request->fechaInicio,
    //             'FechaFinPlan' => $request->fechaFin,
    //             // 'IDPredio' => $request->IDPredio,
    //             'IDCliente' => $usuario->IDCliente,
    //             'EsPublico' => 0,
    //         ]);

    //         DB::commit();

    //         return $this->successResponse($plan, 'Plan creado.', 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return $this->errorResponse($e->getMessage(), 500);
    //     }
    // }

    /**
     *  @group Planes | Planes
     * Actualizar planes
     *
     * @authenticated
     *
     * Este endpoint permite actualizar un plan existente.
     *
     * @header Authorization string required El token de autorización Bearer. Example: "Bearer {token}"
     *
     * @urlParam IDPlan char required El ID del plan. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *

     * @queryParam IDPredio char required El ID del predio. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @bodyParam IDFlujo char optional El ID del flujo.
     * @bodyParam IDEstadoActualInstancia char optional El ID del estado actual de la instancia.
     * @bodyParam IDEstadoAnteriorInstancia char optional El ID del estado anterior de la instancia.
     * @bodyParam IDUltimoEventoInstancia char optional El ID del último evento de la instancia.
     * @bodyParam FechaUltimoEventoInstancia string optional La fecha del último evento de la instancia. (Formato: YYYY-MM-DDTHH:MM:SS)
     * @bodyParam IDPersonaTransitoInstancia char optional El ID de la persona en tránsito de la instancia.
     * @bodyParam IDTipoPlan char optional El ID del tipo de plan.
     * @bodyParam ClavePlan string optional La clave del plan.
     * @bodyParam NombrePlan string optional El nombre del plan.
     * @bodyParam DescripcionPlan string optional La descripción del plan.
     * @bodyParam FechaInicioPlan string optional La fecha de inicio del plan. (Formato: YYYY-MM-DD)
     * @bodyParam FechaFinPlan string optional La fecha de fin del plan. (Formato: YYYY-MM-DD)
     * @bodyParam IDPredioPlan char optional El ID del predio asociado al plan.
     * @bodyParam EsPublico bool optional Indica si el plan es público.

     *
     * @response 200 {
     *   "data": {
     *  "IDPlan": "1",
     * @response 403 {
     *   "message": "No tienes permiso para realizar esta acción."
     * }
     * @response 404 {
     *   "error": "Registro no encontrado"
     * }
     * @response 500 {
     *   "message": "Error de servidor."
     * }
     */
    // Actualizar un registro (PUT /planes/{id})
    public function update(Request $request, $id)
    {

        // dd($request->all());
        try {
            // if (! $this->verificarPermiso('editar plan')) {
            //     return $this->errorResponse('No tienes permiso para realizar esta acción.', 403);
            // }
            $plan = PlanPlan::find($id);
            if (! $plan) {

                return $this->errorResponse('Plan no encontrado', 404);
            }


            $plan->update([

                'ClavePlan' => $request->clave,
                'NombrePlan' => $request->NombrePlan,
                'DescripcionPlan' => $request->NombrePlan,
                'FechaInicioPlan' => $request->fechaInicio,
                'FechaFinPlan' => $request->fechaFin,
            ]);

            return $this->successResponse($plan, 'Plan actualizado.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     *  @group Planes | Planes
     * Eliminar planes
     *
     * @authenticated
     *
     * Este endpoint permite eliminar (marcar como borrado) un plan.
     *
     * @header Authorization string required El token de autorización Bearer. Example: "Bearer {token}"
     *

     * @queryParam IDPredio char required El ID del predio. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @urlParam IDPlan char required El ID del plan. Example: "8b52652a-7160-11ef-9b4f-00090ffe0001"
     *
     * @response 200 {
     *   "data": {
     *     "IDPlan": "1",
     * @response 403 {
     *   "message": "No tienes permiso para realizar esta acción."
     * }
     * @response 404 {
     *   "error": "Registro no encontrado"
     * }
     * @response 500 {
     *   "message": "Error de servidor."
     * }
     */

    // Eliminar un registro (DELETE /planes/{id})
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if (! $this->verificarPermiso('eliminar plan')) {
                return $this->errorResponse('No tienes permiso para realizar esta acción.', 403);
            }
            // Eliminar el requisito de acción (marcar como borrado)
            $plan = PlanPlan::where('IDPlan', $id)->first();

            if ($plan) {
                $plan->update(['borrado' => 1]);

                DB::commit();

                return $this->successResponse($plan, 'Registro eliminado correctamente');
            } else {
                return $this->errorResponse(['error' => 'Registro no encontrado'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function obtenerPrediosPermisoVerPlanesRenovacion(Request $request)
    {

        try {

            DB::beginTransaction();

            $usuario = $this->obtenerPersonaLogueada();

            $roles = Role::whereHas('permissions', function ($query) {
                $query->where('name', 'ver planes de renovación');
            })->pluck('id');

            $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)->where('IDCliente', $usuario->IDCliente)->where('IDPersona', $usuario->IDPersona)
                ->pluck('IDPredio');

            $predios = Predios::orderBy('NombrePredio', 'ASC')->whereIn('IDPredio', $IDpredios)->get();

            return $this->successResponse($predios, 'Predios permiso ver planes renovacion.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
