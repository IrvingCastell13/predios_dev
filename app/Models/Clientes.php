<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Clientes extends BaseModel
{
    protected $table = 'conf_clientes';

    protected $primaryKey = 'IDCliente';

    protected $guarded = [];

    protected $appends = ['image'];

    public function documentos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDCliente');
    }

    public function archivos()
    {
        return $this->hasMany(Archivos::class, 'IDObjetoPadreArchivo', 'IDCliente');
    }

    public function getImageAttribute()
    {

        if (! $this->logo) {
            return '/images/avatar_default.jpg';
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $this->logo,
            now()->addMinutes(20)
        );

        return $url;
    }
}
