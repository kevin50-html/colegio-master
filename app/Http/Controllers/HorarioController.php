<?php

namespace App\Http\Controllers;

use App\Models\CursoMateria;
use App\Models\Horario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(CursoMateria $cursoMateria): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.materias.index', $cursoMateria->curso)->with('error', 'No tienes permisos para consultar los horarios.');
        }

        $horarios = $cursoMateria->horarios()->with('periodo')->orderBy('dia')->orderBy('hora_inicio')->get();

        return view('academico.horarios.index', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'horarios' => $horarios,
            'periodos' => $cursoMateria->periodos()->orderBy('orden')->get(),
        ]);
    }

    public function create(CursoMateria $cursoMateria): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('error', 'No tienes permisos para crear horarios.');
        }

        return view('academico.horarios.create', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'periodos' => $cursoMateria->periodos()->orderBy('orden')->get(),
        ]);
    }

    public function store(Request $request, CursoMateria $cursoMateria): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('error', 'No tienes permisos para crear horarios.');
        }

        $data = $request->validate([
            'periodo_id' => 'nullable|exists:periodos,id',
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula' => 'nullable|string|max:100',
        ]);

        if (!empty($data['periodo_id']) && !$cursoMateria->periodos()->where('id', $data['periodo_id'])->exists()) {
            return redirect()->back()->withInput()->with('error', 'El periodo seleccionado no pertenece a esta relación curso-materia.');
        }

        $cursoMateria->horarios()->create($data);

        return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('success', 'Horario creado correctamente.');
    }

    public function edit(CursoMateria $cursoMateria, Horario $horario): View|RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $horario);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('error', 'No tienes permisos para editar este horario.');
        }

        return view('academico.horarios.edit', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'horario' => $horario,
            'periodos' => $cursoMateria->periodos()->orderBy('orden')->get(),
        ]);
    }

    public function update(Request $request, CursoMateria $cursoMateria, Horario $horario): RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $horario);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('error', 'No tienes permisos para actualizar este horario.');
        }

        $data = $request->validate([
            'periodo_id' => 'nullable|exists:periodos,id',
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula' => 'nullable|string|max:100',
        ]);

        if (!empty($data['periodo_id']) && !$cursoMateria->periodos()->where('id', $data['periodo_id'])->exists()) {
            return redirect()->back()->withInput()->with('error', 'El periodo seleccionado no pertenece a esta relación curso-materia.');
        }

        $horario->update($data);

        return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(CursoMateria $cursoMateria, Horario $horario): RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $horario);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('error', 'No tienes permisos para eliminar horarios.');
        }

        $horario->delete();

        return redirect()->route('academico.curso-materias.horarios.index', $cursoMateria)->with('success', 'Horario eliminado correctamente.');
    }

    private function validarPertenencia(CursoMateria $cursoMateria, Horario $horario): void
    {
        if ($horario->curso_materia_id !== $cursoMateria->id) {
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
