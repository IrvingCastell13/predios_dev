<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait CambiarClienteUsuarioTrait
{
    public function verificarPermisoCambioManualUsuario()
    {

        $usuario = Auth::guard('api')->user();

        if ($usuario->IDRol == 1) {

            if ($usuario->IDRol !== 1) {
                return false;
            }
            if ($usuario->IDRol == 1) {
                return true;
            }

        } else {

            return false;

        }

    }
}
