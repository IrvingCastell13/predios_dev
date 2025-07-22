<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    // Simula la respuesta para la tabla principal de equipos
    public function index()
    {
        return response()->json(['data' => []]); // Devuelve un arreglo vacío para que la tabla no falle
    }

    // Simula la respuesta para el detalle de un equipo
    public function show($id)
    {
        // Devuelve un objeto de equipo de ejemplo
        return response()->json(['data' => [
            'IDEquipo' => $id,
            'DescripcionEquipo' => 'Equipo de Ejemplo',
            'image' => 'https://via.placeholder.com/150',
            'subsistema' => ['NombreSubsistema' => 'Subsistema Demo', 'sistema' => ['NombreSistema' => 'Sistema Demo']],
            'tipo' => ['NombreTipoEquipo' => 'Tipo Demo', 'ModeloTipoEquipo' => 'Modelo-X', 'MarcaTipoEquipo' => 'Marca-Y'],
            'NoSerieEquipo' => 'SN-12345',
            'ClaveEquipo' => 'EQ-DEMO-01',
            'frecuenciaMantenimiento' => 'Mensual',
            'documentos' => []
        ]]);
    }

    // Simula la creación y actualización de equipos
    public function store(Request $request)
    {
        // Simplemente devuelve una respuesta exitosa
        return response()->json(['message' => 'Equipo creado con éxito (simulado)']);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Equipo actualizado con éxito (simulado)']);
    }

    public function updateArchivos(Request $request)
    {
         return response()->json(['message' => 'Archivos actualizados (simulado)']);
    }

    // Simula la generación de PDF
    public function generarPDF(Request $request)
    {
        // Devuelve una respuesta vacía exitosa
        return response()->json(['status' => 'ok']);
    }

    // --- Endpoints para los filtros (devuelven arreglos vacíos) ---
    public function obtenerSistemas()
    {
        return response()->json(['data' => []]);
    }

    public function obtenerSubsistemas()
    {
        return response()->json(['data' => []]);
    }

    public function obtenerTipos()
    {
        return response()->json(['data' => []]);
    }

    public function obtenerEquiposCliente()
    {
         return response()->json(['data' => []]);
    }
}