<?php

namespace App\Models;

class Sistema extends BaseModel
{
    protected $table = 'in_sistemas';

    protected $primaryKey = 'IDSistema';

    protected $guarded = [];


    public function subsistemas()
    {
        return $this->hasMany(Subsistema::class, 'IDSistema', 'IDSistema');
    }
}
