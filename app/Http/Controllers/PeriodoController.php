<?php

namespace App\Http\Controllers;

use App\Models\CursoMateria;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PeriodoController extends Controller
{
    public function index(CursoMateria $cursoMateria): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'gestionar_materias', 'acceso_total'])) {
            return redirect()->route('academico.cursos.materias.index', $cursoMateria->curso)->with('error', 'No tienes permisos para consultar los periodos.');
        }

        $periodos = $cursoMateria->periodos()->withCount('actividades')->orderBy('orden')->get();

        return view('academico.periodos.index', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'periodos' => $periodos,
        ]);
    }

    public function create(CursoMateria $cursoMateria): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.index', $cursoMateria)->with('error', 'No tienes permisos para crear periodos.');
        }

        $siguienteOrden = max(1, ($cursoMateria->periodos()->max('orden') ?? 0) + 1);

        return view('academico.periodos.create', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'ordenSugerido' => $siguienteOrden,
        ]);
    }

    public function store(Request $request, CursoMateria $cursoMateria): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['curso_materia', 'periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.index', $cursoMateria)->with('error', 'No tienes permisos para crear periodos.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $data['orden'] = $data['orden'] ?? max(1, ($cursoMateria->periodos()->max('orden') ?? 0) + 1);

        if ($cursoMateria->periodos()->where('orden', $data['orden'])->exists()) {
            return redirect()->back()->withInput()->with('error', 'Ya existe un periodo con el orden indicado.');
        }

        $cursoMateria->periodos()->create($data);

        return redirect()->route('academico.curso-materias.periodos.index', $cursoMateria)->with('success', 'Periodo creado correctamente.');
    }

    public function show(CursoMateria $cursoMateria, Periodo $periodo): View
    {
        $this->validarPertenencia($cursoMateria, $periodo);

        $periodo->load(['horarios' => fn ($query) => $query->orderBy('dia'), 'actividades' => fn ($query) => $query->orderBy('fecha_entrega')]);

        return view('academico.periodos.show', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'periodo' => $periodo,
        ]);
    }

    public function edit(CursoMateria $cursoMateria, Periodo $periodo): View|RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $periodo);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo])->with('error', 'No tienes permisos para editar este periodo.');
        }

        return view('academico.periodos.edit', [
            'cursoMateria' => $cursoMateria->load(['curso', 'materia']),
            'periodo' => $periodo,
        ]);
    }

    public function update(Request $request, CursoMateria $cursoMateria, Periodo $periodo): RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $periodo);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo])->with('error', 'No tienes permisos para actualizar este periodo.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden' => 'required|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        if ($cursoMateria->periodos()->where('orden', $data['orden'])->where('id', '!=', $periodo->id)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Ya existe un periodo con ese orden.');
        }

        $periodo->update($data);

        return redirect()->route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo])->with('success', 'Periodo actualizado correctamente.');
    }

    public function destroy(CursoMateria $cursoMateria, Periodo $periodo): RedirectResponse
    {
        $this->validarPertenencia($cursoMateria, $periodo);

        $usuario = Auth::user();
        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos', 'acceso_total'])) {
            return redirect()->route('academico.curso-materias.periodos.index', $cursoMateria)->with('error', 'No tienes permisos para eliminar periodos.');
        }

        if ($periodo->actividades()->exists()) {
            return redirect()->route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo])->with('error', 'No se puede eliminar el periodo porque tiene actividades asociadas.');
        }

        $periodo->delete();

        return redirect()->route('academico.curso-materias.periodos.index', $cursoMateria)->with('success', 'Periodo eliminado correctamente.');
    }

    private function validarPertenencia(CursoMateria $cursoMateria, Periodo $periodo): void
    {
        if ($periodo->curso_materia_id !== $cursoMateria->id) {
            abort(404);
        }
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
