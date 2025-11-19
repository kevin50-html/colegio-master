<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Horario;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = trim((string) $request->input('buscar', ''));

        $cursos = Curso::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->withCount([
                'cursoMaterias as horarios_count' => function ($query) {
                    $query->join('horarios', 'curso_materia.id', '=', 'horarios.curso_materia_id');
                },
                'materias',
                'estudiantes',
            ])
            ->orderBy('nombre')
            ->paginate(9)
            ->withQueryString();

        return view('horarios.index', [
            'cursos' => $cursos,
            'busqueda' => $busqueda,
        ]);
    }

    public function curso(Curso $curso): View
    {
        $curso->loadCount('estudiantes');

        $cursoMaterias = $curso->cursoMaterias()
            ->with([
                'materia:id,nombre,codigo',
                'horarios' => function ($query) {
                    $query->with('periodo')
                        ->orderBy(Horario::diaColumn())
                        ->orderBy('hora_inicio');
                },
            ])
            ->orderBy('created_at')
            ->get();

        $materiaIds = $cursoMaterias->pluck('materia_id')->unique()->filter();

        $periodosPorMateria = Periodo::query()
            ->whereIn('materia_id', $materiaIds)
            ->orderBy('materia_id')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->groupBy('materia_id');

        $horarios = $cursoMaterias->flatMap(function ($cursoMateria) {
            return $cursoMateria->horarios->map(function ($horario) use ($cursoMateria) {
                return [
                    'horario' => $horario,
                    'materia' => $cursoMateria->materia,
                    'alias' => $cursoMateria->alias,
                ];
            });
        });

        $horariosPorDia = $horarios->groupBy(function ($registro) {
            return $registro['horario']->dia ?? 'Sin día';
        })->map(function (Collection $registros) {
            return $registros->sortBy(function ($registro) {
                return $registro['horario']->hora_inicio?->format('H:i') ?? '';
            })->values();
        });

        return view('horarios.curso', [
            'curso' => $curso,
            'cursoMaterias' => $cursoMaterias,
            'periodosPorMateria' => $periodosPorMateria,
            'horariosPorDia' => $horariosPorDia,
            'diasSemana' => $this->diasSemana(),
            'totalHorarios' => $horarios->count(),
        ]);
    }

    public function consulta(Curso $curso): View
    {
        $curso->load([
            'docentes' => fn ($query) => $query->orderBy('apellidos')->orderBy('nombres'),
            'estudiantes',
        ])->loadCount('estudiantes');

        $cursoMaterias = $curso->cursoMaterias()
            ->with([
                'materia:id,nombre,codigo',
                'horarios' => function ($query) {
                    $query->with('periodo')
                        ->orderBy(Horario::diaColumn())
                        ->orderBy('hora_inicio');
                },
            ])
            ->orderBy('created_at')
            ->get();

        $horarios = $cursoMaterias->flatMap(function ($cursoMateria) {
            return $cursoMateria->horarios->map(function ($horario) use ($cursoMateria) {
                return [
                    'horario' => $horario,
                    'materia' => $cursoMateria->materia,
                    'alias' => $cursoMateria->alias,
                ];
            });
        });

        $horariosPorDia = $horarios->groupBy(function ($registro) {
            return $registro['horario']->dia ?? 'Sin día';
        })->map(function (Collection $registros) {
            return $registros->sortBy(function ($registro) {
                return $registro['horario']->hora_inicio?->format('H:i') ?? '';
            })->values();
        });

        return view('horarios.consulta', [
            'curso' => $curso,
            'cursoMaterias' => $cursoMaterias,
            'diasSemana' => $this->diasSemana(),
            'horariosPorDia' => $horariosPorDia,
            'totalHorarios' => $horarios->count(),
            'horariosPlano' => $horarios,
        ]);
    }

    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $data = $request->validate([
            'curso_materia_id' => ['required', 'integer'],
            'periodo_id' => ['nullable', 'integer'],
            'dia' => ['required', 'string', Rule::in($this->diasSemana())],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'aula' => ['nullable', 'string', 'max:255'],
        ], [
            'curso_materia_id.required' => 'Selecciona la materia del curso.',
            'curso_materia_id.integer' => 'La materia seleccionada no es válida.',
            'periodo_id.integer' => 'El periodo seleccionado no es válido.',
            'dia.required' => 'El día es obligatorio.',
            'dia.in' => 'Selecciona un día válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'hora_fin.required' => 'La hora de finalización es obligatoria.',
            'hora_fin.date_format' => 'La hora de finalización debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
            'aula.string' => 'El aula debe ser un texto válido.',
            'aula.max' => 'El aula no debe superar los 255 caracteres.',
        ]);

        $cursoMateria = $curso->cursoMaterias()
            ->where('id', $data['curso_materia_id'])
            ->firstOrFail();

        if (! empty($data['periodo_id'])) {
            $periodoValido = Periodo::query()
                ->where('id', $data['periodo_id'])
                ->where('materia_id', $cursoMateria->materia_id)
                ->exists();

            abort_unless($periodoValido, 404);
        }

        Horario::create([
            'curso_materia_id' => $cursoMateria->id,
            'periodo_id' => $data['periodo_id'] ?? null,
            'dia' => $data['dia'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'aula' => $data['aula'] ?? null,
        ]);

        return redirect()
            ->route('horarios.curso', $curso)
            ->with('success', 'Bloque horario registrado correctamente.');
    }

    public function destroy(Curso $curso, Horario $horario): RedirectResponse
    {
        abort_unless($horario->cursoMateria && $horario->cursoMateria->curso_id === $curso->id, 404);

        $horario->delete();

        return redirect()
            ->route('horarios.curso', $curso)
            ->with('success', 'Bloque horario eliminado correctamente.');
    }

    public function edit(Curso $curso, Horario $horario): View
    {
        abort_unless($horario->cursoMateria && $horario->cursoMateria->curso_id === $curso->id, 404);

        $curso->loadCount('estudiantes');

        $cursoMaterias = $curso->cursoMaterias()
            ->with('materia:id,nombre,codigo')
            ->orderBy('created_at')
            ->get();

        $materiaIds = $cursoMaterias->pluck('materia_id')->unique()->filter();

        $periodosPorMateria = Periodo::query()
            ->whereIn('materia_id', $materiaIds)
            ->orderBy('materia_id')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->groupBy('materia_id');

        return view('horarios.edit', [
            'curso' => $curso,
            'horario' => $horario,
            'cursoMaterias' => $cursoMaterias,
            'periodosPorMateria' => $periodosPorMateria,
            'diasSemana' => $this->diasSemana(),
        ]);
    }

    public function update(Request $request, Curso $curso, Horario $horario): RedirectResponse
    {
        abort_unless($horario->cursoMateria && $horario->cursoMateria->curso_id === $curso->id, 404);

        $data = $request->validate([
            'curso_materia_id' => ['required', 'integer'],
            'periodo_id' => ['nullable', 'integer'],
            'dia' => ['required', 'string', Rule::in($this->diasSemana())],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'aula' => ['nullable', 'string', 'max:255'],
        ], [
            'curso_materia_id.required' => 'Selecciona la materia del curso.',
            'curso_materia_id.integer' => 'La materia seleccionada no es válida.',
            'periodo_id.integer' => 'El periodo seleccionado no es válido.',
            'dia.required' => 'El día es obligatorio.',
            'dia.in' => 'Selecciona un día válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'hora_fin.required' => 'La hora de finalización es obligatoria.',
            'hora_fin.date_format' => 'La hora de finalización debe tener el formato HH:MM.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
            'aula.string' => 'El aula debe ser un texto válido.',
            'aula.max' => 'El aula no debe superar los 255 caracteres.',
        ]);

        $cursoMateria = $curso->cursoMaterias()
            ->where('id', $data['curso_materia_id'])
            ->firstOrFail();

        if (! empty($data['periodo_id'])) {
            $periodoValido = Periodo::query()
                ->where('id', $data['periodo_id'])
                ->where('materia_id', $cursoMateria->materia_id)
                ->exists();

            abort_unless($periodoValido, 404);
        }

        $horario->update([
            'curso_materia_id' => $cursoMateria->id,
            'periodo_id' => $data['periodo_id'] ?? null,
            'dia' => $data['dia'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'aula' => $data['aula'] ?? null,
        ]);

        return redirect()
            ->route('horarios.curso', $curso)
            ->with('success', 'Bloque horario actualizado correctamente.');
    }

    protected function diasSemana(): array
    {
        return ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    }
}
