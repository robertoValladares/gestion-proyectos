<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use App\Http\Requests\ProyectoRequest;

class ProyectoController extends Controller
{
   // Listar proyectos
   public function index()
   {
       $proyectos = Proyecto::all();
       return view('proyectos.index', compact('proyectos'));
   }

   // Crear un nuevo proyecto
   public function store(ProyectoRequest  $request)
   {
       $proyecto = Proyecto::create($request->all());

       return response()->json($proyecto, 201);
   }

   // Actualizar un proyecto existente
   public function update(ProyectoRequest  $request, $id)
   {
       $proyecto = Proyecto::findOrFail($id);

       $proyecto->update($request->all());

       return response()->json($proyecto, 200);
   }

   // Eliminar un proyecto
   public function destroy($id)
   {
       $proyecto = Proyecto::findOrFail($id);

       // Elimina las tareas asociadas al proyecto
       $proyecto->tareas()->delete();

       $proyecto->delete();

       return response()->json(null, 204);
   }
}
