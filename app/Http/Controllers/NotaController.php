<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Estudiante;
use App\Models\Nota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotaController extends Controller
{
    public function index(Actividad $actividad): RedirectResponse
    {
        return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad]);
    }

    public function create(Actividad $actividad): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['notas', 'estudiantes'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['registrar_notas', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'No tienes permisos para registrar notas.');
        }

        $cursoId = $actividad->periodo->cursoMateria->curso_id;
        $estudiantes = Estudiante::where('curso_id', $cursoId)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return view('academico.notas.create', [
            'actividad' => $actividad,
            'estudiantes' => $estudiantes,
        ]);
    }

    public function store(Request $request, Actividad $actividad): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['notas', 'estudiantes'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['registrar_notas', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'No tienes permisos para registrar notas.');
        }

        $cursoId = $actividad->periodo->cursoMateria->curso_id;

        $data = $request->validate([
            'estudiante_id' => [
                'required',
                'exists:estudiantes,id',
                Rule::unique('notas', 'estudiante_id')->where('actividad_id', $actividad->id),
            ],
            'valor' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $estudianteValido = Estudiante::where('id', $data['estudiante_id'] ?? null)
            ->where('curso_id', $cursoId)
            ->exists();

        if (!$estudianteValido) {
            return back()->withInput()->with('error', 'El estudiante seleccionado no pertenece al curso de la actividad.');
        }

        $data['actividad_id'] = $actividad->id;

        Nota::create($data);

        return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('success', 'Nota registrada correctamente.');
    }

    public function edit(Actividad $actividad, Nota $nota): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['notas'])) {
            return $redirect;
        }

        if ($nota->actividad_id !== $actividad->id) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'La nota no pertenece a la actividad indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['registrar_notas', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'No tienes permisos para editar notas.');
        }

        return view('academico.notas.edit', [
            'actividad' => $actividad,
            'nota' => $nota,
        ]);
    }

    public function update(Request $request, Actividad $actividad, Nota $nota): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['notas'])) {
            return $redirect;
        }

        if ($nota->actividad_id !== $actividad->id) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'La nota no pertenece a la actividad indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['registrar_notas', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'No tienes permisos para editar notas.');
        }

        $data = $request->validate([
            'valor' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $nota->update($data);

        return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('success', 'Nota actualizada correctamente.');
    }

    public function destroy(Actividad $actividad, Nota $nota): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['notas'])) {
            return $redirect;
        }

        if ($nota->actividad_id !== $actividad->id) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'La nota no pertenece a la actividad indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['registrar_notas', 'acceso_total'])) {
            return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('error', 'No tienes permisos para eliminar notas.');
        }

        $nota->delete();

        return redirect()->route('academico.periodos.actividades.show', [$actividad->periodo, $actividad])->with('success', 'Nota eliminada correctamente.');
    }

    private function asegurarTablas(array $tablas): ?RedirectResponse
    {
        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                return redirect()->route('dashboard')->with('error', 'Debes ejecutar las migraciones más recientes (php artisan migrate) para habilitar la gestión académica.');
            }
        }

        return null;
    }
}
