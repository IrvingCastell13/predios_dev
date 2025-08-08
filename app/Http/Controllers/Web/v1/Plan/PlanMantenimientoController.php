<?php

namespace App\Http\Controllers\web\v1\Plan;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PlanMantenimientoController extends Controller
{


    public function tablero($id)
    {

        return Inertia::render('web/Plan/PlanGuiadoMantenimiento/Tablero/Tablero', compact('id'));
    }

 

    public function gantt($id)
    {
        return Inertia::render('web/Plan/PlanGuiadoMantenimiento/Gantt/Gantt', compact('id'));
    }

    public function ganttDocumentos($id)
    {
        return Inertia::render('web/Plan/PlanGuiadoMantenimiento/GanttDocumentos/Gantt', compact('id'));
    }
}
