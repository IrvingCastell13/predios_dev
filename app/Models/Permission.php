<?php

namespace App\Models;

use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements PermissionContract
{
    protected $guarded = [];
}
