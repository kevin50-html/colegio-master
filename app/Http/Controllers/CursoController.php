<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $cursos = Curso::query()
            ->withCount(['docentes', 'materias', 'estudiantes'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('cursos.index', [
            'cursos' => $cursos,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('cursos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:cursos,nombre',
        ]);

        Curso::create($data);

        return redirect()
            ->route('cursos.index')
            ->with('success', 'Curso creado correctamente.');
    }

    public function show(Curso $curso): View
    {
        $curso->loadCount(['materias', 'docentes', 'estudiantes', 'matriculas'])
            ->load([
                'materias' => fn ($query) => $query->orderBy('materias.nombre'),
                'docentes' => fn ($query) => $query->orderBy('apellidos')->orderBy('nombres'),
                'estudiantes' => fn ($query) => $query->orderBy('apellidos')->orderBy('nombres'),
            ]);

        return view('cursos.show', [
            'curso' => $curso,
        ]);
    }

    public function edit(Curso $curso): View
    {
        return view('cursos.edit', [
            'curso' => $curso,
        ]);
    }

    public function update(Request $request, Curso $curso): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:cursos,nombre,' . $curso->id,
        ]);

        $curso->update($data);

        return redirect()
            ->route('cursos.show', $curso)
            ->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Curso $curso): RedirectResponse
    {
        if ($curso->estudiantes()->exists() || $curso->docentes()->exists() || $curso->materias()->exists() || $curso->matriculas()->exists()) {
            return redirect()
                ->route('cursos.show', $curso)
                ->with('error', 'No se puede eliminar el curso porque tiene información asociada.');
        }

        $curso->delete();

        return redirect()
            ->route('cursos.index')
            ->with('success', 'Curso eliminado correctamente.');
    }
}
