<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Nota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotaController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('buscar');

        $cursos = Curso::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->withCount(['estudiantes', 'docentes', 'materias'])
            ->orderBy('nombre')
            ->paginate(9)
            ->withQueryString();

        return view('notas.index', [
            'cursos' => $cursos,
            'busqueda' => $busqueda,
        ]);
    }

    public function curso(Curso $curso): View
    {
        $curso->load([
            'docentes',
            'estudiantes',
            'materias' => function ($query) {
                $query->with(['periodos' => function ($periodos) {
                    $periodos->orderBy('orden')->orderBy('nombre');
                }, 'periodos.actividades']);
            },
        ]);

        return view('notas.curso', [
            'curso' => $curso,
        ]);
    }

    public function materia(Curso $curso, Materia $materia): View
    {
        $this->ensureMateriaPerteneceAlCurso($curso, $materia);

        $estudiantes = Estudiante::query()
            ->where('curso_id', $curso->id)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $actividades = Actividad::query()
            ->select('actividades.*')
            ->join('periodos', 'periodos.id', '=', 'actividades.periodo_id')
            ->where('periodos.materia_id', $materia->id)
            ->orderBy('periodos.orden')
            ->orderBy('actividades.fecha_entrega')
            ->orderBy('actividades.created_at')
            ->with('periodo')
            ->get();

        $notas = Nota::query()
            ->whereIn('actividad_id', $actividades->pluck('id'))
            ->whereIn('estudiante_id', $estudiantes->pluck('id'))
            ->get()
            ->keyBy(function (Nota $nota) {
                return $this->notaKey($nota->estudiante_id, $nota->actividad_id);
            });

        return view('notas.materia', [
            'curso' => $curso,
            'materia' => $materia,
            'estudiantes' => $estudiantes,
            'actividades' => $actividades,
            'notas' => $notas,
        ]);
    }

    public function guardar(Request $request, Curso $curso, Materia $materia): RedirectResponse
    {
        $this->ensureMateriaPerteneceAlCurso($curso, $materia);

        $data = $request->validate([
            'actividad_id' => ['required', 'integer', 'exists:actividades,id'],
            'estudiante_id' => ['required', 'integer', 'exists:estudiantes,id'],
            'valor' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        $actividad = Actividad::query()
            ->where('id', $data['actividad_id'])
            ->whereHas('periodo', function ($query) use ($materia) {
                $query->where('materia_id', $materia->id);
            })
            ->firstOrFail();

        $estudiante = Estudiante::query()
            ->where('id', $data['estudiante_id'])
            ->where('curso_id', $curso->id)
            ->firstOrFail();

        if ($data['valor'] === null) {
            Nota::query()
                ->where('actividad_id', $actividad->id)
                ->where('estudiante_id', $estudiante->id)
                ->delete();

            return redirect()
                ->route('notas.materia', [$curso, $materia])
                ->with('success', 'Se eliminó la calificación para el estudiante.');
        }

        Nota::updateOrCreate(
            [
                'actividad_id' => $actividad->id,
                'estudiante_id' => $estudiante->id,
            ],
            [
                'valor' => $data['valor'],
            ]
        );

        return redirect()
            ->route('notas.materia', [$curso, $materia])
            ->with('success', 'Calificación registrada correctamente.');
    }

    protected function ensureMateriaPerteneceAlCurso(Curso $curso, Materia $materia): void
    {
        $relacion = $curso->materias()
            ->where('materias.id', $materia->id)
            ->exists();

        abort_unless($relacion, 404);
    }

    protected function notaKey(int $estudianteId, int $actividadId): string
    {
        return $estudianteId . '-' . $actividadId;
    }
}
