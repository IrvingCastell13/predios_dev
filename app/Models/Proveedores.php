<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedores extends BaseModel
{
    use HasFactory;

    protected $table = 'gd_proveedores';

    protected $primaryKey = 'IDProveedor';

    protected $guarded = [];
}
