<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Horario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function index(Horario $horario): RedirectResponse
    {
        return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario]);
    }

    public function create(Horario $horario): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades'])) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'No tienes permisos para crear actividades.');
        }

        return view('academico.actividades.create', [
            'horario' => $horario,
        ]);
    }

    public function store(Request $request, Horario $horario): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades'])) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'No tienes permisos para crear actividades.');
        }

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'fecha_entrega' => ['nullable', 'date'],
            'porcentaje' => ['nullable', 'integer', 'min:0', 'max:100'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $data['horario_id'] = $horario->id;

        Actividad::create($data);

        return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('success', 'Actividad creada correctamente.');
    }

    public function show(Horario $horario, Actividad $actividad): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades', 'notas'])) {
            return $redirect;
        }

        if ($actividad->horario_id !== $horario->id) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'La actividad no pertenece al horario indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'crear_actividades',
            'gestionar_horarios',
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'registrar_notas',
            'ver_notas',
        ])) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'No tienes permisos para ver esta actividad.');
        }

        $notas = $actividad->notas()
            ->with(['estudiante'])
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('academico.actividades.show', [
            'horario' => $horario,
            'actividad' => $actividad,
            'notas' => $notas,
        ]);
    }

    public function edit(Horario $horario, Actividad $actividad): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades'])) {
            return $redirect;
        }

        if ($actividad->horario_id !== $horario->id) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'La actividad no pertenece al horario indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades'])) {
            return redirect()->route('academico.horarios.actividades.show', [$horario, $actividad])->with('error', 'No tienes permisos para editar esta actividad.');
        }

        return view('academico.actividades.edit', [
            'horario' => $horario,
            'actividad' => $actividad,
        ]);
    }

    public function update(Request $request, Horario $horario, Actividad $actividad): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades'])) {
            return $redirect;
        }

        if ($actividad->horario_id !== $horario->id) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'La actividad no pertenece al horario indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades'])) {
            return redirect()->route('academico.horarios.actividades.show', [$horario, $actividad])->with('error', 'No tienes permisos para editar esta actividad.');
        }

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'fecha_entrega' => ['nullable', 'date'],
            'porcentaje' => ['nullable', 'integer', 'min:0', 'max:100'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $actividad->update($data);

        return redirect()->route('academico.horarios.actividades.show', [$horario, $actividad])->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Horario $horario, Actividad $actividad): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades'])) {
            return $redirect;
        }

        if ($actividad->horario_id !== $horario->id) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'La actividad no pertenece al horario indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['crear_actividades'])) {
            return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('error', 'No tienes permisos para eliminar actividades.');
        }

        if ($actividad->notas()->exists()) {
            return redirect()->route('academico.horarios.actividades.show', [$horario, $actividad])->with('error', 'No se puede eliminar una actividad con notas registradas.');
        }

        $actividad->delete();

        return redirect()->route('academico.periodos.horarios.show', [$horario->periodo, $horario])->with('success', 'Actividad eliminada correctamente.');
    }

    private function asegurarTablas(array $tablas): ?RedirectResponse
    {
        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                return redirect()->route('dashboard')->with(
                    'error',
                    'Debes ejecutar las migraciones más recientes (php artisan migrate) para habilitar la gestión académica.'
                );
            }
        }

        return null;
    }
}
