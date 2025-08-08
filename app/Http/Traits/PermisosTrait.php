<?php

namespace App\Http\Traits;

use App\Models\MsRolesClientes;
use App\Models\MsRolesEmpresas;
use App\Models\MsRolesPredios;
use App\Models\MsRolesProveedor;
use App\Models\MsRolesZonas;
use App\Models\Predios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Spatie\Permission\Models\Role;

trait PermisosTrait
{
    use CambiarClienteUsuarioTrait;
    use VerificarTicketsPersonas;

    /**
     * Verifica si el usuario tiene permiso para acceder a los predios.
     *
     * Este método consulta la base de datos para determinar si el usuario,
     * identificado por su ID, tiene el permiso correspondiente para el predio
     * especificado. Si el parámetro $es_publico es verdadero, se verifica
     * si el usuario tiene acceso a "catalogos publicos".
     *
     * @param  string  $permiso  El permiso específico que se está verificando.
     * @param  bool  $es_publico  Indica si se está verificando un permiso público.
     * @return bool Retorna verdadero si el usuario tiene el permiso, falso en caso contrario.
     */
    public function verificarPermisoPredios($permiso, $es_publico)
    {
        $request = request();
        $usuario = Auth::guard('api')->user();

        // * SI ES ADMIN CAMBIA LOS VALORES A LOS QUE VIENE DEL REQUEST DROPDOWNS
        if ($this->verificarPermisoCambioManualUsuario()) {
            $usuario->IDPersona = is_null($request->IDPersona) ? $usuario->IDPersona : $request->IDPersona;
            $usuario->IDCliente = is_null($request->IDCliente) ? $usuario->IDCliente : $request->IDCliente;
        }

        // * CHECA SI EL USUARIO Y CLIENTE TIENE ROL EN MS ROLES CLIENTES
        $clienteRol = MsRolesClientes::where('IDPersona', $usuario->IDPersona)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        // * SI TIENE ENTRA  LA VALIDACION
        if ($clienteRol) {

            $role = Role::find($clienteRol->IDRol);

            // * VERIFICA SI TIENE EL PERMISO DEL ROL MS CLIENTES
            if ($role->hasPermissionTo($permiso)) {
                // * SI TIENE REQGRESA TRUE Y DEJA PASAR
                return true;
            } else {
                // * SI NO TIENE EL PERMISO EN EL ROL DE CLIENTE ENTRA A VERIFICAR A MS ROLES EN PREDIOS

                $predios = Predios::where('IDCliente', $usuario->IDCliente)->pluck('IDPredio');

                $personaPredios = MsRolesPredios::where('IDPersona', $usuario->IDPersona)
                    ->whereIn('IDPredio', $predios)
                    ->where('IDCliente', $usuario->IDCliente)
                    ->get();

                foreach ($personaPredios as $personaPredio) {
                    $role = Role::find($personaPredio->IDRol);

                    if (! $role) {
                        continue; // Ignoramos si no existe
                    }

                    // Validamos si tiene el permiso requerido según contexto
                    $tienePermiso = $es_publico
                        ? $role->hasPermissionTo('catalogos publicos')
                        : $role->hasPermissionTo($permiso);

                    if ($tienePermiso) {
                        return true;
                    }
                }

                return false; // Ningún rol tenía el permiso requerido

            }
        }

        $predios = Predios::where('IDCliente', $usuario->IDCliente)->pluck('IDPredio');

        $personaPredios = MsRolesPredios::where('IDPersona', $usuario->IDPersona)
            ->whereIn('IDPredio', $predios)
            ->where('IDCliente', $usuario->IDCliente)
            ->get();

        foreach ($personaPredios as $personaPredio) {
            $role = Role::find($personaPredio->IDRol);

            if (! $role) {
                continue; // Ignoramos si no existe
            }

            // Validamos si tiene el permiso requerido según contexto
            $tienePermiso = $es_publico
                ? $role->hasPermissionTo('catalogos publicos')
                : $role->hasPermissionTo($permiso);

            if ($tienePermiso) {
                return true;
            }
        }

        return false; // Ningún rol tenía el permiso requerido

    }

    public function verificarPermisoProveedor($permiso, $es_publico)
    {

        $request = request();

        $usuario = Auth::guard('api')->user();

        $tokenId = $usuario->token()->id;

        $registroToken = Token::where('id', $tokenId)->where('revoked', false)->first();

        if ($usuario->IDPersona !== $registroToken->user_id) {
            return $this->errorResponse('No tienes autorizacion para realizar esta acción', 500);
        }

        $provvedorRol = MsRolesProveedor::where('IDPersona', $usuario->IDPersona)
            ->where('IDProveedor', $request->IDProveedor)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($es_publico) {

            return $provvedorRol && $provvedorRol->hasPermissionTo('catalogos publicos');

        }

        return $provvedorRol && $provvedorRol->hasPermissionTo($permiso);
    }

    public function verificarPermisoEmpresa($permiso, $es_publico)
    {

        $request = request();

        $usuario = Auth::guard('api')->user();

        $tokenId = $usuario->token()->id;

        $registroToken = Token::where('id', $tokenId)->where('revoked', false)->first();

        if ($usuario->IDPersona !== $registroToken->user_id) {
            return $this->errorResponse('No tienes autorizacion para realizar esta acción', 500);
        }

        $empresaRol = MsRolesEmpresas::where('IDPersona', $usuario->IDPersona)
            ->where('IDEmpresa', $request->IDEmpresa)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($es_publico) {

            return $empresaRol && $empresaRol->hasPermissionTo('catalogos publicos');

        }

        return $empresaRol && $empresaRol->hasPermissionTo($permiso);
    }

    public function verificarPermisoZona($permiso, $es_publico)
    {

        $request = request();

        $usuario = Auth::guard('api')->user();

        $tokenId = $usuario->token()->id;

        $registroToken = Token::where('id', $tokenId)->where('revoked', false)->first();

        if ($usuario->IDPersona !== $registroToken->user_id) {
            return $this->errorResponse('No tienes autorizacion para realizar esta acción', 500);
        }

        $zonaRol = MsRolesZonas::where('IDPersona', $usuario->IDPersona)
            ->where('IDZona', $request->IDZona)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($es_publico) {

            return $zonaRol && $zonaRol->hasPermissionTo('catalogos publicos');

        }

        return $zonaRol && $zonaRol->hasPermissionTo($permiso);
    }

    public function verificarPermisoCliente($permiso, $es_publico)
    {

        $request = request();

        $usuario = Auth::guard('api')->user();

        if ($this->verificarPermisoCambioManualUsuario()) {
            $usuario->IDPersona = is_null($request->IDPersona) ? $usuario->IDPersona : $request->IDPersona;
            $usuario->IDCliente = is_null($request->IDCliente) ? $usuario->IDCliente : $request->IDCliente;
        }

        $clienteRol = MsRolesClientes::where('IDPersona', $usuario->IDPersona)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($clienteRol) {

            $role = Role::find($clienteRol->IDRol);
            if ($es_publico) {
                return $role->hasPermissionTo('catalogos publicos');
            }

            return $role->hasPermissionTo($permiso);
        }

        return false;
    }

    /**
     * Verifica si el usuario tiene permiso para ver la configuración de acciones.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function verificarPermisoZonas($permiso, $es_publico)
    {
        return true;
    }

    public function verificarRolCliente()
    {
        $usuario = Auth::guard('api')->user();

        $clienteRol = MsRolesClientes::where('IDPersona', $usuario->IDPersona)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        if ($clienteRol) {
            // return true;
            return $clienteRol && $clienteRol->hasPermissionTo('Administrador');
        }

        return false;
    }

    protected function obtenerPersonaLogueada()
    {
        return Auth::guard('api')->user();
    }

    protected function verificarPermiso($permiso, $es_publico = false, $type = 'predio')
    {
        // return true;

        switch ($type) {
            case 'predio':
                if (! $this->verificarPermisoPredios($permiso, $es_publico)) {
                    return false;
                } else {
                    if (! $this->verificarPermisoZonas($permiso, $es_publico)) {
                        return false;
                    } else {
                        return true;
                    }
                }
                break;

            case 'proveedor':
                return $this->verificarPermisoProveedor($permiso, $es_publico);
                break;
            case 'empresa':
                return $this->verificarPermisoEmpresa($permiso, $es_publico);
                break;

            case 'zona':
                return $this->verificarPermisoZona($permiso, $es_publico);
                break;

            case 'cliente':

                return $this->verificarPermisoCliente($permiso, $es_publico);
                break;

            default:
                // code...
                break;
        }

    }
}
