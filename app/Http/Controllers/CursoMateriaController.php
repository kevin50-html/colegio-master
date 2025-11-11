<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\CursoMateria;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CursoMateriaController extends Controller
{
    public function index(Request $request): View
    {
        $cursoId = (int) $request->input('curso');

        $cursos = Curso::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $selectedCurso = null;
        $asignaciones = collect();

        if ($cursoId > 0) {
            $selectedCurso = Curso::query()
                ->with(['cursoMaterias' => function ($query) {
                    $query->with('materia')->orderBy('created_at');
                }])
                ->find($cursoId);

            if (! $selectedCurso) {
                session()->flash('error', 'El curso seleccionado no existe.');
            } else {
                $asignaciones = $selectedCurso->cursoMaterias;
            }
        }

        return view('curso_materias.index', [
            'cursos' => $cursos,
            'selectedCurso' => $selectedCurso,
            'asignaciones' => $asignaciones,
        ]);
    }

    public function create(Request $request): View
    {
        $cursoId = (int) $request->input('curso');

        $cursos = Curso::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $selectedCurso = $cursoId > 0
            ? Curso::query()->with('materias:id')->find($cursoId)
            : null;

        if ($cursoId > 0 && ! $selectedCurso) {
            session()->flash('error', 'El curso seleccionado no existe.');
        }

        $materiasDisponibles = collect();

        if ($selectedCurso) {
            $materiasDisponibles = Materia::query()
                ->when($selectedCurso, function ($query) use ($selectedCurso) {
                    $ids = $selectedCurso->materias->pluck('id');

                    if ($ids->isNotEmpty()) {
                        $query->whereNotIn('id', $ids);
                    }
                })
                ->orderBy('nombre')
                ->get(['id', 'nombre']);
        }

        return view('curso_materias.create', [
            'cursos' => $cursos,
            'selectedCurso' => $selectedCurso,
            'materiasDisponibles' => $materiasDisponibles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'curso_id' => ['required', 'integer', 'exists:cursos,id'],
            'materia_id' => [
                'required',
                'integer',
                'exists:materias,id',
                Rule::unique('curso_materia')->where(fn ($query) => $query->where('curso_id', $request->input('curso_id'))),
            ],
            'alias' => ['nullable', 'string', 'max:255'],
        ]);

        $cursoMateria = CursoMateria::create($data);

        return redirect()
            ->route('curso-materias.index', ['curso' => $cursoMateria->curso_id])
            ->with('success', 'Materia asignada al curso correctamente.');
    }

    public function show(CursoMateria $cursoMateria): View
    {
        $cursoMateria->load(['curso:id,nombre', 'materia:id,nombre,codigo']);

        return view('curso_materias.show', [
            'cursoMateria' => $cursoMateria,
        ]);
    }

    public function edit(CursoMateria $cursoMateria): View
    {
        $cursoMateria->load(['curso:id,nombre', 'materia:id,nombre,codigo']);

        return view('curso_materias.edit', [
            'cursoMateria' => $cursoMateria,
        ]);
    }

    public function update(Request $request, CursoMateria $cursoMateria): RedirectResponse
    {
        $data = $request->validate([
            'alias' => ['nullable', 'string', 'max:255'],
        ]);

        $cursoMateria->update($data);

        return redirect()
            ->route('curso-materias.show', $cursoMateria)
            ->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy(CursoMateria $cursoMateria): RedirectResponse
    {
        $cursoId = $cursoMateria->curso_id;

        $cursoMateria->delete();

        return redirect()
            ->route('curso-materias.index', ['curso' => $cursoId])
            ->with('success', 'Asignación eliminada correctamente.');
    }
}
