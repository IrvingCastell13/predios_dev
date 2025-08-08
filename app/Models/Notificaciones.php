<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notificaciones extends BaseModel
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $primaryKey = 'IDNotificacion';

    protected $guarded = [];
}
