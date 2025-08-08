<?php

namespace App\Models;

use App\Utilities\Procedimientos\ProcedimientosUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Orden extends BaseModel
{
    protected $table = 'ot_orden_trabajo';

    protected $primaryKey = 'IDOT';

    protected $guarded = [];

    protected $appends = ['tareas_realizadas', 'ubicacion', 'url', 'iniciar', 'cancelar', 'editar', 'finalizar', 'rechazar', 'aceptar', 'subtotal', 'impuestos', 'total'];

    protected static function booted()
    {
        static::creating(function ($model) {

            DB::beginTransaction();
            $clienteId = static::$usuarioAutenticado->IDCliente ?? null;
            $moduloId = '0ed514d3-d393-4b3e-bf38-ace0ba72f668';

            $numeroOT = ProcedimientosUtil::obtenerSiguienteContador($moduloId, $clienteId);

            // dd($numeroOT);

            $model->NumOT = $numeroOT;


        });

    }

    private $IDFlujo             = 'a38544cc-cb12-40af-804d-89d9979b6ebf'; //* ID del flujo de trabajo para órdenes de trabajo
    private $EstadoAsignadaOt    = '497e2bf0-33e9-4a83-819f-b065af7a7b90';
    private $EstadoCerradaOt     = '6494f42d-76c6-4751-ac8d-e1ca9ddef43f';
    private $EstadoCreadaOt      = '906796a3-109f-4869-935b-d8a84ffc2018';
    private $EstadoCanceladaOt   = 'aaf0b78a-f0ef-4da0-92c1-14e906ae90bf';
    private $EstadoTerminadaOt   = 'b33ae5ab-3ad4-48c2-b2fc-a59761c5c9ef';
    private $EstadoEnProcesoOt   = 'b38f2d6f-f4fc-4f0c-beed-5e030ea390a7';
    private $EstadoProgramadaOt  = 'f97e99ea-2c76-47f0-ac4e-0e33fad48e45';

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDOT', 'IDInstancia');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoOrden::class, 'IDTipoOT', 'IDTipoOT');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'IDEquipo', 'IDEquipo');
    }

    public function documento()
    {
        return $this->belongsTo(Documentos::class, 'IDDocumento', 'IDDocumento');
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
        return $this->belongsTo(Niveles::class, 'IDNievel', 'IDNievel');
    }

    public function zona()
    {
        return $this->belongsTo(Zonas::class, 'IDZona', 'IDZona');
    }

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }

    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDOT');
    }

    public function tareas()
    {
        return $this->hasMany(TareasOrden::class, 'IDOT', 'IDOT');
    }

    public function rondines()
    {
        return $this->hasMany(OTRondines::class, 'IDOT', 'IDOT')->orderBy('Orden', 'asc');
    }

    public function costos()
    {
        return $this->hasMany(CostosOrden::class, 'IDOT', 'IDOT');

    }

    public function comentarios()
    {
        return $this->hasMany(ComentariosOrden::class, 'IDOT', 'IDOT');

    }

    public function requisitos()
    {
        return $this->hasMany(OtRequisitos::class, 'IDOT', 'IDOT');

    }

    public function mediciones()
    {
        return $this->hasMany(OtMediciones::class, 'IDOT', 'IDOT');

    }

    public function scopeEstado($query)
    {
        if (! is_null(request('estados'))) {
            return $query->whereHas('instancia', function ($query) {
                $query->whereIn('IDEstadoActualInstancia', request('estados'));
            });
        }

        return $query;
    }

    public function scopeTipo($query)
    {
        if (! is_null(request('IDTipoOT'))) {
            $query->where('IDTipoOT', request('IDTipoOT'));
        }

        return $query;
    }

    public function scopePersona($query)
    {
        if (! is_null(request('personas'))) {
            $query->whereIn('IDPersona', request('personas'));
        }

        return $query;
    }

    public function scopeVer($query)
    {
        $filtro = request('ver');


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
        if (! is_null($fechaInicio) && ! is_null($fechaFin)) {

            $query->whereBetween('FechaIniOrdenTrabajo', [$fechaInicio, $fechaFin]);
        }

        return $query;
    }

    public function getTareasRealizadasAttribute()
    {
        return TareasOrden::where('IDOT')->where('RealizadoTareaOT', 1)->count();
    }

    public function getUbicacionAttribute()
    {
        return collect([
            optional($this->predio)->NombrePredio,
            optional($this->edificio)->NombreEdificio,
            optional($this->nivel)->NombreNivel,
            optional($this->zona)->NombreZona,
        ])->filter()->implode(' - ');
    }

    public function getUrlAttribute()
    {
        return route('OrdenesTrabajo.show', Crypt::encrypt($this->IDOT));
    }

    // *si esta en estaddo asignado
    public function getIniciarAttribute()
    {
        // return true;
        if($this->Instancia->IDEstadoActualInstancia === $this->EstadoAsignadaOt) {
            return true;
        }

        return false;
    }
    // *si esta en estaddo asignado
    public function getCancelarAttribute()
    {
        // return true;

        if($this->Instancia->IDEstadoActualInstancia === $this->EstadoAsignadaOt || $this->Instancia->IDEstadoActualInstancia === $this->EstadoProgramadaOt) {
            return true;
        }

        return false;
    }

    public function getEditarAttribute()
    {
        if (in_array($this->Instancia->IDEstadoActualInstancia, [
            $this->EstadoProgramadaOt,
            $this->EstadoAsignadaOt,
            $this->EstadoEnProcesoOt,
        ])) {

            return $this->checkPermiso('editar ordenes');
        }

        return false;
    }


    // *si esta en estaddo En proceso
    public function getFinalizarAttribute()
    {
        // return true;

        // *si esta en estado En proceso
        if($this->Instancia->IDEstadoActualInstancia === $this->EstadoEnProcesoOt) {

            // *si el usuario autenticado es el responsable de la orden

            if($this->IDPersona === static::$usuarioAutenticado?->IDPersona || $this->checkPermiso('Ejecutar una orden de trabajo')) {
                return true;
            }

        }

        return false;
    }


    public function getRechazarAttribute()
    {
        // return true;

        if($this->Instancia->IDEstadoActualInstancia === $this->EstadoTerminadaOt) {
            return true;
        }

        return false;
    }

     public function getAceptarAttribute()
    {
        // return true;

        if($this->Instancia->IDEstadoActualInstancia === $this->EstadoTerminadaOt) {
            return true;
        }

        return false;
    }

    //* editar si tiene permiso y esta en estado Programada o Asignada o En proceso

    public function checkPermiso($permiso)
    {


        $personaPredios = MsRolesPredios::where('IDPersona', static::$usuarioAutenticado->IDPersona ?? null)
            ->where('IDPredio', $this->IDPredio)
            ->where('IDCliente', static::$usuarioAutenticado->IDCliente ?? null)
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

    public function getSubtotalAttribute()
    {
        return $this->costos->sum(function ($costo) {
            return ($costo->CantidadCostoOT ?? 0) * ($costo->PrecioCostoOT ?? 0);
        });
    }

    public function getImpuestosAttribute()
    {
        return 0;
    }

    public function getTotalAttribute()
    {
        return $this->subtotal + $this->impuestos;
    }

}
