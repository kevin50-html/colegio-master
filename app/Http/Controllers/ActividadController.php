<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Materia;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActividadController extends Controller
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
            ->withCount([
                'periodos as actividades_count' => function ($query) {
                    $query->join('actividades', 'periodos.id', '=', 'actividades.periodo_id');
                },
            ])
            ->orderBy('nombre')
            ->get();

        return view('actividades.index', [
            'materias' => $materias,
            'busqueda' => $busqueda,
        ]);
    }

    public function materia(Materia $materia): View
    {
        $periodos = Periodo::query()
            ->where('materia_id', $materia->id)
            ->with(['materia', 'horarios'])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $actividades = Actividad::query()
            ->whereHas('periodo', function ($query) use ($materia) {
                $query->where('materia_id', $materia->id);
            })
            ->with('periodo')
            ->orderBy('fecha_entrega')
            ->orderBy('created_at')
            ->get();

        return view('actividades.materia', [
            'materia' => $materia,
            'periodos' => $periodos,
            'actividades' => $actividades,
        ]);
    }

    public function storePeriodo(Request $request, Materia $materia): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'orden' => 'nullable|integer|min:1',
        ]);

        Periodo::create([
            'materia_id' => $materia->id,
            'nombre' => $data['nombre'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'orden' => $data['orden'] ?? null,
        ]);

        return redirect()
            ->route('actividades.materia', $materia)
            ->with('success', 'Periodo registrado para la materia.');
    }

    public function store(Request $request, Materia $materia): RedirectResponse
    {
        $data = $request->validate([
            'periodo_id' => 'required|integer|exists:periodos,id',
            'horario_id' => 'required|integer|exists:horarios,id',
            'titulo' => 'required|string|max:255',
            'fecha_entrega' => 'nullable|date',
            'porcentaje' => 'nullable|integer|min:1|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $periodo = Periodo::query()
            ->where('id', $data['periodo_id'])
            ->where('materia_id', $materia->id)
            ->firstOrFail();

        $horario = $periodo->horarios()
            ->where('id', $data['horario_id'])
            ->firstOrFail();

        Actividad::create([
            'periodo_id' => $periodo->id,
            'horario_id' => $horario->id,
            'titulo' => $data['titulo'],
            'fecha_entrega' => $data['fecha_entrega'] ?? null,
            'porcentaje' => $data['porcentaje'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
        ]);

        return redirect()
            ->route('actividades.materia', $materia)
            ->with('success', 'Actividad registrada para la materia.');
    }

    public function destroy(Actividad $actividad): RedirectResponse
    {
        $materia = Materia::query()
            ->whereHas('periodos.actividades', function ($query) use ($actividad) {
                $query->where('actividades.id', $actividad->id);
            })
            ->first();

        $actividad->delete();

        if ($materia) {
            return redirect()
                ->route('actividades.materia', $materia)
                ->with('success', 'Actividad eliminada correctamente.');
        }

        return redirect()
            ->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}
