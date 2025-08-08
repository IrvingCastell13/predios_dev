<?php

namespace App\Models;

class Subsistema extends BaseModel
{
    protected $table = 'in_subsistemas';

    protected $primaryKey = 'IDSubsistema';

    protected $guarded = [];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class, 'IDSistema');
    }
}
