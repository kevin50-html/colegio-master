<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotaConsultaController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensurePuedeConsultar();

        $busqueda = $request->string('buscar');

        $cursos = Curso::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->withCount(['estudiantes', 'docentes', 'materias'])
            ->orderBy('nombre')
            ->paginate(9)
            ->withQueryString();

        return view('notas_consulta.index', [
            'cursos' => $cursos,
            'busqueda' => $busqueda,
        ]);
    }

    public function curso(Curso $curso): View
    {
        $this->ensurePuedeConsultar();

        $curso->load([
            'docentes',
            'estudiantes',
            'materias' => function ($query) {
                $query->with(['periodos' => function ($periodos) {
                    $periodos->orderBy('orden')->orderBy('nombre');
                }, 'periodos.actividades']);
            },
        ]);

        return view('notas_consulta.curso', [
            'curso' => $curso,
        ]);
    }

    public function materia(Curso $curso, Materia $materia): View
    {
        $this->ensurePuedeConsultar();
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
            ->keyBy(fn (Nota $nota) => $this->notaKey($nota->estudiante_id, $nota->actividad_id));

        $actividadesPorPeriodo = $actividades->groupBy(fn ($actividad) => $actividad->periodo?->id ?? 'sin-periodo');

        $periodosMeta = $actividadesPorPeriodo->map(function ($grupo) {
            $periodo = $grupo->first()->periodo;

            return [
                'id' => $periodo?->id,
                'nombre' => $periodo->nombre ?? 'Sin periodo',
            ];
        });

        $resumenEstudiantes = collect($estudiantes)->mapWithKeys(function ($estudiante) use ($actividades, $notas, $actividadesPorPeriodo) {
            $valoresPorActividad = [];

            foreach ($actividades as $actividad) {
                $nota = $notas->get($this->notaKey($estudiante->id, $actividad->id));
                $valoresPorActividad[$actividad->id] = $nota?->valor;
            }

            $valoresConNotas = array_filter($valoresPorActividad, fn ($valor) => $valor !== null);
            $promedioGeneral = !empty($valoresConNotas)
                ? round(array_sum($valoresConNotas) / count($valoresConNotas), 2)
                : null;

            $promediosPeriodos = [];

            foreach ($actividadesPorPeriodo as $periodoKey => $grupo) {
                $valoresPeriodo = [];

                foreach ($grupo as $actividad) {
                    $valor = $valoresPorActividad[$actividad->id] ?? null;

                    if ($valor !== null) {
                        $valoresPeriodo[] = $valor;
                    }
                }

                $promediosPeriodos[$periodoKey] = !empty($valoresPeriodo)
                    ? round(array_sum($valoresPeriodo) / count($valoresPeriodo), 2)
                    : null;
            }

            return [
                $estudiante->id => [
                    'general' => $promedioGeneral,
                    'periodos' => $promediosPeriodos,
                ],
            ];
        });

        $promediosGenerales = $resumenEstudiantes->pluck('general')->filter(fn ($valor) => $valor !== null);
        $promedioCurso = $promediosGenerales->isNotEmpty()
            ? round($promediosGenerales->avg(), 2)
            : null;

        $aprobados = $resumenEstudiantes->filter(function ($resumen) {
            return $resumen['general'] !== null && $resumen['general'] >= 3;
        })->count();

        $conNotas = $promediosGenerales->count();
        $reprobados = $conNotas - $aprobados;

        return view('notas_consulta.materia', [
            'curso' => $curso,
            'materia' => $materia,
            'estudiantes' => $estudiantes,
            'actividades' => $actividades,
            'notas' => $notas,
            'actividadesPorPeriodo' => $actividadesPorPeriodo,
            'periodosMeta' => $periodosMeta,
            'resumenEstudiantes' => $resumenEstudiantes,
            'promedioCurso' => $promedioCurso,
            'aprobados' => $aprobados,
            'reprobados' => $reprobados,
            'conNotas' => $conNotas,
        ]);
    }

    protected function ensureMateriaPerteneceAlCurso(Curso $curso, Materia $materia): void
    {
        $relacion = $curso->materias()
            ->where('materias.id', $materia->id)
            ->exists();

        abort_unless($relacion, 404);
    }

    protected function ensurePuedeConsultar(): void
    {
        abort_unless(Auth::user()?->hasAnyPermission(['ver_notas', 'registrar_notas', 'gestionar_materias', 'acceso_total']) ?? false, 403);
    }

    protected function notaKey(int $estudianteId, int $actividadId): string
    {
        return $estudianteId . '-' . $actividadId;
    }
}
