<?php

use App\Http\Controllers\web\v1\Plan\PlanMantenimientoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard-reportes', function () {
    return view('dashboard-reportes');
});

Route::get('/dashboard-renovacion', function () { // <-- URL cambiada
    return view('dashboard-renovacion'); // <-- Vista cambiada
});

Route::get('/dashboard-mantenimiento', function () {
    return view('dashboard-mantenimiento');
});


Route::get('/dashboard-orden-trabajo', function () {
    return view('dashboard-orden-trabajo');
});



Route::get('planMantenimientoDetalle/canva/{IDPlan}', [PlanMantenimientoController::class, 'tablero'])->name('planMantenimientoDetalleTablero');

Route::get('planMantenimientoDetalle/gantt/{IDPlan}', [PlanMantenimientoController::class, 'gantt'])->name('planMantenimientoDetalleGantt');

Route::get('planDocumentosDetalle/gantt/{IDPlan}', [PlanMantenimientoController::class, 'ganttDocumentos'])->name('planDocumentosDetalle');
