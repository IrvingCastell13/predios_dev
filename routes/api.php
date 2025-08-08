<?php

use App\Http\Controllers\Api\FiltroController;
use App\Http\Controllers\Api\OrdenTrabajoController;
use App\Http\Controllers\Api\PublicacionController;
use App\Http\Controllers\Api\ReporteDocumentosController;
use App\Http\Controllers\Api\ReporteEquiposController;
use App\Http\Controllers\api\v1\Plan\PlanesController;
use App\Models\Personas;
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
Route::get('/bi/listar-paises', [ReporteDocumentosController::class, 'listarPaises']);
Route::get('/bi/listar-estados', [ReporteDocumentosController::class, 'listarEstados']);
Route::get('/bi/listar-municipios', [ReporteDocumentosController::class, 'listarMunicipios']);

Route::get('/bi/listar-edificios', [ReporteDocumentosController::class, 'listarEdificios']);
Route::get('/bi/listar-niveles', [ReporteDocumentosController::class, 'listarNiveles']);
Route::get('/bi/listar-zonas', [ReporteDocumentosController::class, 'listarZonas']);
Route::get('/bi/listar-planes-renovacion', [ReporteDocumentosController::class, 'listarPlanesRenovacion']);


// endpoint para las graficas de Dashboard pestaña de Cumplimiento
Route::get('/bi/matriz-subcategoria', [ReporteDocumentosController::class, 'matrizPorSubcategoria']);
Route::get('/bi/matriz-subcategoria-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorSubcategoria']);
Route::get('/bi/calificaciones-ponderadas', [ReporteDocumentosController::class, 'calificacionesPonderadasPorPredio']);
Route::get('/bi/matriz-grupo', [ReporteDocumentosController::class, 'matrizPorGrupo']);
Route::get('/bi/matriz-grupo-archivos', [ReporteDocumentosController::class, 'matrizArchivosPorGrupo']);


Route::get('/bi/matriz-subcategoria-zona', [ReporteDocumentosController::class, 'matrizPorSubcategoriaZona']);
Route::get('/bi/matriz-subcategoria-archivos-zona', [ReporteDocumentosController::class, 'matrizArchivosPorSubcategoriaZona']);
Route::get('/bi/matriz-grupo-zona', [ReporteDocumentosController::class, 'matrizPorGrupoZona']);
Route::get('/bi/matriz-grupo-archivos-zona', [ReporteDocumentosController::class, 'matrizArchivosPorGrupoZona']);
Route::get('/bi/calificaciones-ponderadas-zona', [ReporteDocumentosController::class, 'calificacionesPonderadasPorZona']);


// endpoint las 3 graficas de Dashboard Renovacion
Route::get('/bi/resumen-estado-acciones-renovacion', [ReporteDocumentosController::class, 'resumenEstadoAccionesRenovacion']);
Route::get('/bi/detalle-estado-acciones-renovacion', [ReporteDocumentosController::class, 'detalleEstadoAccionesRenovacion']);
Route::get('/bi/resumen-estado-ots-renovacion', [ReporteDocumentosController::class, 'resumenEstadoOTsRenovacion']);
Route::get('/bi/detalle-estado-ots-renovacion', [ReporteDocumentosController::class, 'detalleEstadoOTsRenovacion']);
Route::get('/bi/resumen-estado-documentos', [ReporteDocumentosController::class, 'resumenEstadoDocumentos']);
Route::get('/bi/detalle-estado-documentos', [ReporteDocumentosController::class, 'detalleEstadoDocumentos']);

// endpoint para las 3 graficas de Dashboard Mantenimiento
Route::get('/bi/estado-mantenimiento-equipos', [ReporteEquiposController::class, 'estadoAccionesPorMantenimiento']);
Route::get('/bi/estado-acciones', [ReporteEquiposController::class, 'estadoAcciones']);

Route::get('/bi/resumen-ot-estado', [ReporteEquiposController::class, 'estadoAccionesConOT']);
Route::get('/bi/detalle-estados-ot', [ReporteEquiposController::class, 'detalleEstadoOTs']);

Route::get('/bi/resumen-equipos-estado', [ReporteEquiposController::class, 'graficaEstadoEquiposDePlan']);
Route::get('/bi/detalle-estados-equipos', [ReporteEquiposController::class, 'tablaEstadoEquiposDePlan']);

Route::get('/reportes/mantenimiento/exportar', [ReporteEquiposController::class, 'exportarReporteMantenimiento']);

// Enpoints para los filtros de Dashboard Mantenimiento (sistema y subsitema)
Route::get('/bi/listar-sistemas', [ReporteEquiposController::class, 'listarSistemas']);
Route::get('/bi/listar-subsistemas', [ReporteEquiposController::class, 'listarSubsistemas']);

// Enpoints para los filtros de Dashboard Mantenimiento (Edificio, Nivel y Zona)
Route::get('/bi/listar-predios-mantenimento', [ReporteEquiposController::class, 'listarPredios']);
Route::get('/bi/listar-edificios', [ReporteEquiposController::class, 'listarEdificios']);
Route::get('/bi/listar-niveles', [ReporteEquiposController::class, 'listarNiveles']);
Route::get('/bi/listar-zonas', [ReporteEquiposController::class, 'listarZonas']);
Route::get('/bi/listar-tipos-equipo-con-ubicacion', [ReporteEquiposController::class, 'listarTiposEquipoConUbicacion']);
Route::get('/bi/listar-planes-mantenimiento', [ReporteEquiposController::class, 'listarPlanes']);

// Enpoints para los filtros de Dashboard Renovacion']);

// Enpoints para los filtros de Dashboard Renovacion (categoria y subcategoria)
Route::get('/bi/listar-grupos-doc', [ReporteDocumentosController::class, 'listarGruposDoc']);
Route::get('/bi/listar-categorias-doc', [ReporteDocumentosController::class, 'listarCategoriasDoc']);
Route::get('/reportes/renovacion/exportar', [ReporteDocumentosController::class, 'exportarExcelRenovacion']);


// endpoint para la pestaña de Vigencia de Dashboard Reportes
Route::get('/bi/documentos-con-estado', [ReporteDocumentosController::class, 'documentosConEstadoPorCategoria']);
Route::get('/bi/documentos-por-subcategoria', [ReporteDocumentosController::class, 'documentosPorSubcategoria']);
Route::get('/bi/porcentaje-vigencia-por-predio', [ReporteDocumentosController::class, 'porcentajeVigenciaPorPredio']);
Route::get('/bi/tabla-detallada-vigencia', [ReporteDocumentosController::class, 'tablaDetalladaVigencia']);

Route::get('/bi/documentos-con-estado-zona', [ReporteDocumentosController::class, 'documentosConEstadoPorCategoriaZona']);
Route::get('/bi/documentos-por-subcategoria-zona', [ReporteDocumentosController::class, 'documentosPorSubcategoriaZona']);
Route::get('/bi/porcentaje-vigencia-por-zona', [ReporteDocumentosController::class, 'porcentajeVigenciaPorZona']);

Route::get('/bi/tabla-detallada-vigencia-zona', [ReporteDocumentosController::class, 'tablaDetalladaVigenciaPorZona']);

Route::get('/reportes/documentos/exportar', [ReporteDocumentosController::class, 'exportarReporteDocumentos']);

// endpoint para el modulo de publicaciones
Route::post('/publicaciones', [PublicacionController::class, 'store']);
Route::get('/publicaciones', [PublicacionController::class, 'index']);


// endpoint para el Dashboard de Órdenes de Trabajo
Route::get('/bi/listar-edificio-ot', [OrdenTrabajoController::class, 'listarEdificios']);
Route::get('/bi/listar-nivel-ot', [OrdenTrabajoController::class, 'listarNiveles']);
Route::get('/bi/listar-zona-ot', [OrdenTrabajoController::class, 'listarZonas']);
Route::get('/bi/listar-responsables-ot', [OrdenTrabajoController::class, 'listarResponsables']);

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

Route::get('/bi/ordenes-trabajo/exportar-excel', [OrdenTrabajoController::class, 'exportarExcel']);

Route::get('obtnerTableroPlanEquipos/{IDPlan}', [PlanesController::class, 'obtnerTableroPlanEquipos'])->name('obtnerTableroPlanEquipos');
// Route::get('obtnerPlanesShow/', [PlanesController::class, 'show'])->name('planPlanes.show');

Route::get('obtnerGanttPlanEquipos/{IDPlan}', [PlanesController::class, 'obtnerGanttPlanEquipos'])->name('obtnerGanttPlanEquipos');
Route::get('/planes/{id}', [PlanesController::class, 'show'])->name('planPlanes.show');

Route::get('obtenerGanttPlanDocumentos/{IDPlan}', [PlanesController::class, 'obtenerGanttPlanDocumentos'])->name('obtenerGanttPlanDocumentos');

Route::prefix('filtros')->group(function () {
    Route::get('/predios', [FiltroController::class, 'getPredios']);
    Route::get('/edificios', [FiltroController::class, 'getEdificios']);
    Route::get('/categorias', [FiltroController::class, 'getDocumentosCategorias']);
    Route::get('/subcategorias', [FiltroController::class, 'getDocumentosSubcategorias']);
    Route::get('/niveles', [FiltroController::class, 'getNiveles']);
    Route::get('/zonas', [FiltroController::class, 'getZonas']);
    Route::get('/responsables', [FiltroController::class, 'getResponsables']);
    Route::get('/subsistemas', [FiltroController::class, 'getSubsistemas']);
    Route::get('/sistemas-con-subsistemas', [FiltroController::class, 'getSistemasConSubsistemas']);
});


Route::get('/generar-token-prueba', function () {
    // --- PEGA AQUÍ EL ID DEL USUARIO DE TU BASE DE DATOS ---
    $userId = '007fbcef-c429-4aff-b1ea-3f087a22ceec';

    $user = Personas::find($userId);

    if (!$user) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    // Elimina los tokens antiguos para mantenerlo limpio (opcional)
    $user->tokens()->delete();

    // Crea un nuevo token
    $token = $user->createToken('TokenDePruebaParaPostman')->accessToken;

    // Devuelve el token
    return response()->json([
        'message' => 'Token generado con éxito. Cópialo y pégalo en Postman.',
        'token' => $token
    ]);
});
