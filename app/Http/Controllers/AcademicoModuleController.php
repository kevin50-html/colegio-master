<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AcademicoModuleController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'periodos', 'horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_cursos',
            'gestionar_materias',
            'gestionar_periodos',
            'gestionar_horarios',
            'crear_actividades',
            'registrar_notas',
            'ver_cursos',
            'ver_materias',
        ])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a la gestión académica.');
        }

        $resumen = [
            'cursos' => Curso::count(),
            'materias' => Materia::count(),
            'periodos' => Periodo::count(),
            'horarios' => Horario::count(),
        ];

        return view('academico.index', [
            'resumen' => $resumen,
        ]);
    }

    public function materias(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias', 'cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_materias',
            'gestionar_cursos',
            'gestionar_periodos',
            'ver_materias',
            'ver_cursos',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar las materias.');
        }

        $cursoId = $request->integer('curso_id');
        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $materias = Materia::with('curso')
            ->when($cursoId, fn ($query) => $query->where('curso_id', $cursoId))
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('academico.modulos.materias', [
            'materias' => $materias,
            'busqueda' => $busqueda,
            'cursos' => $cursos,
            'cursoId' => $cursoId,
        ]);
    }

    public function periodos(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos', 'materias', 'cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'ver_materias',
            'ver_cursos',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar los periodos.');
        }

        $cursoId = $request->integer('curso_id');
        $materiaId = $request->integer('materia_id');
        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $materias = Materia::with('curso')
            ->when($cursoId, fn ($query) => $query->where('curso_id', $cursoId))
            ->orderBy('nombre')
            ->get();

        $periodos = Periodo::with(['materia.curso'])
            ->withCount('horarios')
            ->when($materiaId, fn ($query) => $query->where('materia_id', $materiaId))
            ->when($cursoId, fn ($query) => $query->whereHas('materia', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($busqueda !== '', fn ($query) => $query->where('nombre', 'like', "%{$busqueda}%"))
            ->orderBy('orden')
            ->paginate(15)
            ->withQueryString();

        return view('academico.modulos.periodos', [
            'periodos' => $periodos,
            'busqueda' => $busqueda,
            'cursos' => $cursos,
            'materias' => $materias,
            'cursoId' => $cursoId,
            'materiaId' => $materiaId,
        ]);
    }

    public function horarios(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios', 'periodos', 'materias', 'cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_horarios',
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'ver_materias',
            'ver_cursos',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar los horarios.');
        }

        $cursoId = $request->integer('curso_id');
        $materiaId = $request->integer('materia_id');
        $periodoId = $request->integer('periodo_id');
        $dia = trim((string) $request->input('dia'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $materias = Materia::with('curso')
            ->when($cursoId, fn ($query) => $query->where('curso_id', $cursoId))
            ->orderBy('nombre')
            ->get();

        $periodos = Periodo::with(['materia.curso'])
            ->when($cursoId, fn ($query) => $query->whereHas('materia', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($materiaId, fn ($query) => $query->where('materia_id', $materiaId))
            ->orderBy('orden')
            ->get();

        $diasDisponibles = Horario::select('dia_semana')
            ->distinct()
            ->orderBy('dia_semana')
            ->pluck('dia_semana')
            ->all();

        $horarios = Horario::with(['periodo.materia.curso'])
            ->when($periodoId, fn ($query) => $query->where('periodo_id', $periodoId))
            ->when($materiaId, fn ($query) => $query->whereHas('periodo', fn ($sub) => $sub->where('materia_id', $materiaId)))
            ->when($cursoId, fn ($query) => $query->whereHas('periodo.materia', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($dia !== '', fn ($query) => $query->where('dia_semana', $dia))
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->paginate(15)
            ->withQueryString();

        return view('academico.modulos.horarios', [
            'horarios' => $horarios,
            'cursos' => $cursos,
            'materias' => $materias,
            'periodos' => $periodos,
            'diasDisponibles' => $diasDisponibles,
            'cursoId' => $cursoId,
            'materiaId' => $materiaId,
            'periodoId' => $periodoId,
            'dia' => $dia,
        ]);
    }

    public function cursosPorMaterias(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'periodos', 'horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_cursos',
            'gestionar_materias',
            'gestionar_periodos',
            'gestionar_horarios',
            'ver_cursos',
            'ver_materias',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar el resumen de cursos y materias.');
        }

        $cursoId = $request->integer('curso_id');

        $cursosListado = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $cursos = Curso::with(['materias' => function ($query) {
            $query->orderBy('nombre')
                ->withCount('periodos')
                ->with(['periodos' => function ($periodoQuery) {
                    $periodoQuery->orderBy('orden')->withCount('horarios');
                }]);
        }])
            ->withCount('materias')
            ->when($cursoId, fn ($query) => $query->where('id', $cursoId))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('academico.modulos.cursos-materias', [
            'cursos' => $cursos,
            'cursosListado' => $cursosListado,
            'cursoId' => $cursoId,
        ]);
    }

    private function asegurarTablas(array $tablas): ?RedirectResponse
    {
        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                return redirect()->route('dashboard')->with(
                    'error',
                    'Debes ejecutar las migraciones más recientes (php artisan migrate) para habilitar la gestión académica.'
                );
            }
        }

        return null;
    }
}
