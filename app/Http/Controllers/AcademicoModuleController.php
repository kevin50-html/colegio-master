<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\CursoMateria;
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
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'curso_materia', 'periodos', 'horarios', 'actividades'])) {
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
            'acceso_total',
        ])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a la gestión académica.');
        }

        $resumen = [
            'cursos' => Curso::count(),
            'materias' => Materia::count(),
            'asignaciones' => CursoMateria::count(),
            'periodos' => Periodo::count(),
            'horarios' => Horario::count(),
            'actividades' => Actividad::count(),
        ];

        return view('academico.index', [
            'resumen' => $resumen,
        ]);
    }

    public function materias(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias', 'curso_materia', 'cursos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_materias',
            'gestionar_cursos',
            'acceso_total',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar las materias.');
        }

        $cursoId = $request->integer('curso_id');
        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $materias = Materia::withCount('cursoMaterias')
            ->with(['cursos' => function ($query) {
                $query->orderBy('nombre');
            }])
            ->when($cursoId, fn ($query) => $query->whereHas('cursos', fn ($sub) => $sub->where('cursos.id', $cursoId)))
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
        if ($redirect = $this->asegurarTablas(['periodos', 'curso_materia', 'cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'acceso_total',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar los periodos.');
        }

        $cursoId = $request->integer('curso_id');
        $materiaId = $request->integer('materia_id');
        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);
        $materias = Materia::orderBy('nombre')->get(['id', 'nombre']);

        $periodos = Periodo::with(['cursoMateria.curso', 'cursoMateria.materia'])
            ->withCount('actividades')
            ->when($cursoId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($materiaId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('materia_id', $materiaId)))
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

    public function actividades(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['actividades', 'periodos', 'curso_materia', 'cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'crear_actividades',
            'registrar_notas',
            'gestionar_periodos',
            'gestionar_materias',
            'acceso_total',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar las actividades.');
        }

        $cursoId = $request->integer('curso_id');
        $materiaId = $request->integer('materia_id');
        $periodoId = $request->integer('periodo_id');
        $busqueda = trim((string) $request->input('q'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);
        $materias = Materia::orderBy('nombre')->get(['id', 'nombre']);
        $periodos = Periodo::with(['cursoMateria.curso', 'cursoMateria.materia'])
            ->when($cursoId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($materiaId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('materia_id', $materiaId)))
            ->orderBy('orden')
            ->get();

        $actividades = Actividad::with(['periodo.cursoMateria.curso', 'periodo.cursoMateria.materia'])
            ->withCount('notas')
            ->when($cursoId, fn ($query) => $query->whereHas('periodo.cursoMateria', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($materiaId, fn ($query) => $query->whereHas('periodo.cursoMateria', fn ($sub) => $sub->where('materia_id', $materiaId)))
            ->when($periodoId, fn ($query) => $query->where('periodo_id', $periodoId))
            ->when($busqueda !== '', fn ($query) => $query->where('titulo', 'like', "%{$busqueda}%"))
            ->orderBy('fecha_entrega')
            ->orderBy('titulo')
            ->paginate(15)
            ->withQueryString();

        return view('academico.modulos.actividades', [
            'actividades' => $actividades,
            'cursos' => $cursos,
            'materias' => $materias,
            'periodos' => $periodos,
            'cursoId' => $cursoId,
            'materiaId' => $materiaId,
            'periodoId' => $periodoId,
            'busqueda' => $busqueda,
        ]);
    }

    public function horarios(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios', 'curso_materia', 'periodos', 'cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_horarios',
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'acceso_total',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar los horarios.');
        }

        $cursoId = $request->integer('curso_id');
        $materiaId = $request->integer('materia_id');
        $periodoId = $request->integer('periodo_id');
        $dia = trim((string) $request->input('dia'));

        $cursos = Curso::orderBy('nombre')->get(['id', 'nombre']);
        $materias = Materia::orderBy('nombre')->get(['id', 'nombre']);
        $periodos = Periodo::with('cursoMateria.materia')->orderBy('orden')->get(['id', 'nombre', 'curso_materia_id']);

        $diasDisponibles = Horario::select('dia')->distinct()->orderBy('dia')->pluck('dia')->all();

        $horarios = Horario::with(['cursoMateria.curso', 'cursoMateria.materia', 'periodo'])
            ->when($cursoId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('curso_id', $cursoId)))
            ->when($materiaId, fn ($query) => $query->whereHas('cursoMateria', fn ($sub) => $sub->where('materia_id', $materiaId)))
            ->when($periodoId, fn ($query) => $query->where('periodo_id', $periodoId))
            ->when($dia !== '', fn ($query) => $query->where('dia', $dia))
            ->orderBy('dia')
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
        if ($redirect = $this->asegurarTablas(['curso_materia', 'cursos', 'materias', 'periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_cursos',
            'gestionar_materias',
            'gestionar_periodos',
            'acceso_total',
        ])) {
            return redirect()->route('academico.index')->with('error', 'No tienes permisos para consultar el resumen de cursos y materias.');
        }

        $cursoId = $request->integer('curso_id');

        $cursosListado = Curso::orderBy('nombre')->get(['id', 'nombre']);

        $cursos = Curso::with(['cursoMaterias.materia' => function ($query) {
            $query->orderBy('nombre');
        }, 'cursoMaterias.periodos' => function ($query) {
            $query->orderBy('orden')->withCount('actividades');
        }])
            ->withCount('cursoMaterias')
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
                return redirect()->route('dashboard')->with('error', 'Debes ejecutar las migraciones más recientes (php artisan migrate) para habilitar la gestión académica.');
            }
        }

        return null;
    }
}
