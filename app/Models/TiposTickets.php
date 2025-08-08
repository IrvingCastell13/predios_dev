<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class TiposTickets extends BaseModel
{
    protected static $usuarioAutenticado;

    protected $table = 'tk_tipos_ticket';

    protected $primaryKey = 'IDTipoTicket';

    protected $guarded = [];

    protected $appends = ['editar_tipo', 'eliminar_tipo'];

    protected static function booted()
    {

        static::creating(function ($model) {
            $model->IDTipoTicket = (string) Str::uuid();
        });

        static::$usuarioAutenticado = Auth::guard('api')->user();
    }

    public function sucategoria_ticket()
    {
        return $this->belongsTo(SubcategoriasTicket::class, 'IDSubCatTicket', 'IDSubCatTicket');
    }

    public function nivel_servicio()
    {
        return $this->belongsTo(NivelServicio::class, 'IDNivelServicio', 'IDNivelServicio');
    }

    public function getEditarTipoAttribute(): bool
    {
        return $this->tienePermisoSobreTipo('editar tipo ticket');
    }

    public function getEliminarTipoAttribute(): bool
    {
        return $this->tienePermisoSobreTipo('eliminar tipo ticket');
    }

    public function tienePermisoSobreTipo(string $permisoNombre): bool
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
}
