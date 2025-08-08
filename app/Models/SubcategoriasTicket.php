<?php

namespace App\Models;

class SubcategoriasTicket extends BaseModel
{
    protected $table = 'tk_subcategorias_ticket';

    protected $primaryKey = 'IDSubCatTicket';

    protected $guarded = [];

    public function categoria_ticket()
    {
        return $this->belongsTo(CategoriasTikets::class, 'IDCatTicket', 'IDCatTicket');
    }

    public function tipos()
    {
        return $this->hasMany(TiposTickets::class, 'IDSubCatTicket', 'IDSubCatTicket')->orderBy('NombreTipoTicket', 'asc');
    }

    public function eliminarEnCascada()
    {
        foreach ($this->tipos as $tipo) {
            $tipo->Borrado = true;
            $tipo->save();
        }

        $this->Borrado = true;
        $this->save();
    }
}
