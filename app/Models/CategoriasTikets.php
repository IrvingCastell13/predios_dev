<?php

namespace App\Models;

class CategoriasTikets extends BaseModel
{
    protected $table = 'tk_categorias_ticket';

    protected $primaryKey = 'IDCatTicket';

    protected $guarded = [];

    public function subcategorias()
    {
        return $this->hasMany(SubcategoriasTicket::class, 'IDCatTicket', 'IDCatTicket')->orderBy('NombreSubCatTicket', 'asc');
    }

    public function eliminarEnCascada()
    {
        foreach ($this->subcategorias as $sub) {
            $sub->eliminarEnCascada();
        }

        $this->Borrado = true;
        $this->save();
    }
}
