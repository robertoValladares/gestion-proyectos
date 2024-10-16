<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/proyectos');
});


Route::resource('proyectos', ProyectoController::class);

Route::get('/tareas/proyecto/{proyectoId}', [TareaController::class, 'index'])->name('tareas.index');
Route::get('/tareas/{proyectoId}', [TareaController::class, 'show'])->name('tareas.show');
Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
Route::put('/tareas/{id}', [TareaController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{id}', [TareaController::class, 'destroy'])->name('tareas.destroy');
