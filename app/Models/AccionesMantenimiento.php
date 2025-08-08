<?php

namespace App\Models;

use Carbon\Carbon;

class AccionesMantenimiento extends BaseModel
{
    protected $table = 'plan_acciones_mantenimiento'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDAccionesMantenimiento'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    protected $appends = ['cantidad_tareas', 'fechas_rango', 'accion', 'acciones', 'follow', 'follow_app'];

    public function plan()
    {
        return $this->belongsTo(PlanPlan::class, 'IDPlan');
    }

    public function Instancia()
    {
        return $this->belongsTo(TrackInstancias::class, 'IDAccionesMantenimiento', 'IDInstancia');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipos::class, 'IDEquipo');
    }

    public function acciones_definiciones()
    {
        return $this->belongsTo(Accion::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }



    public function orden()
    {
        return $this->belongsTo(Orden::class, 'IDOrdenTrabajo', 'IDOT');
    }



    public function tareas()
    {
        return $this->hasMany(PlanDefAccionTarea::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }


    public function getCantidadTareasAttribute()
    {
        return PlanDefAccionTarea::where('IDDefinicionAccion', $this->IDDefinicionAccion)->count();
    }

    public function getFechasRangoAttribute()
    {
        $fecha_inicio = $this->FechaInicioAccion
            ? Carbon::parse($this->FechaInicioAccion)->format('m/d')
            : '';

        $fecha_fin = $this->FechaFinAccion
            ? Carbon::parse($this->FechaFinAccion)->format('m/d')
            : '';

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
            ->where('IDEquipo', $this->IDEquipo)
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
            ['IDObjeto', $this->IDAccionesMantenimiento],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : false;
    }

    public function getFollowAppAttribute()
    {
        $follow = Follow::where([
            ['IDObjeto', $this->IDAccionesMantenimiento],
            ['IDPersona', static::$usuarioAutenticado->IDPersona ?? null],
        ])->first(['IDFollow']);

        return $follow ? $follow->IDFollow : null;
    }

}
