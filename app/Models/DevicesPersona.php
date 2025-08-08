<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DevicesPersona extends BaseModel
{
    use HasFactory;

    protected $table = 'pushnotifications';

    protected $primaryKey = 'IDPushNotification';

    protected $guarded = [];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona');
    }
}
