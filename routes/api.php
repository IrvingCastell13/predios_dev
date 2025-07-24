<?php

use App\Http\Controllers\Api\EquipoController;
use App\Http\Controllers\Api\ReporteDocumentosController;
use App\Http\Controllers\Api\ReporteEquiposController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/reportes/documentos/estado-por-predio', [ReporteDocumentosController::class, 'estadoPorPredio']);

Route::get('/reportes/documentos/documentos-por-predio-con-archivos', [ReporteDocumentosController::class, 'documentosPorPredioConArchivos']);

Route::get('/reportes/documentos/resumen-por-grupo', [ReporteDocumentosController::class, 'resumenPorGrupo']);

Route::get('/reportes/documentos/resumen-por-categoria', [ReporteDocumentosController::class, 'resumenPorSubcategoria']);

Route::get('/reportes/documentos/resumen-general', [ReporteDocumentosController::class, 'cumplimientoPonderadoTotal']);


//endpoint para obtener la lista de predios
Route::get('/bi/listar-predios', [ReporteDocumentosController::class, 'listarPredios']);
// Rutas para los reportes de BI (Gráficas)
Route::get('/bi/matriz-subcategoria', [ReporteDocumentosController::class, 'matrizPorSubcategoria']);
Route::get('/bi/matriz-subcategoria-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorSubcategoria']);

Route::get('/bi/calificaciones-ponderadas', [ReporteDocumentosController::class, 'calificacionesPonderadasPorPredio']);

Route::get('/bi/matriz-grupo', [ReporteDocumentosController::class, 'matrizPorGrupo']);
Route::get('/bi/matriz-grupo-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorGrupo']);

// Ruta para el nuevo reporte de estado de renovacion
Route::get('/bi/estado-renovacion-documentos', [ReporteDocumentosController::class, 'estadoAccionesPorRenovacion']);

// Ruta para el nuevo reporte de estado de MANTENIMIENTO
Route::get('/bi/estado-mantenimiento-equipos', [ReporteEquiposController::class, 'estadoAccionesPorMantenimiento']);

// Rutas para poblar los nuevos filtros de sistema/subsistema
Route::get('/bi/listar-sistemas', [ReporteEquiposController::class, 'listarSistemas']);
Route::get('/bi/listar-subsistemas', [ReporteEquiposController::class, 'listarSubsistemas']);

Route::get('/bi/listar-grupos-doc', [ReporteDocumentosController::class, 'listarGruposDoc']);
Route::get('/bi/listar-categorias-doc', [ReporteDocumentosController::class, 'listarCategoriasDoc']);





// Endpoints para poblar los dropdowns de los filtros
Route::get('/obtener-sistemas-equipos', [EquipoController::class, 'obtenerSistemas'])->name('obtenerSistemasEquiposCliente');
Route::get('/obtener-subsistemas-equipos', [EquipoController::class, 'obtenerSubsistemas'])->name('obtenerSubsistemasEquiposCliente');
Route::get('/obtener-tipos-equipos', [EquipoController::class, 'obtenerTipos'])->name('tipoEquipos.index');
Route::get('/obtener-equipos-cliente', [EquipoController::class, 'obtenerEquiposCliente'])->name('obtenerEquiposCliente');

// Endpoints para el CRUD principal de Equipos (Create, Read, Update, Delete)
Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');
Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
Route::get('/equipos/{id}', [EquipoController::class, 'show'])->name('equipos.show');
Route::put('/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');

// Endpoints para funcionalidades adicionales
Route::post('/equipos/update-archivos', [EquipoController::class, 'updateArchivos'])->name('equipo.updateArchivos');
Route::post('/equipos-pdf', [EquipoController::class, 'generarPDF'])->name('equiposPDF');

// Rutas para los nuevos filtros del dashboard de cumplimiento
Route::get('/bi/listar-tipos-documento', [ReporteDocumentosController::class, 'listarTiposDocumento']);
Route::get('/bi/listar-tipos-inmueble', [ReporteDocumentosController::class, 'listarTiposInmueble']);

// Ruta para la nueva pestaña de Vigencia
Route::get('/bi/documentos-con-estado', [ReporteDocumentosController::class, 'documentosConEstadoPorCategoria']);
Route::get('/bi/documentos-por-subcategoria', [ReporteDocumentosController::class, 'documentosPorSubcategoria']);
Route::get('/bi/tabla-detallada-vigencia', [ReporteDocumentosController::class, 'tablaDetalladaVigencia']);

Route::get('/bi/porcentaje-vigencia-por-predio', [ReporteDocumentosController::class, 'porcentajeVigenciaPorPredio']);
