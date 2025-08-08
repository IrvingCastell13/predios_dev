<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificacionesGd extends BaseModel
{
    use HasFactory;

    protected $table = 'gd_notificaciones';

    protected $primaryKey = 'IDTipoDocumento';

    protected $guarded = [];
}
