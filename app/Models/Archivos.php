<?php

namespace App\Models;

use App\Helpers\MetikHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Archivos extends BaseModel
{
    protected $table = 'arch_archivos';

    protected $primaryKey = 'IDArchivo';

    protected $guarded = [];

    protected $appends = ['url'];

    public function persona()
    {
        return $this->belongsTo(Personas::class, 'IDPersona', 'IDPersona');
    }

    public function getUrlAttribute()
    {

        $url = MetikHelper::getImageAwsS3($this);

        return $url;

    }
}
