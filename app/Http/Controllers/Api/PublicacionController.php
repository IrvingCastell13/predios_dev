<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Helper para generar UUIDs

class PublicacionController extends Controller
{

    public function index(Request $request)
    {
        // Validar que el IDPredio sea requerido y exista.
        $validated = $request->validate([
            'IDPredio' => 'required|string|exists:conf_predios,IDPredio',
            'IDTema'   => 'nullable|string|exists:pu_tema,IDTema', // El tema es opcional
        ]);

        try {
            // Iniciar la consulta a la tabla de publicaciones
            $query = DB::table('pu_publicacion as pub')
                // Unir tablas para obtener nombres en lugar de solo IDs
                ->join('conf_predios as predio', 'pub.IDPredio', '=', 'predio.IDPredio')
                ->join('pu_tema as tema', 'pub.IDTema', '=', 'tema.IDTema')
                ->join('ms_personas as creador', 'pub.IDSolicitantePublicacion', '=', 'creador.IDPersona')
                ->leftJoin('arch_archivos as archivo', 'pub.IDArchivo', '=', 'archivo.IDArchivo') // LeftJoin por si no hay archivo

                // Seleccionar los campos que se devolverán
                ->select(
                    'pub.IDPublicacion',
                    'pub.ResumenPublicacion as titulo',
                    'pub.DescripcionPublicacion as descripcion',
                    'pub.InicioPublicacion',
                    'pub.FinPublicacion',
                    'predio.NombrePredio',
                    'tema.NombreTema as categoria',
                    DB::raw("CONCAT(creador.NombrePersona, ' ', creador.ApellidoPaternoPersona) as nombre_creador"),
                    'archivo.NombreOriginalArchivo as nombre_archivo'
                );

            // Aplicar el filtro OBLIGATORIO por predio
            $query->where('pub.IDPredio', $validated['IDPredio']);

            // Aplicar el filtro OPCIONAL por tema (categoría)
            if ($request->filled('IDTema')) {
                $query->where('pub.IDTema', $validated['IDTema']);
            }

            // Ordenar los resultados (ej. por fecha de creación descendente)
            $publicaciones = $query->orderBy('pub.FechaCreacionObjeto', 'desc')->get();

            // Devolver la respuesta en formato JSON
            return response()->json($publicaciones);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las publicaciones', 'details' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'IDTema'                   => 'required|exists:pu_tema,IDTema',
            'IDSolicitantePublicacion' => 'required|exists:ms_personas,IDPersona',
            'IDAutorizadorPublicacion' => 'required|exists:ms_personas,IDPersona',
            'IDPredio'                 => 'required|exists:conf_predios,IDPredio',
            'IDCliente'                => 'required|exists:conf_clientes,IDCliente',
            'ResumenPublicacion'       => 'required|string|max:45',
            'DescripcionPublicacion'   => 'required|string',
            'InicioPublicacion'        => 'required|date',
            'FinPublicacion'           => 'required|date',
            'archivo'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ]);

        try {
            DB::beginTransaction();

            $idPublicacion = Str::uuid()->toString();
            $idArchivo = null;

            // Insertamos primero en la tabla padre 'track_instancias' para cumplir con la restricción.
            // Usamos el mismo UUID que usará la publicación.
            DB::table('track_instancias')->insert([
                'IDInstancia' => $idPublicacion,
                'IDCliente'   => $validated['IDCliente'], // Este campo es requerido en track_instancias
                // Los demás campos de track_instancias pueden ser NULL o tener valores por defecto.
                // Cuando implementes la lógica completa, aquí pondrás el IDFlujo, IDEstadoActual, etc.
            ]);

            $datosPublicacion = [
                'IDPublicacion'            => $idPublicacion,
                'IDTema'                   => $validated['IDTema'],
                'IDSolicitantePublicacion' => $validated['IDSolicitantePublicacion'],
                'IDAutorizadorPublicacion' => $validated['IDAutorizadorPublicacion'],
                'ResumenPublicacion'       => $validated['ResumenPublicacion'],
                'DescripcionPublicacion'   => $validated['DescripcionPublicacion'],
                'IDPredio'                 => $validated['IDPredio'],
                'IDCliente'                => $validated['IDCliente'],
                'InicioPublicacion'        => $validated['InicioPublicacion'],
                'FinPublicacion'           => $validated['FinPublicacion'],
                'VencePublicacion'         => 1,
                'FechaCreacionObjeto'      => now(),
                'FechaActualizacionObjeto' => now(),
            ];

            if ($request->hasFile('archivo')) {
                $idArchivo = Str::uuid()->toString();
                $datosPublicacion['IDArchivo'] = $idArchivo;
                $path = $request->file('archivo')->store('publicaciones');
                $nombreOriginal = $request->file('archivo')->getClientOriginalName();
                $extension = $request->file('archivo')->getClientOriginalExtension();
                $tamano = $request->file('archivo')->getSize();

                DB::table('arch_archivos')->insert([
                    'IDArchivo'             => $idArchivo,
                    'IDObjetoPadreArchivo'  => $idPublicacion,
                    'NombreOriginalArchivo' => $nombreOriginal,
                    'ExtensionArchivo'      => $extension,
                    'TamanoArchivo'         => $tamano,
                    'IDSublote'             => 'sublote-01',
                    'IDCliente'             => $validated['IDCliente'],
                    'FechaCreacionObjeto'   => now(),
                    'FechaActualizacionObjeto' => now(),
                    // 'archivo_path'  => $path,
                ]);
            }

            DB::table('pu_publicacion')->insert($datosPublicacion);

            DB::commit();

            return response()->json(['message' => 'Publicación creada con éxito', 'id' => $idPublicacion], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la publicación', 'details' => $e->getMessage()], 500);
        }
    }
}
