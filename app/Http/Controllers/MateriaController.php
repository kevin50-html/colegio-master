<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total', 'gestionar_cursos'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para consultar las materias.');
        }

        $busqueda = trim((string) $request->input('q'));

        $materias = Materia::withCount('cursoMaterias')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('academico.materias.index', [
            'materias' => $materias,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.materias.index')->with('error', 'No tienes permisos para crear materias.');
        }

        return view('academico.materias.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.materias.index')->with('error', 'No tienes permisos para crear materias.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre',
            'codigo' => 'nullable|string|max:50|unique:materias,codigo',
            'intensidad_horaria' => 'nullable|integer|min:1|max:40',
            'descripcion' => 'nullable|string',
        ]);

        Materia::create($data);

        return redirect()->route('academico.materias.index')->with('success', 'Materia creada correctamente.');
    }

    public function show(Materia $materia): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias', 'curso_materia', 'periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'gestionar_cursos', 'acceso_total'])) {
            return redirect()->route('academico.materias.index')->with('error', 'No tienes permisos para ver esta materia.');
        }

        $materia->load(['cursos' => function ($query) {
            $query->orderBy('nombre');
        }, 'cursoMaterias.periodos' => function ($query) {
            $query->orderBy('orden');
        }]);

        $resumen = [
            'cursos' => $materia->cursos->count(),
            'periodos' => $materia->cursoMaterias->sum(fn ($cm) => $cm->periodos->count()),
        ];

        return view('academico.materias.show', [
            'materia' => $materia,
            'resumen' => $resumen,
        ]);
    }

    public function edit(Materia $materia): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.materias.show', $materia)->with('error', 'No tienes permisos para editar esta materia.');
        }

        return view('academico.materias.edit', [
            'materia' => $materia,
        ]);
    }

    public function update(Request $request, Materia $materia): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.materias.show', $materia)->with('error', 'No tienes permisos para actualizar esta materia.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:materias,nombre,' . $materia->id,
            'codigo' => 'nullable|string|max:50|unique:materias,codigo,' . $materia->id,
            'intensidad_horaria' => 'nullable|integer|min:1|max:40',
            'descripcion' => 'nullable|string',
        ]);

        $materia->update($data);

        return redirect()->route('academico.materias.show', $materia)->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['materias', 'curso_materia'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.materias.index')->with('error', 'No tienes permisos para eliminar materias.');
        }

        if ($materia->cursoMaterias()->exists()) {
            return redirect()->route('academico.materias.show', $materia)->with('error', 'No se puede eliminar la materia porque está asignada a cursos.');
        }

        $materia->delete();

        return redirect()->route('academico.materias.index')->with('success', 'Materia eliminada correctamente.');
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
