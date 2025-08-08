<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MsCatalogos extends BaseModel
{
    use HasFactory;

    protected $table = 'ms_catalogos';

    protected $primaryKey = 'IDCatalogo';

    protected $guarded = [];
}
