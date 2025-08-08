<?php

namespace App\Helpers;

use App\Models\Archivos;
use App\Models\CategoriasTikets;
use App\Models\DevicesPersona;
use App\Models\Follow;
use App\Models\Funciones;
use App\Models\MetadatosGenerales;
use App\Models\Modulo;
use App\Models\MsRolesClientes;
use App\Models\MsRolesPredios;
use App\Models\NivelServicio;
use App\Models\Predios;
use App\Models\SubcategoriasTicket;
use App\Models\TiposMetadatos;
use App\Models\TiposTickets;
use App\Models\TrackInstancias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class MetikHelper
{
    /**
     * Guarda un archivo en el sistema.
     *
     * Este método es utilizado para guardar un archivo en el sistema utilizando
     * el identificador único (UUID) y el ID del cliente asociado al archivo.
     *
     * @param  mixed  $file  Archivo que se va a guardar.
     * @param  string  $uuid  Identificador único para el archivo.
     * @param  int  $idCliente  Identificador del cliente asociado al archivo.
     */
    public static function guardarArchivo($file, $uuid, $idCliente, $IDPersona)
    {

        $usuario = Auth::guard('api')->user();
        $Path = 'track_archivos/';
        $nombreArchivo = pathinfo(trim($file->getClientOriginalName()), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $tamano = $file->getSize();

        // dd($nombreArchivo.'.'.$extension);

        // $file->storeAs($Path, $nombreArchivo.'.'.$extension, 'public');

        $stringS3 = Storage::disk('s3')->put($usuario->IDCliente.'/'.$nombreArchivo.'.'.$extension, $file);

        $s3 = basename($stringS3);


        $maxNumArchivo = Archivos::where('IDObjetoPadreArchivo', $uuid)->max('NumArchivo');

        $numArchivo = $maxNumArchivo ? $maxNumArchivo + 1 : 1; // Incrementar o iniciar en 1
        $numArchivoAnterior = $numArchivo - 1;

        $archivoAnterior = Archivos::where('IDObjetoPadreArchivo', $uuid)->orderBy('NumArchivo', 'asc')->whereNull('IDArchivoPadreArchivo')->first();

        $archivo = Archivos::create([
            'IDSublote' => 'e14845ca-627d-11f0-9f7b-00090faa0001',
            'NumArchivo' => $numArchivo,
            'NombreOriginalArchivo' => $nombreArchivo,
            'ExtensionArchivo' => $extension,
            's3' => $s3,
            'TamanoArchivo' => $tamano,
            'IDObjetoPadreArchivo' => $uuid,
            'IDPermisoVerArchivo' => 1,
            'IDPermisoAbrirArchivo' => 1,
            'IDCliente' => $idCliente,
            'IDPersona' => $IDPersona,
        ]);

        // Actualizar el archivo con IDArchivoPadreArchivo igual al ID del archivo recién creado
        if (! is_null($archivoAnterior)) {
            // dd($archivoAnterior->IDArchivo);
            $archivo->update([
                'IDArchivoPadreArchivo' => $archivoAnterior->IDArchivo,
            ]);
        }

        return $archivo;

        // return $filePath; // Opcional: devolver la ruta del archivo guardado
    }

    public static function guardarImagen($file, $path)
    {

        $usuario = Auth::guard('api')->user();
        $Path = "web/imgs/$path";
        $nombreArchivo = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $extension = $file->getClientOriginalExtension();

        $stringS3 = Storage::disk('s3')->put($usuario->IDCliente.'/'.$nombreArchivo.'.'.$extension, $file);

        return $stringS3;

        // return $filePath; // Opcional: devolver la ruta del archivo guardado
    }

    /**
     * Crea una nueva instancia de seguimiento en la base de datos.
     *
     * Este método se utiliza para registrar una nueva instancia de seguimiento
     * con la información proporcionada, incluyendo el flujo, estado actual,
     * estado anterior, último evento, fecha del último evento, persona en tránsito
     * y cliente asociado.
     *
     * @param  int  $IDFlujo  Identificador del flujo asociado a la instancia.
     * @param  int  $IDEstadoActualInstancia  Identificador del estado actual de la instancia.
     * @param  int  $IDEstadoAnteriorInstancia  Identificador del estado anterior de la instancia.
     * @param  int  $IDUltimoEventoInstancia  Identificador del último evento asociado a la instancia.
     * @param  string  $FechaUltimoEventoInstancia  Fecha del último evento en formato adecuado.
     * @param  int  $IDPersonaTransitoInstancia  Identificador de la persona en tránsito asociada a la instancia.
     * @param  int  $IDCliente  Identificador del cliente asociado a la instancia.
     * @return TrackInstancias Devuelve la instancia creada.
     */
    public static function trackInstancia($IDFlujo, $IDEstadoActualInstancia, $IDEstadoAnteriorInstancia, $IDUltimoEventoInstancia, $FechaUltimoEventoInstancia, $IDPersonaTransitoInstancia, $IDCliente)
    {
        $instancia = TrackInstancias::create([
            'IDFlujo' => $IDFlujo,
            'IDEstadoActualInstancia' => $IDEstadoActualInstancia,
            'IDEstadoAnteriorInstancia' => $IDEstadoAnteriorInstancia,
            'IDUltimoEventoInstancia' => $IDUltimoEventoInstancia,
            'FechaUltimoEventoInstancia' => $FechaUltimoEventoInstancia,
            'IDPersonaTransitoInstancia' => $IDPersonaTransitoInstancia,
            'IDCliente' => $IDCliente,
        ]);

        return $instancia->IDInstancia;
    }

    public static function eliminarImagen($url)
    {
        try {

            return true;
            // Suponiendo que tienes un modelo Imagen que almacena registros de imágenes

            // Construir la ruta completa del archivo
            $rutaArchivo = storage_path('app/public/'.$url);

            // Verificar si el archivo existe y eliminarlo
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }

            return true;
        } catch (\Exception $e) {
            // Log the error or handle it as per your needs
            dd($e);

            return false;
        }
    }

    public static function obtenerFuncionesPermisos()
    {
        $request = request();

        $usuario = Auth::guard('api')->user();

        $personaPredio = MsRolesPredios::where('IDPersona', $usuario->IDPersona)
            // ->where('IDRol', $request->IDRol)
            ->where('IDPredio', $request->IDPredio)
            ->where('IDCliente', $usuario->IDCliente)
            ->first();

        $permisos = $personaPredio->getPermissionsViaRoles()->pluck('id');

        // $modulos = Modulo::with('opciones')->whereIn('IDPermiso', $permisos)->get();

        $funciones = Funciones::with('permiso')->whereIn('PermisoFuncion', $permisos)->get();

        return $funciones;
    }

    public static function obtenerEmailsFollows($IDObjeto)
    {

        $follows = Follow::select('p.IDPersona', 'p.EmailPersona', DB::raw("CONCAT(p.NombrePersona, ' ', p.ApellidoPaternoPersona, ' ', p.ApellidoMaternoPersona) as full_name"))
            ->join('ms_personas as p', 'p.IDPersona', 'follows.IDPersona')
            ->whereIn('IDObjeto', $IDObjeto)
            ->where('follows.borrado', 0)
            ->groupBy('p.EmailPersona', 'p.IDPersona')
            ->withOutGlobalScopes()
            ->get()->ToArray();

        return $follows;
    }

    public static function obtenerDevicesPersona($IDPersona)
    {

        $devices = DevicesPersona::where('IDPersona', $IDPersona)
            ->where('Estado', 1)
            ->withOutGlobalScopes()
            ->groupBy('IDDevice')
            ->get()->toArray();

        return $devices;
    }

    public static function obtenerIDModulo($nombre)
    {

        $modulo = Modulo::where('NombreModulo', $nombre)->first();

        if ($modulo) {
            return $modulo->IDModulo;
        } else {
            return null;
        }
    }

    public static function duplicarCatalogos($IDCliente)
    {

        DB::beginTransaction();

        $catalogosArray = [
            NivelServicio::class,
            CategoriasTikets::class,

            // metadatos generales

            TiposMetadatos::class,
        ];

        foreach ($catalogosArray as $catalogo) {

            $obtenerRegistrosPrivados = $catalogo::where('EsPublico', 1)->get();

            foreach ($obtenerRegistrosPrivados as $registroPublico) {
                $nuevoRegistro = $registroPublico->replicate();
                $nuevoRegistro->EsPublico = 0;
                $nuevoRegistro->IDCliente = $IDCliente;

                $nuevoRegistro->save();
            }

        }

        $subcategorias = SubcategoriasTicket::where('EsPublico', 1)->get();

        foreach ($subcategorias as $sub) {

            try {
                // code...
                $nuevoRegistro = $sub->replicate();

                $categoriaPublica = CategoriasTikets::where('IDCatTicket', $nuevoRegistro->IDCatTicket)->where('Borrado', 0)->where('EsPublico', 1)->first();

                $categoriaPrivada = CategoriasTikets::where('NombreCatTicket', $categoriaPublica->NombreCatTicket)
                    ->where('EsPublico', 0)
                    ->where('IDCliente', $IDCliente)
                    ->where('borrado', 0)->first();

                // dump($query->toSql(), $query->getBindings());
                // if($categoriaPrivada){

                $nuevoRegistro->EsPublico = 0;

                $nuevoRegistro->IDCliente = $IDCliente;
                $nuevoRegistro->IDCatTicket = $categoriaPrivada->IDCatTicket;
                $nuevoRegistro->save();
                // }
            } catch (\Throwable $th) {
                // throw $th;

            }
        }

        $tipos = TiposTickets::where('EsPublico', 1)->get();

        foreach ($tipos as $tipo) {

            try {
                $nuevoRegistro = $tipo->replicate();
                // dd($nuevoRegistro);
                // Modificar propiedades del nuevo registro
                $subcategoria = SubcategoriasTicket::where('IDSubCatTicket', $nuevoRegistro->IDSubCatTicket)->where('Borrado', 0)->where('EsPublico', 1)->first();

                $subcategoriaPrivada = SubcategoriasTicket::where('NombreSubCatTicket', $subcategoria->NombreSubCatTicket)
                    ->where('EsPublico', 0)
                    ->where('IDCliente', $IDCliente)
                    ->first();

                $nivelServicio = NivelServicio::where('IDNivelServicio', $nuevoRegistro->IDNivelServicio)->where('Borrado', 0)->where('EsPublico', 1)->first();

                $nivelServicioPrivado = NivelServicio::where('NombreNivelServicio', $nivelServicio->NombreNivelServicio)
                    ->where('EsPublico', 0)
                    ->where('IDCliente', $IDCliente)
                    ->first();

                if ($nivelServicioPrivado) {

                    $nuevoRegistro->EsPublico = 0;
                    $nuevoRegistro->IDCliente = $IDCliente;
                    $nuevoRegistro->IDSubCatTicket = $subcategoriaPrivada->IDSubCatTicket;
                    $nuevoRegistro->IDNivelServicio = $nivelServicioPrivado->IDNivelServicio;
                    // Guardar el nuevo registro
                    $nuevoRegistro->save();
                }
            } catch (\Throwable $th) {

            }

        }

        $metadatos = MetadatosGenerales::where('EsPublico', 1)->get();

        foreach ($metadatos as $metadato) {

            $nuevoRegistro = $metadato->replicate();

            $tipoMetadatoPublico = TiposMetadatos::where('IDTipoMetadato', $nuevoRegistro->IDTipoMetadato)->where('Borrado', 0)->where('EsPublico', 1)->first();

            $tipoMetadatoPrivado = TiposMetadatos::where('NombreTipoMetadato', $tipoMetadatoPublico->NombreTipoMetadato)
                ->where('EsPublico', 0)
                ->where('IDCliente', $IDCliente)
                ->first();

            $nuevoRegistro->EsPublico = 0;

            $nuevoRegistro->IDCliente = $IDCliente;
            $nuevoRegistro->IDTipoMetadato = $tipoMetadatoPrivado->IDTipoMetadato;
            $nuevoRegistro->save();
        }

        DB::commit();

    }

    public static function notificationSuccess($deviceId, $body = '', $title = '', $subtitle = '')
    {
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key='.env('API_SERVER_KEY_FIREBASE'),
        ])
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $deviceId,
                'notification' => [
                    // 'tag' => $dataset['chatId'],
                    'body' => $body,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    // 'image' => $dataset['chatId']
                ],
                // "data" => $dataset
            ])->json();
    }

    public static function CheckPermisoCliente($permiso)
    {

        $persona = Auth::guard('api')->user();

        $clienteRol = MsRolesClientes::where('IDPersona', $persona->IDPersona ?? null)
            ->where('IDCliente', $persona->IDCliente ?? null)
            ->pluck('IDRol');


        $HasPermissionCliente = Role::whereIn('id', $clienteRol)
            ->whereHas('permissions', function ($query) use ($permiso) {
                $query->where('name', $permiso);
            })
            ->exists();

        return $HasPermissionCliente;
    }

    public static function TienePermisoPredios(string $permisoNombre): bool
        {
            $roles = Role::whereHas('permissions', function ($query) use ($permisoNombre) {
                $query->where('name', $permisoNombre);
            })->pluck('id');

            $persona = Auth::guard('api')->user();

            $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)
                ->where('IDCliente', $persona->IDCliente ?? null)
                ->where('IDPersona', $persona->IDPersona ?? null)
                // ->where('IDPredio', $this->IDPredio)
                ->pluck('IDPredio');
            if (count($IDpredios) == 0) {
                return false;
            } else {
                return true;
            }
        }


    public static function obtenerPrediosPermisoVerBi()
    {
        try {

            DB::beginTransaction();
            $usuario = Auth::guard('api')->user();

            $roles = Role::whereHas('permissions', function ($query) {
                $query->where('name', 'BI tickets');
            })->pluck('id');

            $IDpredios = MsRolesPredios::whereIn('IDRol', $roles)->where('IDCliente', $usuario->IDCliente)->where('IDPersona', $usuario->IDPersona)
                ->pluck('IDPredio');

            $predios = Predios::orderBy('NombrePredio', 'ASC')->whereIn('IDPredio', $IDpredios)->pluck('IDPredio');

            return $predios;

        } catch (\Exception $e) {
            return [];
        }
    }

    public static function tieneRolCliente()
    {
        $usuario = Auth::guard('api')->user();

        $clienteRol = MsRolesClientes::where('IDPersona', $usuario->IDPersona)
        ->where('IDCliente', $usuario->IDCliente)
        ->pluck('IDRol');

        return $clienteRol;
    }

    public static function getImageAwsS3($archivo)
    {

       $user = Auth::guard('api')->user();

        $IDCliente = $user->IDCliente ?? null;

        if($archivo->manual == 1){
            $urlPrivate = $IDCliente.'/'.$archivo->s3;
        }else{
            $urlPrivate = $IDCliente.'/'.$archivo->NombreOriginalArchivo.'.'.$archivo->ExtensionArchivo.'/'.$archivo->s3;
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $urlPrivate,
            now()->addMinutes(20)
        );

        return $url;

    }
}
