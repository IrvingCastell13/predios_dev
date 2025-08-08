<?php

namespace App\Models;

class TiposZonas extends BaseModel
{
    protected $table = 'conf_tipos_zona';

    protected $primaryKey = 'IDTipoZona';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];
}
