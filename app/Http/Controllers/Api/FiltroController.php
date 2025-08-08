<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Edificios;
use App\Models\Niveles;
use App\Models\Predios;
use App\Models\Sistema;
use App\Models\Subsistema;
use App\Models\Zonas;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\CategoriasDocumentos;
use App\Models\GruposDoc;
use App\Models\Personas;

class FiltroController extends BaseController
{
    /**
     * Devuelve una lista de todos los predios activos.
     */
    public function getPredios()
    {
        $data = Predios::where('Borrado', 0)
            ->select('IDPredio', 'NombrePredio')
            ->orderBy('NombrePredio')
            ->get();

        // Usamos response()->json() para devolver una respuesta JSON estándar
        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve edificios basados en una lista opcional de IDs de predios.
     */
    public function getEdificios(Request $request)
    {
        $request->validate(['predios' => 'sometimes|array']);
        $predioIds = $request->input('predios', []);

        $query = Edificios::where('Borrado', 0)
            ->select('IDEdificio', 'NombreEdificio', 'IDPredio');

        // Si el frontend envía IDs de predios, filtramos por ellos
        if (!empty($predioIds)) {
            $query->whereIn('IDPredio', $predioIds);
        }

        $data = $query->orderBy('NombreEdificio')->get();
        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve niveles basados en una lista opcional de IDs de edificios.
     */
    public function getNiveles(Request $request)
    {
        $request->validate(['edificios' => 'sometimes|array']);
        $edificioIds = $request->input('edificios', []);

        $query = Niveles::where('Borrado', 0)
            ->select('IDNivel', 'NombreNivel', 'IDEdificio');

        if (!empty($edificioIds)) {
            $query->whereIn('IDEdificio', $edificioIds);
        }

        $data = $query->orderBy('NombreNivel')->get();
        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve zonas basadas en una lista opcional de IDs de niveles.
     */
    public function getZonas(Request $request)
    {
        $request->validate(['niveles' => 'sometimes|array']);
        $nivelIds = $request->input('niveles', []);

        $query = Zonas::where('Borrado', 0)
            ->select('IDZona', 'NombreZona', 'IDNivel');

        if (!empty($nivelIds)) {
            $query->whereIn('IDNivel', $nivelIds);
        }

        $data = $query->orderBy('NombreZona')->get();
        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve una lista de todos los subsistemas activos.
     */
    public function getSubsistemas()
    {
        $data = Subsistema::where('Borrado', 0)
            ->select('IDSubsistema', 'NombreSubsistema', 'IDSistema')
            ->orderBy('NombreSubsistema')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function getSistemasConSubsistemas(Request $request)
    {
        try {
            $sistemas = Sistema::with('subsistemas')->orderBy('NombreSistema')->get();
            return $this->successResponse($sistemas, 'Sistemas y subsistemas obtenidos.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // --- NUEVA FUNCIÓN PARA CATEGORÍAS ---
    public function getDocumentosCategorias()
    {
        try {
            $data = GruposDoc::where('Borrado', 0)
                ->orderBy('NombreGrupoDoc', 'asc')
                ->get();

            return $this->successResponse($data, 'ok');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // --- NUEVA FUNCIÓN PARA SUBCATEGORÍAS ---
    public function getDocumentosSubcategorias()
    {
        try {
            $data = CategoriasDocumentos::where('Borrado', 0)
                ->orderBy('NombreCategoriaDoc', 'asc')
                ->get();

            return $this->successResponse($data, 'ok');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getResponsables()
    {
        try {
            // Suponiendo que 'Borrado = 0' es el criterio para usuarios activos,
            // que ya está manejado por un Global Scope en tu modelo Personas.
            $data = Personas::select('IDPersona', 'NombrePersona', 'ApellidoPaternoPersona', 'ApellidoMaternoPersona')
                ->orderBy('NombrePersona')
                ->get();

            // El accesor 'full_name' se aplicará automáticamente a cada modelo en la colección.
            // Para asegurar que se envíe en el JSON, lo seleccionamos explícitamente si es necesario o simplemente confiamos en el $appends.
            // En este caso, como 'full_name' está en $appends, no se necesita hacer nada más.

            return $this->successResponse($data, 'ok');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
