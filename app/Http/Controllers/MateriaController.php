<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Curso;
use App\Models\Horario;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(Request $request, Curso $curso): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_materias',
            'gestionar_cursos',
            'gestionar_periodos',
            'gestionar_horarios',
            'crear_actividades',
            'registrar_notas',
        ])) {
            return redirect()->route('academico.cursos.index')->with('error', 'No tienes permisos para acceder a las materias del curso.');
        }

        $busqueda = trim((string) $request->input('q'));

        $materias = $curso->materias()
            ->withCount('periodos')
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

        return view('academico.materias.index', [
            'curso' => $curso,
            'materias' => $materias,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(Curso $curso): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias'])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para crear materias.');
        }

        return view('academico.materias.create', [
            'curso' => $curso,
        ]);
    }

    public function store(Request $request, Curso $curso): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias'])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para crear materias.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('materias')->where('curso_id', $curso->id)],
            'codigo' => ['nullable', 'string', 'max:50', 'unique:materias,codigo'],
            'intensidad_horaria' => ['nullable', 'integer', 'min:1', 'max:40'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $data['curso_id'] = $curso->id;

        Materia::create($data);

        return redirect()->route('academico.cursos.materias.index', $curso)->with('success', 'Materia creada correctamente.');
    }

    public function show(Curso $curso, Materia $materia): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias', 'periodos', 'horarios', 'actividades'])) {
            return $redirect;
        }

        if ($materia->curso_id !== $curso->id) {
            return redirect()->route('academico.cursos.index')->with('error', 'La materia no pertenece al curso indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_materias',
            'gestionar_cursos',
            'gestionar_periodos',
            'gestionar_horarios',
            'crear_actividades',
            'registrar_notas',
        ])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para ver esta materia.');
        }

        $periodos = $materia->periodos()
            ->withCount('horarios')
            ->orderBy('orden')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'periodos' => $materia->periodos()->count(),
            'horarios' => Horario::whereHas('periodo', fn ($q) => $q->where('materia_id', $materia->id))->count(),
            'actividades' => Actividad::whereHas('horario.periodo', fn ($q) => $q->where('materia_id', $materia->id))->count(),
        ];

        return view('academico.materias.show', [
            'curso' => $curso,
            'materia' => $materia,
            'periodos' => $periodos,
            'resumen' => $resumen,
        ]);
    }

    public function edit(Curso $curso, Materia $materia): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        if ($materia->curso_id !== $curso->id) {
            return redirect()->route('academico.cursos.index')->with('error', 'La materia no pertenece al curso indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias'])) {
            return redirect()->route('academico.cursos.materias.show', [$curso, $materia])->with('error', 'No tienes permisos para editar esta materia.');
        }

        return view('academico.materias.edit', [
            'curso' => $curso,
            'materia' => $materia,
        ]);
    }

    public function update(Request $request, Curso $curso, Materia $materia): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        if ($materia->curso_id !== $curso->id) {
            return redirect()->route('academico.cursos.index')->with('error', 'La materia no pertenece al curso indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias'])) {
            return redirect()->route('academico.cursos.materias.show', [$curso, $materia])->with('error', 'No tienes permisos para editar esta materia.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('materias')->ignore($materia->id)->where('curso_id', $curso->id)],
            'codigo' => ['nullable', 'string', 'max:50', Rule::unique('materias', 'codigo')->ignore($materia->id)],
            'intensidad_horaria' => ['nullable', 'integer', 'min:1', 'max:40'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $materia->update($data);

        return redirect()->route('academico.cursos.materias.show', [$curso, $materia])->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Curso $curso, Materia $materia): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['cursos', 'materias'])) {
            return $redirect;
        }

        if ($materia->curso_id !== $curso->id) {
            return redirect()->route('academico.cursos.index')->with('error', 'La materia no pertenece al curso indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias'])) {
            return redirect()->route('academico.cursos.materias.index', $curso)->with('error', 'No tienes permisos para eliminar materias.');
        }

        if ($materia->periodos()->exists()) {
            return redirect()->route('academico.cursos.materias.show', [$curso, $materia])->with('error', 'No se puede eliminar una materia con periodos asociados.');
        }

        $materia->delete();

        return redirect()->route('academico.cursos.materias.index', $curso)->with('success', 'Materia eliminada correctamente.');
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
