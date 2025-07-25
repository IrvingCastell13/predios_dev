<?php

use App\Http\Controllers\Api\OrdenTrabajoController;
use App\Http\Controllers\Api\PublicacionController;
use App\Http\Controllers\Api\ReporteDocumentosController;
use App\Http\Controllers\Api\ReporteEquiposController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//ENPOINTS
Route::get('/reportes/documentos/estado-por-predio', [ReporteDocumentosController::class, 'estadoPorPredio']);
Route::get('/reportes/documentos/documentos-por-predio-con-archivos', [ReporteDocumentosController::class, 'documentosPorPredioConArchivos']);
Route::get('/reportes/documentos/resumen-por-grupo', [ReporteDocumentosController::class, 'resumenPorGrupo']);
Route::get('/reportes/documentos/resumen-por-categoria', [ReporteDocumentosController::class, 'resumenPorSubcategoria']);
Route::get('/reportes/documentos/resumen-general', [ReporteDocumentosController::class, 'cumplimientoPonderadoTotal']);


//endpoint para obtener la lista de predios para filtros en DASHBOARD REPORTES
Route::get('/bi/listar-predios', [ReporteDocumentosController::class, 'listarPredios']);
//endpoint para obtener la lista de tipo de documentos para filtros en DASHBOARD REPORTES
Route::get('/bi/listar-tipos-documento', [ReporteDocumentosController::class, 'listarTiposDocumento']);
//endpoint para obtener la lista de tipo de inmueble para filtros en DASHBOARD REPORTES
Route::get('/bi/listar-tipos-inmueble', [ReporteDocumentosController::class, 'listarTiposInmueble']);

// endpoint para las graficas de Dashboard pestaña de Cumplimiento
Route::get('/bi/matriz-subcategoria', [ReporteDocumentosController::class, 'matrizPorSubcategoria']);
Route::get('/bi/matriz-subcategoria-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorSubcategoria']);
Route::get('/bi/calificaciones-ponderadas', [ReporteDocumentosController::class, 'calificacionesPonderadasPorPredio']);
Route::get('/bi/matriz-grupo', [ReporteDocumentosController::class, 'matrizPorGrupo']);
Route::get('/bi/matriz-grupo-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorGrupo']);

// endpoint las 3 graficas de Dashboard Renovacion
Route::get('/bi/estado-renovacion-documentos', [ReporteDocumentosController::class, 'estadoAccionesPorRenovacion']);

// endpoint para las 3 graficas de Dashboard Mantenimiento
Route::get('/bi/estado-mantenimiento-equipos', [ReporteEquiposController::class, 'estadoAccionesPorMantenimiento']);

// Enpoints para los filtros de Dashboard Mantenimiento (sistema y subsitema)
Route::get('/bi/listar-sistemas', [ReporteEquiposController::class, 'listarSistemas']);
Route::get('/bi/listar-subsistemas', [ReporteEquiposController::class, 'listarSubsistemas']);

// Enpoints para los filtros de Dashboard Renovacion (categoria y subcategoria)
Route::get('/bi/listar-grupos-doc', [ReporteDocumentosController::class, 'listarGruposDoc']);
Route::get('/bi/listar-categorias-doc', [ReporteDocumentosController::class, 'listarCategoriasDoc']);


// endpoint para la pestaña de Vigencia de Dashboard Reportes
Route::get('/bi/documentos-con-estado', [ReporteDocumentosController::class, 'documentosConEstadoPorCategoria']);
Route::get('/bi/documentos-por-subcategoria', [ReporteDocumentosController::class, 'documentosPorSubcategoria']);
Route::get('/bi/tabla-detallada-vigencia', [ReporteDocumentosController::class, 'tablaDetalladaVigencia']);
Route::get('/bi/porcentaje-vigencia-por-predio', [ReporteDocumentosController::class, 'porcentajeVigenciaPorPredio']);

// endpoint para el modulo de publicaciones
Route::post('/publicaciones', [PublicacionController::class, 'store']);
Route::get('/publicaciones', [PublicacionController::class, 'index']);


// endpoint para el Dashboard de Órdenes de Trabajo
//endpoint de Generales OT
Route::get('/bi/ordenes-trabajo/detalle-por-predio', [OrdenTrabajoController::class, 'getOtDetallePorPredio']);
Route::get('/bi/ordenes-trabajo/por-predio-estado', [OrdenTrabajoController::class, 'getOtPorPredioEstado']);
Route::get('/bi/ordenes-trabajo/global-a-tiempo', [OrdenTrabajoController::class, 'getOtGlobalATiempo']);
Route::get('/bi/ordenes-trabajo/a-tiempo-por-predio', [OrdenTrabajoController::class, 'getOtATiempoPorPredio']);
//endpoint de Responsables OT
Route::get('/bi/ordenes-trabajo/por-responsable', [OrdenTrabajoController::class, 'getOtPorResponsable']);
Route::get('/bi/ordenes-trabajo/por-responsable-estado', [OrdenTrabajoController::class, 'getOtPorResponsableEstado']);
Route::get('/bi/ordenes-trabajo/a-tiempo-por-responsable', [OrdenTrabajoController::class, 'getOtATiempoPorResponsable']);
//endpoint de Categoria OT
Route::get('/bi/ordenes-trabajo/por-tipo', [OrdenTrabajoController::class, 'getOtPorTipo']);
Route::get('/bi/ordenes-trabajo/a-tiempo-por-tipo', [OrdenTrabajoController::class, 'getOtATiempoPorTipo']);
//endpoint de para filtro de tipos de OT en Dashboard Ordenes de Trabajo
Route::get('/bi/ordenes-trabajo/listar-tipos', [OrdenTrabajoController::class, 'listarTiposOT']);
