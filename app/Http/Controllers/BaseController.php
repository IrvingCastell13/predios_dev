<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponser;

/**
 * @OA\Info(
 *     title="API de Documentos",
 *     version="1.0.0",
 *     description="API para gestionar documentos y predios.",
 *
 *     @OA\Contact(
 *         email="soporte@tudominio.com"
 *     )
 * )
 */
class BaseController extends Controller
{
    use ApiResponser;
}
