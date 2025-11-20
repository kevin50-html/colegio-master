<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q'));

        $materias = Materia::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('nombre', 'like', "%{$search}%")
                        ->orWhere('codigo', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('materias.index', [
            'materias' => $materias,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('materias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre',
            'codigo' => 'required|string|max:50|unique:materias,codigo',
            'intensidad_horaria' => 'required|integer|min:1|max:40',
            'descripcion' => 'nullable|string',
        ]);

        Materia::create($data);

        return redirect()
            ->route('materias.index')
            ->with('success', 'Materia creada correctamente.');
    }

    public function show(Materia $materia): View
    {
        return view('materias.show', [
            'materia' => $materia,
        ]);
    }

    public function edit(Materia $materia): View
    {
        return view('materias.edit', [
            'materia' => $materia,
        ]);
    }

    public function update(Request $request, Materia $materia): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre,' . $materia->id,
            'codigo' => 'required|string|max:50|unique:materias,codigo,' . $materia->id,
            'intensidad_horaria' => 'required|integer|min:1|max:40',
            'descripcion' => 'nullable|string',
        ]);

        $materia->update($data);

        return redirect()
            ->route('materias.show', $materia)
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia): RedirectResponse
    {
        if (method_exists($materia, 'cursoMaterias') && $materia->cursoMaterias()->exists()) {
            return redirect()
                ->route('materias.show', $materia)
                ->with('error', 'No se puede eliminar la materia porque está asignada a cursos.');
        }

        $materia->delete();

        return redirect()
            ->route('materias.index')
            ->with('success', 'Materia eliminada correctamente.');
    }
}
