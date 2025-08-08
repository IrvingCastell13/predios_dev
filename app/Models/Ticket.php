<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use App\Http\Traits\PermisosTrait;
use App\Utilities\Procedimientos\ProcedimientosUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class Ticket extends BaseModel
{
    use PermisosTrait;

    protected static $usuarioAutenticado;

    protected static $msRolesEnPredios;

    protected $table = 'tk_ticket';

    protected $primaryKey = 'IDTicket';

    protected $guarded = [];

    protected $appends = ['fecha_hora', 'follow', 'follow_app', 'estado_instancia', 'estado_instancia_app', 'image', 'tiempo_solucion', 'Cerrado', 'Archivado', 'Atendido', 'Rechazado', 'rechazos', 'estado_respuesta', 'fecha_creacion_ticket', 'permiso_reasignar', 'tomar', 'subcategoria', 'url', 'permiso_editar'];



    protected $casts = [
        'NoTk' => 'string',
    ];

    protected static function booted()
    {
       static::creating(function ($model) {

            DB::beginTransaction();
            $clienteId = static::$usuarioAutenticado->IDCliente ?? null;
            $moduloId = '9053ab7e-fc40-4109-b77b-2fde0c76c879';

            $numeroTk = ProcedimientosUtil::obtenerSiguienteContador($moduloId, $clienteId);

            // dd($numeroTk);

            $model->NoTk = $numeroTk;


        });

        static::$usuarioAutenticado = Auth::guard('api')->user();

        // $usuario = Auth::guard('api')->user();

        // if ($usuario->IDRol == 1) {

        //     if ($usuario->IDRol == 1) {
        //         static::$usuarioAutenticado->IDPersona = is_null(request('IDPersona')) ? static::$usuarioAutenticado->IDPersona : request('IDPersona');
        //         static::$usuarioAutenticado->IDCliente = is_null(request('IDCliente')) ? static::$usuarioAutenticado->IDCliente : request('IDCliente');
        //     }

        // }

        // static::$msRolesEnPredios = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
        // ->where('IDPredio', $this->IDPredio)
        // ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
        // ->first();
    }

     public function getNombreTicketAttribute($value)
    {
        // Si el valor es el string "null", retorna null
        if ($value === "null") {
            return null;
        }
        return $value;
    }

    public function verificarTicketPermiso()
    {

        // return true;
        $request = request();

        if (! is_null($request->IDPersona)) {
            $usuario = Personas::findOrfail($request->IDPersona);
        } else {
            $usuario = Auth::guard('api')->user();
        }

        if ($this->verificarPermisoCambioManualUsuario()) {
            $usuario->IDPersona = is_null($request->IDPersona) ? $usuario->IDPersona : $request->IDPersona;
            $usuario->IDCliente = is_null($request->IDCliente) ? $usuario->IDCliente : $request->IDCliente;
        }

        $personaPredio = MsRolesPredios::where('IDPersona', $usuario->IDPersona)
            // ->where('IDRol', $request->IDRol)
            ->where('IDPredio', $request->IDPredio)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($personaPredio) {
            $role = Role::find($personaPredio->IDRol);

            return $role->hasPermissionTo('ver tickets personas');
        }

        return false;

    }

    public function getFechaHoraAttribute()
    {
        return $this->FechaCreacionObjeto->format('d/m/Y - H:i');
    }

    public function getFechaCreacionTicketAttribute()
    {
        return $this->FechaCreacionObjeto->format('d/m/Y - H:i');
    }

    public function getFollowAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDTicket],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : false;
    }

    public function getFollowAppAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDTicket],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : null;
    }

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDTicket', 'IDInstancia');
    }

    public function tipo()
    {
        return $this->belongsTo(TiposTickets::class, 'IDTipoTicket', 'IDTipoTicket');
    }

    public function tipoTicketRelation()
    {
        return $this->belongsTo(TiposTickets::class, 'IDTipoTicket', 'IDTipoTicket');
    }

    public function predio()
    {
        return $this->belongsTo(Predios::class, 'IDPredio', 'IDPredio');
    }

    public function edificio()
    {
        return $this->belongsTo(Edificios::class, 'IDEdificio', 'IDEdificio');
    }

    public function nivel()
    {
        return $this->belongsTo(Niveles::class, 'IDNivel', 'IDNivel');
    }

    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'IDZona', 'IDZona');
    }

    public function abre_ticket()
    {
        return $this->belongsTo(Personas::class, 'IDAbreTicket', 'IDPersona');
    }

    public function solicitante()
    {
        return $this->belongsTo(Personas::class, 'IDSolicitaTicket', 'IDPersona');
    }

    public function responsable_asignado()
    {
        return $this->belongsTo(Personas::class, 'IDAtiendeTicket', 'IDPersona');
    }

    public function responsable_seguimiento()
    {
        return $this->belongsTo(Personas::class, 'IDSupervisaTicket', 'IDPersona');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'IDEquipo', 'IDEquipo');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'IDTicket', 'IDTicket')
            ->orderBy('FechaComentarioTicket', 'desc');
    }

    public function archivos_adjuntos()
    {
        return $this->hasMany(ArchivoAdjuntosTicket::class, 'IDTicket', 'IDTicket')
            ->orderBy('FechaEvidencia', 'desc');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencias::class, 'IDTicket', 'IDTicket')
            ->orderBy('FechaEvidencia', 'desc');

    }

    // *SCOP FILTERS TICKETS
    public function scopeEstado($query)
    {

        if (! is_null(request('estadoInstancia'))) {
            dd(request('estadoInstancia'));
            if (Str::isUuid(request('estadoInstancia'))) {
                dd("filtrar por uuid");
            } else {
                // Es un string común
            }


            return $query->whereHas('Instancia', function ($query) {

                $query->whereIn('IDEstadoActualInstancia', request('estadoInstancia'));
            });
        }

        return $query;
    }

    public function scopeNivelServicio($query)
    {

        if (! is_null(request('Estado'))) {
            return $query->whereHas('tipo', function ($query) {
                $query->where('IDNivelServicio', request('Estado'));
            });
        }

        return $query;
    }

    // dd($this->verificarTicketPermiso());
    public function scopeAsignado($query)
    {
        $persona = static::$usuarioAutenticado->IDPersona; // o como accedas a la persona autenticada

        // Solo aplicar el filtro si viene el parámetro
        if (! is_null(request('IDAtiendeTicket'))) {

            // Verifica si el usuario tiene permiso
            if ($this->verificarTicketPermiso()) {
                // Puede ver cualquier asignado
                $query->whereIn('IDAtiendeTicket', request('IDAtiendeTicket'));
            } else {
                // Solo puede ver los tickets asignados a él mismo
                $ids = request('IDAtiendeTicket');

                // Asegúrate de que sea array
                // if (!is_array($ids)) {
                $ids = in_array($persona, $ids) ? [$persona] : [];
                // }
                // dd($ids);

                // // Si su propio ID está entre los solicitados
                // if (in_array($persona, $ids)) {
                //     $query->where('IDAtiendeTicket', $persona);
                // } else {
                $query->whereIn('IDAtiendeTicket', $ids);

            }
        }

        return $query;
    }

    public function scopePredioFiltro($query)
    {
        if (! is_null(request('IDPredio'))) {
            $query->where('IDPredio', request('IDPredio'));
        }

        return $query;
    }

    public function scopeVer($query)
    {

        if (! is_null(request('ver'))) {

            if (request('ver') === 'Ver los mios') {

                $IDEstadoTK08Pool = '329e0d27-ee2b-11ef-a708-00090ffe0001';

                // Obtener roles del usuario en ese predio y cliente
                $personaPredio = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
                    ->where('IDPredio', request('IDPredio'))
                    ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
                    ->pluck('IDRol');

                // // Tickets donde participa directamente
                // $query->where(function ($q) {
                //     $q->where('IDAtiendeTicket', static::$usuarioAutenticado->IDPersona)
                //     ->orWhere('IDSolicitaTicket', static::$usuarioAutenticado->IDPersona)
                //     ->orWhere('IDSupervisaTicket', static::$usuarioAutenticado->IDPersona);
                // });

                // Agregar tickets en estado pool si tiene rol válido
                $query->orWhere(function ($q) use ($personaPredio, $IDEstadoTK08Pool) {
                    $q->whereHas('Instancia', function ($subQ) use ($IDEstadoTK08Pool) {
                        $subQ->where('IDEstadoActualInstancia', $IDEstadoTK08Pool);
                    })
                        ->whereHas('Tipo', function ($subQ) use ($personaPredio) {
                            $subQ->where(function ($r) use ($personaPredio) {
                                $r->whereIn('RolAsignaAutoTicket', $personaPredio)
                                    ->orWhereIn('RolAsignaDefaultTicket', $personaPredio);
                            });
                        });
                });
            }

            return $query;
        }

        return $query;
    }

    public function scopeEdificioFiltro($query)
    {
        if (! is_null(request('IDEdificio'))) {
            $query->where('IDEdificio', request('IDEdificio'));
        }

        return $query;
    }

    public function scopeNivelFiltro($query)
    {
        if (! is_null(request('IDNivel'))) {
            $query->where('IDNivel', request('IDNivel'));
        }

        return $query;
    }

    public function scopeResueltos($query)
    {

        // if (is_null(request('estadoInstancia'))) {

        //     $query->where(function ($query) {
        //         $query->whereHas('Instancia', function ($q) {
        //             $q->where('IDEstadoActualInstancia', '329d2490-ee2b-11ef-a708-00090ffe0001');
        //         })
        //             ->where('IDSolicitaTicket', static::$usuarioAutenticado->IDPersona)
        //             ->orWhereDoesntHave('Instancia', function ($q) {
        //                 $q->where('IDEstadoActualInstancia', '329d2490-ee2b-11ef-a708-00090ffe0001');
        //             });
        //     });

        // }

        return $query;
    }

    public function scopeZonaFiltro($query)
    {
        if (! is_null(request('IDZona'))) {
            $query->where('IDZona', request('IDZona'));
        }

        return $query;
    }

    public function scopeArchivados($query)
    {

        // if (is_null(request('archivados')) && is_null(request('estadoInstancia'))) {

        //     return $query->whereHas('Instancia', function ($query) {
        //         $query->where('IDEstadoActualInstancia', '<>', '329df291-ee2b-11ef-a708-00090ffe0001');
        //     });

        // }

        return $query;
    }

    public function scopeSearch($query)
    {
        if (! is_null(request('search'))) {
            $search = request('search');

            $query->where('NombreTicket', 'LIKE', "%{$search}%")
                ->orWhere('DescripcionTicket', 'LIKE', "%{$search}%")
                ->orWhere('NoTk', 'LIKE', "%{$search}%")
                ->orWhereHas('responsable_asignado', function ($q) use ($search) {
                    $q->where('NombrePersona', 'LIKE', "%{$search}%")
                        ->orWhere('ApellidoPaternoPersona', 'LIKE', "%{$search}%")
                        ->orWhere('ApellidoMaternoPersona', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('predio', function ($q) use ($search) {
                    $q->where('NombrePredio', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('edificio', function ($q) use ($search) {
                    $q->where('NombreEdificio', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('nivel', function ($q) use ($search) {
                    $q->where('NombreNivel', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('zona', function ($q) use ($search) {
                    $q->where('NombreZona', 'LIKE', "%{$search}%");
                });
        }

        return $query;
    }

    public function getEstadoInstanciaAttribute()
    {
        $Instancia = TrackInstancias::join('track_estados as e', 'e.IDEstado', 'track_instancias.IDEstadoActualInstancia')
            ->where('IDInstancia', $this->IDTicket)
            ->withOutGlobalScopes()
            ->first();

        if (is_null($Instancia)) {
            return 'Creado';
        } else {

            $nombreInstancia = $Instancia->NombreEstado;
            if (!MetikHelper::CheckPermisoCliente('Mostrar Detalles tickets') && !$this->tienePermisoSobrePredio('Mostrar Detalles tickets')) {
                // if(!$this->tienePermisoSobrePredio('Mostrar Detalles tickets')){
                     $nombreInstancia = $Instancia->Alias;
                    // * aqui muestra el alias del permiso

                    // * EN PROCESO #b28f00
                    if ($Instancia->Alias === 'En proceso') {
                        return "<span  class='ticket-label ticket-label-warning'>{$nombreInstancia}</span>";
                    }
                    // * RESUELTO #4b465c
                    if ($Instancia->Alias === 'Resuelto') {
                        return "<span  class='ticket-label ticket-label-info'>{$nombreInstancia}</span>";
                    }
                    // * ATENDIDO #0f6491
                    if ($Instancia->Alias === 'Atendido') {
                        return "<span  class='ticket-label ticket-label-primary'>{$nombreInstancia}</span>";
                    }

                    // * ARCHIVADO
                    if ($Instancia->Alias === 'Archivado') {
                        return "<span  class='ticket-label ticket-label-info'>{$nombreInstancia}</span>";
                    }

                // }
            }else{


                // * AQUI MUESTRA EL NOMBRE DEL PERMISO
                // * ASIGNADO #00a488
                if ($Instancia->IDEstadoActualInstancia === 'fc1ae5f1-ee25-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-succes'>{$nombreInstancia}</span>";
                }
                // * EN PROCESO #b28f00
                if ($Instancia->IDEstadoActualInstancia === '6db15bdc-ee24-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-warning'>{$nombreInstancia}</span>";
                }
                // * RESUELTO #4b465c
                if ($Instancia->IDEstadoActualInstancia === '329d2490-ee2b-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-info'>{$nombreInstancia}</span>";
                }
                // * ATENDIDO #0f6491
                if ($Instancia->IDEstadoActualInstancia === 'fc1b47d0-ee25-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-primary'>{$nombreInstancia}</span>";
                }
                // * POOL
                if ($Instancia->IDEstadoActualInstancia === '329e0d27-ee2b-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-danger'>{$nombreInstancia}</span>";
                }
                // * ARCHIVADO
                if ($Instancia->IDEstadoActualInstancia === '329df291-ee2b-11ef-a708-00090ffe0001') {
                    return "<span  class='ticket-label ticket-label-info'>{$nombreInstancia}</span>";
                }
            }





        }
    }

    public function tienePermisoSobrePredio(string $permisoNombre): bool
    {
        $roles = Role::whereHas('permissions', function ($query) use ($permisoNombre) {
            $query->where('name', $permisoNombre);
        })->pluck('id');

        $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente ?? null)
            ->where('IDPersona', static::$usuarioAutenticado->IDPersona ?? null)
            ->where('IDPredio', $this->IDPredio)
            ->pluck('IDPredio');

        if (count($IDpredios) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getEstadoInstanciaAppAttribute()
    {
        $Instancia = TrackInstancias::join('track_estados as e', 'e.IDEstado', 'track_instancias.IDEstadoActualInstancia')
            ->where('IDInstancia', $this->IDTicket)
            ->withOutGlobalScopes()
            ->first();

        if (is_null($Instancia)) {
            return 'Creado';
        } else {

            $nombreInstancia = $Instancia->NombreEstado;
            if (!MetikHelper::CheckPermisoCliente('Mostrar Detalles tickets')) {
                if(!$this->tienePermisoSobrePredio('Mostrar Detalles tickets')){
                     $nombreInstancia = $Instancia->Alias;
                }
            }
            return $nombreInstancia;

        }
    }

    public function getImageAttribute()
    {

        // /images/upload-img-1.jpg
        $archivos = Archivos::select('NombreOriginalArchivo', 'ExtensionArchivo', 's3', 'manual')->where('IDObjetoPadreArchivo', $this->IDTicket)->get();

        if (count($archivos) == 0) {

            $evidencias = Evidencias::where('IDTicket', $this->IDTicket)->first();

            // * si evidencias es null checa documentos cargados
            if (is_null($evidencias)) {
                return url('/images/defaut_ticket.jpg');
            }else{
                $archivos = Archivos::select('NombreOriginalArchivo', 'ExtensionArchivo', 's3', 'manual')->where('IDObjetoPadreArchivo', $evidencias->IDArchivo)->get();
                // dd($archivos);
                // dd($archivos);
                if ($archivos[0]->ExtensionArchivo !== 'pdf') {

                    if(is_null($archivos[0]->s3)){
                        return url('/images/defaut_ticket.jpg');
                    }else{

                        $url = MetikHelper::getImageAwsS3($archivos[0]);
                        // dd($url);
                        return $url;
                    }

                } else {
                    return url('/images/defaut_ticket.jpg');
                }
            }


            return url('/images/defaut_ticket.jpg');
        } else {

            if ($archivos[0]->ExtensionArchivo !== 'pdf') {

                $url = MetikHelper::getImageAwsS3($archivos[0]);
                return $url;

            } else {
                return url('/images/defaut_ticket.jpg');
            }
        }

    }

    public function getTiempoSolucionAttribute()
    {
        $tiempo = $this->tipo->nivel_servicio->TSolucionNivelServicio;
        $tiempoFormateado = $this->formatTiempo($tiempo);

        return $tiempoFormateado;
    }

    public function formatTiempo($tiempo)
    {
        // Si es menos de 60 minutos
        if ($tiempo < 60) {
            return $tiempo == 1 ? '1 minuto' : "$tiempo minutos";
        }
        // Si es menos de 24 horas
        elseif ($tiempo < 1440) {
            $horas = floor($tiempo / 60);
            $minutos = $tiempo % 60;

            $resultado = $horas == 1 ? '1 hora' : "$horas horas";
            if ($minutos > 0) {
                $resultado .= $minutos == 1 ? ' con 1 minuto' : " con $minutos minutos";
            }

            return $resultado;
        }
        // Si es menos de 365 días
        elseif ($tiempo < 525600) {
            $dias = floor($tiempo / 1440);
            $horasMinutos = $tiempo % 1440;

            $resultado = $dias == 1 ? '1 día' : "$dias días";

            if ($horasMinutos > 0) {
                $horas = floor($horasMinutos / 60);
                $minutos = $horasMinutos % 60;

                if ($horas > 0) {
                    $resultado .= $horas == 1 ? ' con 1 hora' : " con $horas horas";

                    if ($minutos > 0) {
                        $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                    }
                } elseif ($minutos > 0) {
                    $resultado .= $minutos == 1 ? ' con 1 minuto' : " con $minutos minutos";
                }
            }

            return $resultado;
        }
        // Si es más de 365 días (años)
        else {
            $años = floor($tiempo / 525600);
            $diasMinutos = $tiempo % 525600;

            $resultado = $años == 1 ? '1 año' : "$años años";

            if ($diasMinutos > 0) {
                $dias = floor($diasMinutos / 1440);

                if ($dias > 0) {
                    $resultado .= $dias == 1 ? ' con 1 día' : " con $dias días";

                    $horasMinutos = $diasMinutos % 1440;
                    if ($horasMinutos > 0) {
                        $horas = floor($horasMinutos / 60);
                        $minutos = $horasMinutos % 60;

                        if ($horas > 0) {
                            $resultado .= $horas == 1 ? ', 1 hora' : ", $horas horas";

                            if ($minutos > 0) {
                                $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                            }
                        } elseif ($minutos > 0) {
                            $resultado .= $minutos == 1 ? ' y 1 minuto' : " y $minutos minutos";
                        }
                    }
                }
            }

            return $resultado;
        }
    }

    public function getCerradoAttribute()
    {

        // * SI ESTA EN ATENDIDO SOLO EL SOLICITANTE PODRA ACEPTAR

        if ($this->Instancia->IDEstadoActualInstancia === 'fc1b47d0-ee25-11ef-a708-00090ffe0001') {

            if (static::$usuarioAutenticado->IDPersona === $this->IDSolicitaTicket || static::$usuarioAutenticado->IDPersona === $this->IDSupervisaTicket) {
                return true;
            } else {
                return $this->checkPermiso('Aceptar en Atendido');
            }
        }

        // * SI ESTA EN PROCESO REVISAR EN BASE AL PERMISO Aceptar en proceso

        if ($this->Instancia->IDEstadoActualInstancia === '6db15bdc-ee24-11ef-a708-00090ffe0001') {
            return $this->checkPermiso('Aceptar en proceso');
        }

        // *  si no entra en ninguna de estas dos sera  falso no mostrara el boton
        return false;
    }

    public function getArchivadoAttribute()
    {

        // * SI ESTA EN ESTADO RESUELTO
        if ($this->Instancia->IDEstadoActualInstancia === '329d2490-ee2b-11ef-a708-00090ffe0001') {

            if (static::$usuarioAutenticado->IDRol == 1) {
                $this->checkPermiso('Archivar ticket');
                // if(request('IDPersona') === $this->IDSolicitaTicket || request('IDPersona') === $this->IDSupervisaTicket){
                //     return true;
                // }else{
                //     return false;
                // }

            } else {
                return $this->checkPermiso('Archivar ticket');
            }
            // if(static::$usuarioAutenticado->IDPersona === $this->IDSolicitaTicket || static::$usuarioAutenticado->IDPersona === $this->IDSupervisaTicket){

            //     if($this->checkPermiso("Archivar ticket")){
            //         return true;
            //     }else{

            //         return false;
            //     }

            // }else{
            //     false;
            // }

        }

        return false;
    }

    public function getAtendidoAttribute()
    {

        // * si esta en proceso revidsa el permiso Concluir en proceso
        if ($this->Instancia->IDEstadoActualInstancia === '6db15bdc-ee24-11ef-a708-00090ffe0001') {
            return $this->checkPermiso('Concluir en proceso');
        }

        // * SI ESTA EN ESTADO ASIGNADO
        if ($this->Instancia->IDEstadoActualInstancia === 'fc1ae5f1-ee25-11ef-a708-00090ffe0001') {

            return $this->checkPermiso('Concluir en Asignado');

        }

        return false;
    }

    public function getRechazadoAttribute()
    {
        // * SI ESTA EN ATENDIDO SOLO EL SOLICITANTE PODRA ACEPTAR

        if ($this->Instancia->IDEstadoActualInstancia === 'fc1b47d0-ee25-11ef-a708-00090ffe0001') {

            if (static::$usuarioAutenticado->IDPersona === $this->IDSolicitaTicket || static::$usuarioAutenticado->IDPersona === $this->IDSupervisaTicket) {
                return true;
            } else {
                return $this->checkPermiso('Aceptar en Atendido');
            }
        }

        // *  si no entra en ninguna de estas dos sera  falso no mostrara el boton
        return false;
    }

    public function getTomarAttribute()
    {
        // * SI ESTA EN ESTADO POOL
        if ($this->Instancia->IDEstadoActualInstancia === '329e0d27-ee2b-11ef-a708-00090ffe0001') {

            return $this->checkPermiso('Tomar Ticket');

        }

        return false;

    }

    public function trackRechazos()
    {
        return $this->hasMany(TrackHistoriaInstancias::class, 'IDInstancia', 'IDTicket')
            ->where('IDEventoHistoria', TrackHistoriaInstancias::EVENTO_RECHAZO_ID);
    }

    public function getRechazosAttribute()
    {
        return $this->trackRechazos->count() ?? 0;
    }

    public function getEstadoRespuestaAttribute()
    {

        $IDEstadoAsignado = 'fc1ae5f1-ee25-11ef-a708-00090ffe0001';

        if($this->Instancia->IDEstadoActualInstancia === $IDEstadoAsignado && $this->TAcumSolucionTicket > 0) {


            $fechaHoy = Carbon::now();
            $fechaTermina = Carbon::parse($this->HoraTerminaSolucionTicket);

            $diferenciaEnMinutosHoraTermina = $fechaHoy->diffInMinutes($fechaTermina);

            $nivelServicio = $this->tipo->nivel_servicio ?? null;

            $total = $nivelServicio->TSolucionNivelServicio - ($this->TAcumSolucionTicket + $diferenciaEnMinutosHoraTermina);

        }else{

            $fechaCreacion = Carbon::parse($this->FechaCreacionObjeto);
            $fechaTermina = Carbon::parse($this->HoraTerminaSolucionTicket);

            $diferenciaEnMinutosHoraTermina = $fechaCreacion->diffInMinutes($fechaTermina);

            $nivelServicio = $this->tipo->nivel_servicio ?? null;

            if (!$nivelServicio) {
                $total = 0;
            }else{
                $total = ($nivelServicio->TSolucionNivelServicio - $diferenciaEnMinutosHoraTermina - $this->TAcumSolucionTicket);
            }

        }

        if ($total < 0) {
            return 'Atrasado';
        } else {
            return 'En Tiempo';
        }

    }

    public function getPermisoReasignarAttribute()
    {

        if ($this->Instancia->IDEstadoActualInstancia !== '329d2490-ee2b-11ef-a708-00090ffe0001') {

            $personaPredios = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
                ->where('IDPredio', $this->IDPredio)
                ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
                ->get();

            foreach ($personaPredios as $personaPredio) {
                $role = Role::find($personaPredio->IDRol);

                if (! $role) {
                    continue; // Si el rol no existe, lo ignoramos
                }
                if ($role->hasPermissionTo('reasignar ticket')) {
                    return true;
                }
            }

            return false;
        } else {
            return false;
        }

    }

    public function getPermisoEditarAttribute()
    {

        $personaPredios = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
            ->where('IDPredio', $this->IDPredio)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
            ->get();

        foreach ($personaPredios as $personaPredio) {
            $role = Role::find($personaPredio->IDRol);

            if (! $role) {
                continue; // Si el rol no existe, lo ignoramos
            }
            if ($role->hasPermissionTo('Editar tickets')) {
                return true;
            }
        }

        return false;

    }

    public function checkPermiso($permiso)
    {

        $personaPredios = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona)
            ->where('IDPredio', $this->IDPredio)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente)
            ->get();

        foreach ($personaPredios as $personaPredio) {
            $role = Role::find($personaPredio->IDRol);

            if (! $role) {
                continue; // Si el rol no existe, lo ignoramos
            }
            if ($role->hasPermissionTo($permiso)) {
                return true;
            }
        }

        return false;
    }

    public function getSubcategoriaAttribute()
    {

        return $this->tipo->sucategoria_ticket->IDSubCatTicket;
    }

    public function getUrlAttribute()
    {
        return route('ticket.web.index', Crypt::encrypt($this->IDTicket));
    }
}
