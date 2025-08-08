<?php

namespace App\Models;

use Carbon\Carbon;


class PlanAccionRenovacion extends BaseModel
{
    protected $table = 'plan_acciones_renovacion'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDAccionesRenovacion'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    protected $appends = ['cantidad_tareas', 'fechas_rango', 'accion', 'acciones', 'follow', 'follow_app'];

    public function plan()
    {
        return $this->belongsTo(PlanPlan::class, 'IDPlan');
    }

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDAccionesRenovacion', 'IDInstancia');
    }

    public function documento()
    {
        return $this->belongsTo(Documentos::class, 'IDDocumento');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'IDOrdenTrabajo', 'IDOT');
    }

    public function tareas()
    {
        return $this->hasMany(PlanDefAccionTarea::class, 'IDAccionesRenovacion', 'IDDefRenovacion');
    }

    public function renovaciones_definiciones()
    {
        // Conecta la columna 'IDDefinicionAccion' de esta tabla
        // con la columna 'IDDefinicionAccion' de la tabla 'Accion'.
        return $this->belongsTo(Accion::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }

    public function getCantidadTareasAttribute()
    {
        return PlanDefAccionTarea::where('IDDefinicionAccion', $this->IDDefinicionAccion)->count();
    }

    public function getFechasRangoAttribute()
    {
        $fecha_inicio = Carbon::parse($this->FechaInicioAccion)->format('m/d');
        $fecha_fin = Carbon::parse($this->FechaFinAccion)->format('m/d');

        return "$fecha_inicio - $fecha_fin";
    }

    public function getAccionAttribute()
    {
        $accion = Accion::where('IDDefinicionAccion', $this->IDDefinicionAccion)->first();

        if ($accion) {
            return $accion->NombreDefinicionAccion;
        }

        return '';
    }

    public function getAccionesAttribute()
    {
        // Obtén los IDs únicos de definición de acción para este plan y documento
        $ids = self::where('IDPlan', $this->IDPlan)
            ->where('IDDocumento', $this->IDDocumento)
            ->pluck('IDDefinicionAccion')
            ->unique()
            ->filter(); // Elimina nulos o ceros si aplica

        // Si no hay IDs, retorna colección vacía
        if ($ids->isEmpty()) {
            return collect();
        }

        // Obtén todas las acciones en una sola consulta
        return Accion::whereIn('IDDefinicionAccion', $ids)->get();
    }



    public function getFollowAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDAccionesRenovacion],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : false;
    }

    public function getFollowAppAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDAccionesRenovacion],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : null;
    }
}
