<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a los cursos.');
        }

        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::withCount('cursoMaterias as materias_count')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('academico.cursos.index', [
            'cursos' => $cursos,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para crear cursos.');
        }

        return view('academico.cursos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para crear cursos.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:cursos,nombre',
        ]);

        Curso::create($data);

        return redirect()->route('academico.cursos.index')->with('success', 'Curso creado correctamente.');
    }

    public function show(Curso $curso): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'curso_materia', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para ver este curso.');
        }

        $curso->load(['cursoMaterias.materia' => function ($query) {
            $query->orderBy('nombre');
        }]);

        return view('academico.cursos.show', [
            'curso' => $curso,
        ]);
    }

    public function edit(Curso $curso): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.show', $curso)->with('error', 'No tienes permisos para editar este curso.');
        }

        return view('academico.cursos.edit', [
            'curso' => $curso,
        ]);
    }

    public function update(Request $request, Curso $curso): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.show', $curso)->with('error', 'No tienes permisos para actualizar este curso.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:cursos,nombre,' . $curso->id,
        ]);

        $curso->update($data);

        return redirect()->route('academico.cursos.show', $curso)->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para eliminar cursos.');
        }

        if ($curso->cursoMaterias()->exists()) {
            return redirect()->route('academico.cursos.show', $curso)->with('error', 'No se puede eliminar un curso con materias asignadas.');
        }

        $curso->delete();

        return redirect()->route('academico.cursos.index')->with('success', 'Curso eliminado correctamente.');
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
