<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function index(Periodo $periodo): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos', 'actividades'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.index', $periodo->cursoMateria)->with('error', 'No tienes permisos para consultar las actividades.');
        }

        $actividades = $periodo->actividades()->withCount('notas')->orderBy('fecha_entrega')->orderBy('titulo')->get();

        return view('academico.actividades.index', [
            'periodo' => $periodo->load(['cursoMateria.curso', 'cursoMateria.materia']),
            'actividades' => $actividades,
        ]);
    }

    public function create(Periodo $periodo): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos', 'actividades'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.index', $periodo)->with('error', 'No tienes permisos para crear actividades.');
        }

        return view('academico.actividades.create', [
            'periodo' => $periodo->load(['cursoMateria.curso', 'cursoMateria.materia']),
        ]);
    }

    public function store(Request $request, Periodo $periodo): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos', 'actividades'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.index', $periodo)->with('error', 'No tienes permisos para crear actividades.');
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_entrega' => 'nullable|date',
            'porcentaje' => 'nullable|integer|min:1|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $periodo->actividades()->create($data);

        return redirect()->route('academico.periodos.actividades.index', $periodo)->with('success', 'Actividad creada correctamente.');
    }

    public function show(Periodo $periodo, Actividad $actividad): View
    {
        $this->validarPertenencia($periodo, $actividad);

        return view('academico.actividades.show', [
            'periodo' => $periodo->load(['cursoMateria.curso', 'cursoMateria.materia']),
            'actividad' => $actividad->load(['notas.estudiante']),
        ]);
    }

    public function edit(Periodo $periodo, Actividad $actividad): View|RedirectResponse
    {
        $this->validarPertenencia($periodo, $actividad);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$periodo, $actividad])->with('error', 'No tienes permisos para editar esta actividad.');
        }

        return view('academico.actividades.edit', [
            'periodo' => $periodo->load(['cursoMateria.curso', 'cursoMateria.materia']),
            'actividad' => $actividad,
        ]);
    }

    public function update(Request $request, Periodo $periodo, Actividad $actividad): RedirectResponse
    {
        $this->validarPertenencia($periodo, $actividad);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$periodo, $actividad])->with('error', 'No tienes permisos para actualizar esta actividad.');
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_entrega' => 'nullable|date',
            'porcentaje' => 'nullable|integer|min:1|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $actividad->update($data);

        return redirect()->route('academico.periodos.actividades.show', [$periodo, $actividad])->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Periodo $periodo, Actividad $actividad): RedirectResponse
    {
        $this->validarPertenencia($periodo, $actividad);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.index', $periodo)->with('error', 'No tienes permisos para eliminar actividades.');
        }

        if ($actividad->notas()->exists()) {
            return redirect()->route('academico.periodos.actividades.show', [$periodo, $actividad])->with('error', 'No se puede eliminar la actividad porque tiene notas registradas.');
        }

        $actividad->delete();

        return redirect()->route('academico.periodos.actividades.index', $periodo)->with('success', 'Actividad eliminada correctamente.');
    }

    private function validarPertenencia(Periodo $periodo, Actividad $actividad): void
    {
        if ($actividad->periodo_id !== $periodo->id) {
            abort(404);
        }
    }

    private function asegurarTablas(array $tablas): ?RedirectResponse
    {
        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                return redirect()->route('dashboard')->with('error', 'Debes ejecutar las migraciones académicas (php artisan migrate).');
            }
        }

        return null;
    }
}
