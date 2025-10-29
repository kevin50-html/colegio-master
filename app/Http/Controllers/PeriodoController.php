<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodoController extends Controller
{
    public function index(Materia $materia): RedirectResponse
    {
        return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia]);
    }

    public function create(Materia $materia): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos'])) {
            return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('error', 'No tienes permisos para crear periodos.');
        }

        $siguienteOrden = ($materia->periodos()->max('orden') ?? 0) + 1;

        return view('academico.periodos.create', [
            'materia' => $materia,
            'siguienteOrden' => $siguienteOrden,
        ]);
    }

    public function store(Request $request, Materia $materia): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos'])) {
            return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('error', 'No tienes permisos para crear periodos.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('periodos')->where('materia_id', $materia->id)],
            'orden' => ['required', 'integer', 'min:1', 'max:20'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $data['materia_id'] = $materia->id;

        Periodo::create($data);

        return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('success', 'Periodo creado correctamente.');
    }

    public function show(Materia $materia, Periodo $periodo): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos', 'horarios', 'actividades'])) {
            return $redirect;
        }

        if ($periodo->materia_id !== $materia->id) {
            return redirect()->route('academico.cursos.materias.index', $materia->curso)->with('error', 'El periodo no pertenece a la materia indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_periodos',
            'gestionar_horarios',
            'gestionar_materias',
            'gestionar_cursos',
            'crear_actividades',
            'registrar_notas',
        ])) {
            return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('error', 'No tienes permisos para ver este periodo.');
        }

        $horarios = $periodo->horarios()
            ->withCount('actividades')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'horarios' => $periodo->horarios()->count(),
            'actividades' => Actividad::whereHas('horario', fn ($q) => $q->where('periodo_id', $periodo->id))->count(),
        ];

        return view('academico.periodos.show', [
            'materia' => $materia,
            'periodo' => $periodo,
            'horarios' => $horarios,
            'resumen' => $resumen,
        ]);
    }

    public function edit(Materia $materia, Periodo $periodo): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos'])) {
            return $redirect;
        }

        if ($periodo->materia_id !== $materia->id) {
            return redirect()->route('academico.cursos.materias.index', $materia->curso)->with('error', 'El periodo no pertenece a la materia indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos'])) {
            return redirect()->route('academico.materias.periodos.show', [$materia, $periodo])->with('error', 'No tienes permisos para editar este periodo.');
        }

        return view('academico.periodos.edit', [
            'materia' => $materia,
            'periodo' => $periodo,
        ]);
    }

    public function update(Request $request, Materia $materia, Periodo $periodo): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos'])) {
            return $redirect;
        }

        if ($periodo->materia_id !== $materia->id) {
            return redirect()->route('academico.cursos.materias.index', $materia->curso)->with('error', 'El periodo no pertenece a la materia indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos'])) {
            return redirect()->route('academico.materias.periodos.show', [$materia, $periodo])->with('error', 'No tienes permisos para editar este periodo.');
        }

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('periodos')->ignore($periodo->id)->where('materia_id', $materia->id)],
            'orden' => ['required', 'integer', 'min:1', 'max:20'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $periodo->update($data);

        return redirect()->route('academico.materias.periodos.show', [$materia, $periodo])->with('success', 'Periodo actualizado correctamente.');
    }

    public function destroy(Materia $materia, Periodo $periodo): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['periodos'])) {
            return $redirect;
        }

        if ($periodo->materia_id !== $materia->id) {
            return redirect()->route('academico.cursos.materias.index', $materia->curso)->with('error', 'El periodo no pertenece a la materia indicada.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_periodos'])) {
            return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('error', 'No tienes permisos para eliminar periodos.');
        }

        if ($periodo->horarios()->exists()) {
            return redirect()->route('academico.materias.periodos.show', [$materia, $periodo])->with('error', 'No se puede eliminar un periodo con horarios asociados.');
        }

        $periodo->delete();

        return redirect()->route('academico.cursos.materias.show', [$materia->curso, $materia])->with('success', 'Periodo eliminado correctamente.');
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
