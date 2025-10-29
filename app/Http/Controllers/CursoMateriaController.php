<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\CursoMateria;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CursoMateriaController extends Controller
{
    public function index(Curso $curso, Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'curso_materia'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para gestionar las materias del curso.');
        }

        $busqueda = trim((string) $request->input('q'));

        $asignadas = $curso->cursoMaterias()->with(['materia', 'periodos'])->orderBy(Materia::select('nombre')->whereColumn('materias.id', 'curso_materia.materia_id'))->get();

        $disponibles = Materia::when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->whereDoesntHave('cursoMaterias', function ($q) use ($curso) {
                $q->where('curso_id', $curso->id);
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('academico.cursos.materias.index', [
            'curso' => $curso,
            'asignadas' => $asignadas,
            'disponibles' => $disponibles,
            'busqueda' => $busqueda,
        ]);
    }

    public function store(Curso $curso, Request $request): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'curso_materia'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para asignar materias.');
        }

        $data = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'alias' => 'nullable|string|max:255',
        ]);

        if ($curso->cursoMaterias()->where('materia_id', $data['materia_id'])->exists()) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'La materia ya está asociada al curso.');
        }

        $curso->cursoMaterias()->create($data);

        return redirect()->route('academico.cursos.materias.index', $curso)->with('success', 'Materia asignada al curso.');
    }

    public function update(Curso $curso, CursoMateria $cursoMateria, Request $request): RedirectResponse
    {
        if ($cursoMateria->curso_id !== $curso->id) {
            abort(404);
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para actualizar la materia.');
        }

        $data = $request->validate([
            'alias' => 'nullable|string|max:255',
        ]);

        $cursoMateria->update($data);

        return redirect()->route('academico.cursos.materias.index', $curso)->with('success', 'Información actualizada.');
    }

    public function destroy(Curso $curso, CursoMateria $cursoMateria): RedirectResponse
    {
        if ($cursoMateria->curso_id !== $curso->id) {
            abort(404);
        }

        if ($cursoMateria->periodos()->exists()) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No puedes quitar la materia porque tiene periodos u horarios configurados.');
        }

        $cursoMateria->delete();

        return redirect()->route('academico.cursos.materias.index', $curso)->with('success', 'Materia retirada del curso.');
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
