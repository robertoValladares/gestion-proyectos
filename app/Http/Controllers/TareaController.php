<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index($proyectoId)
    {
        $tareas = Tarea::where('proyecto_id', $proyectoId)->get();
        return view('tareas.index', compact('tareas', 'proyectoId'));
    }

    public function store(Request $request)
    {
        $proyecto = Proyecto::find($request->proyecto_id);

        $this->validate($request, [
            'fecha_vencimiento' => 'required|date|after:' . $proyecto->fecha_inicio,
        ], [
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de inicio del proyecto.',
        ]);


        Tarea::create($request->all());
        return response()->json(['message' => 'Tarea creada con éxito.']);
    }

    public function show($id)
    {
        $tarea = Tarea::findOrFail($id);
        return response()->json($tarea);
    }

    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::find($request->proyecto_id);

        $this->validate($request, [
            'fecha_vencimiento' => 'required|date|after:' . $proyecto->fecha_inicio,
        ], [
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
            'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de inicio del proyecto.',
        ]);

        $tarea = Tarea::findOrFail($id);
        $tarea->update($request->all());
        return response()->json(['message' => 'Tarea actualizada con éxito.']);
    }

    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return response()->json(['message' => 'Tarea eliminada con éxito.']);
    }
}
