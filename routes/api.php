<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoriaController;
use App\Http\Controllers\API\TipousuarioController;
use App\Http\Controllers\API\ProductoController;
use App\Http\Controllers\API\GrupomenuController;
use App\Http\Controllers\API\OpcionmenuController;
use App\Http\Controllers\API\AccesoController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\UbigeoController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\ProcesoController;
use App\Http\Controllers\API\BienController;
use App\Http\Controllers\API\EtapaController;
use App\Http\Controllers\API\InscripcionController;
use App\Http\Controllers\API\BasesController;
use App\Http\Controllers\API\CodeCheckController;
use App\Http\Controllers\API\ContactoController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\EmpresaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/authenticate', function (Request $request) {
    return $request->user();
});*/


Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('authenticate',[LoginController::class,'authenticate']);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('tipousuario', TipousuarioController::class);
    Route::apiResource('producto', ProductoController::class);
    Route::post('actualizar-producto/{producto}',[ProductoController::class,'actualizar']);
    Route::apiResource('grupomenu', GrupomenuController::class);
    Route::apiResource('opcionmenu', OpcionmenuController::class);
    Route::apiResource('acceso', AccesoController::class);
    Route::post('tipousuario/acceso',[TipousuarioController::class,'acceso']);
    Route::post('logout', [LoginController::class, 'logout']);

    Route::apiResource('proceso', ProcesoController::class);
    Route::apiResource('bien', BienController::class);
    Route::post('bien/empezarSubasta', [BienController::class,'empezarSubasta'])->name('bien.empezarSubasta');
    Route::post('bien/empezarSubastaManual', [BienController::class,'empezarSubastaManual'])->name('bien.empezarSubastaManual');
    Route::post('bien/finalizarSubastaManual', [BienController::class,'finalizarSubastaManual'])->name('bien.finalizarSubastaManual');
    Route::post('bien/contadorSubastaManual', [BienController::class,'contadorSubastaManual'])->name('bien.contadorSubastaManual');
    Route::apiResource('etapa', EtapaController::class);
    Route::post('proceso/store2', [ProcesoController::class,'store2'])->name('proceso.store2');
    Route::post('proceso/update2', [ProcesoController::class,'update2'])->name('proceso.update2');
    Route::post('proceso/obtenerGanadorProceso', [ProcesoController::class,'obtenerGanadorProceso'])->name('proceso.obtenerGanadorProceso');
    Route::apiResource('inscripcion', InscripcionController::class);
    Route::post('actualizar-inscripcion/{id}',[InscripcionController::class,'actualizar']);
    Route::apiResource('bases', BasesController::class);

    Route::apiResource('bids', BidController::class);

    Route::apiResource('slider', SliderController::class);
    Route::post('actualizar-slider/{slider}',[SliderController::class,'actualizar']);

    Route::apiResource('empresa', EmpresaController::class);
    Route::apiResource('contacto', ContactoController::class);
});
Route::apiResource('usuario', UsuarioController::class);
Route::get('distrito',[UbigeoController::class,'distrito']);
Route::get('provincia',[UbigeoController::class,'provincia']);
Route::get('departamento',[UbigeoController::class,'departamento']);

Route::get('proceso',[ProcesoController::class,'list'])->name('proceso.list');
Route::get('procesosPorUsuario/{id}',[ProcesoController::class,'obtenerProcesosDeUsuario'])->name('proceso.usuario');
Route::get('procesosUltimos', [ProcesoController::class,'obtenerProcesosUltimos'])->name('proceso.obtenerProcesosUltimos');
Route::get('inscripcionesByUser',[InscripcionController::class,'inscripcionesByUser'])->name('inscripcion.byUser');

Route::post('usuario/store2', [UsuarioController::class, 'store2'])->name('usuario.store2');

 // Password reset routes
 Route::post('password/email',  ForgotPasswordController::class);
 Route::post('password/code/check', CodeCheckController::class);
 Route::post('password/reset', ResetPasswordController::class);

 Route::get('categoriaList',[CategoriaController::class,'list'])->name('categoria.list');
 Route::get('sliderList',[SliderController::class,'list'])->name('slider.list');
 Route::get('empresaList',[EmpresaController::class,'list'])->name('empresa.list');
 Route::get('procesosFiltroWeb',[ProcesoController::class,'obtenerFiltroWeb'])->name('proceso.obtenerFiltroWeb');
 Route::get('obtenerProceso',[ProcesoController::class,'obtenerProceso'])->name('proceso.obtenerProceso');
 Route::get('procesosListaPDF',[ProcesoController::class,'listaPDF'])->name('proceso.listaPDF');
 Route::get('procesosListaBienesPDF',[ProcesoController::class,'listaBienesPDF'])->name('proceso.listaBienesPDF');
 Route::get('procesosListaParticipantesPDF',[ProcesoController::class,'listaParticipantesPDF'])->name('proceso.listaParticipantesPDF');
 Route::get('procesosListaGanadoresPDF',[ProcesoController::class,'listaGanadorPDF'])->name('proceso.listaGanadorPDF');

 Route::post('contacto/guardar', [ContactoController::class,'store'])->name('contacto.guardar');