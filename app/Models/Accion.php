<?php

namespace App\Models;

class Accion extends BaseModel
{
    protected $table = 'plan_def_acciones'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $primaryKey = 'IDDefinicionAccion'; // Ajusta según la clave primaria de tu tabla

    protected $guarded = [];

    protected $appends = ['id_roles', 'unidad_periodicidad'];

    public function planDefMantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }

    public function lecturas()
    {
        return $this->hasMany(Lecturas::class, 'IDDefinicionAccion', 'IDDefinicionAccion');
    }

    public function comentarios()
    {
        return $this->hasMany(PLanComentarios::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }

    public function tareas()
    {
        return $this->hasMany(PlanDefAccionTarea::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }

    public function requisitos()
    {
        return $this->hasMany(PlanDefAccionRequisitos::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }

    public function planDefRenovacion()
    {
        return $this->hasMany(Renovacion::class, 'IDDefinicionAccion', 'IDDefinicionAccion');

    }

    public function categoria()
    {
        return $this->belongsTo(GruposDoc::class, 'IDCategoria', 'IDGrupoDoc');
    }


    public function sistema()
    {
        return $this->belongsTo(Sistema::class, 'IDSistema', 'IDSistema');
    }


    public function subsistema()
    {
        return $this->belongsTo(Subsistema::class, 'IDSubsistema', 'IDSubsistema');
    }

    public function subcategoria()
    {
        return $this->belongsTo(CategoriasDocumentos::class, 'IDSubcategoria', 'IDCategoriaDoc');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'IDPais', 'IDPais');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'IDEstado', 'IDEstado');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'IDMunicipio', 'IDMunicipio');
    }

    public function getIdRolesAttribute()
    {
        return PlanAccionRoles::where('IDDefinicionAccion', $this->IDDefinicionAccion)
            ->pluck('IDRol')
            ->map(fn ($id) => (int) $id) // Forzar entero
            ->toArray(); // Devuelve [17, 30]
    }

    public function getUnidadPeriodicidadAttribute()
    {
        if ($this->Periodicidad % 30 === 0) {
            return 'mes';
        } elseif ($this->Periodicidad % 7 === 0) {
            return 'semana';
        } else {
            return 'día';
        }
    }
}
