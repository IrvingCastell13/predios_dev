<?php

namespace App\Models;

class Estado extends BaseModel
{
    protected $table = 'conf_estados';

    protected $primaryKey = 'IDEstado';

    protected $guarded = [];

    /**
     * Relación con el modelo País.
     */
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'IDPais', 'IDPais');
    }
}
