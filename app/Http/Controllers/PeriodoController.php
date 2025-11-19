<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodoController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('buscar');

        $materias = Materia::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%");
                });
            })
            ->withCount('periodos')
            ->orderBy('nombre')
            ->get();

        return view('periodos.index', [
            'materias' => $materias,
            'busqueda' => $busqueda,
        ]);
    }

    public function materia(Materia $materia): View
    {
        $periodos = Periodo::query()
            ->where('materia_id', $materia->id)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('periodos.materia', [
            'materia' => $materia,
            'periodos' => $periodos,
        ]);
    }

    public function store(Request $request, Materia $materia): RedirectResponse
    {
        $data = $this->validatePeriodo($request);

        Periodo::create([
            'materia_id' => $materia->id,
            'nombre' => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'orden' => $data['orden'] ?? null,
        ]);

        return redirect()
            ->route('periodos.materia', $materia)
            ->with('success', 'Periodo creado correctamente.');
    }

    public function edit(Periodo $periodo): View
    {
        $periodo->load('materia');

        return view('periodos.edit', [
            'periodo' => $periodo,
            'materia' => $periodo->materia,
        ]);
    }

    public function update(Request $request, Periodo $periodo): RedirectResponse
    {
        $data = $this->validatePeriodo($request, $periodo->id);

        $periodo->update([
            'nombre' => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'orden' => $data['orden'] ?? null,
        ]);

        return redirect()
            ->route('periodos.materia', $periodo->materia_id)
            ->with('success', 'Periodo actualizado correctamente.');
    }

    public function destroy(Periodo $periodo): RedirectResponse
    {
        $materiaId = $periodo->materia_id;
        $periodo->delete();

        return redirect()
            ->route('periodos.materia', $materiaId)
            ->with('success', 'Periodo eliminado correctamente.');
    }

    protected function validatePeriodo(Request $request, ?int $periodoId = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'orden' => ['required', 'integer', 'min:1'],
        ], [
            'nombre.required' => 'El nombre del periodo es obligatorio.',
            'nombre.string' => 'El nombre del periodo debe ser texto válido.',
            'nombre.max' => 'El nombre del periodo no debe superar 255 caracteres.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha fin es obligatoria.',
            'fecha_fin.date' => 'La fecha fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser posterior o igual a la fecha inicio.',
            'orden.required' => 'El orden es obligatorio.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden debe ser al menos 1.',
        ]);
    }
}
