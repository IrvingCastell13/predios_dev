<?php

namespace App\Http\Traits;

use App\Models\MsRolesPredios;
use App\Models\Personas;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

trait VerificarTicketsPersonas
{
    public function verificarTicketPermiso()
    {

        // return true;
        $request = request();

        if (! is_null($request->IDPersona)) {
            $usuario = Personas::findOrfail($request->IDPersona);
        } else {
            $usuario = Auth::guard('api')->user();
        }

        if ($this->verificarPermisoCambioManualUsuario()) {
            $usuario->IDPersona = is_null($request->IDPersona) ? $usuario->IDPersona : $request->IDPersona;
            $usuario->IDCliente = is_null($request->IDCliente) ? $usuario->IDCliente : $request->IDCliente;
        }

        $personaPredio = MsRolesPredios::where('IDPersona', $usuario->IDPersona)
            // ->where('IDRol', $request->IDRol)
            ->where('IDPredio', $request->IDPredio)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($personaPredio) {
            $role = Role::find($personaPredio->IDRol);

            return $role->hasPermissionTo('ver tickets personas');
        }

        return false;

    }
}
