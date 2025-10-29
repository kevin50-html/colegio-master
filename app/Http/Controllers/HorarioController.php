<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Horario;
use App\Models\Periodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(Periodo $periodo): RedirectResponse
    {
        return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo]);
    }

    public function create(Periodo $periodo): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios'])) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'No tienes permisos para crear horarios.');
        }

        return view('academico.horarios.create', [
            'periodo' => $periodo,
        ]);
    }

    public function store(Request $request, Periodo $periodo): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios'])) {
            return $redirect;
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios'])) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'No tienes permisos para crear horarios.');
        }

        $data = $request->validate([
            'dia_semana' => ['required', 'string', 'max:20'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'aula' => ['nullable', 'string', 'max:100'],
            'modalidad' => ['nullable', 'string', 'max:100'],
        ]);

        $data['periodo_id'] = $periodo->id;

        Horario::create($data);

        return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('success', 'Horario creado correctamente.');
    }

    public function show(Periodo $periodo, Horario $horario): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios', 'actividades'])) {
            return $redirect;
        }

        if ($horario->periodo_id !== $periodo->id) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'El horario no pertenece al periodo indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission([
            'gestionar_horarios',
            'gestionar_periodos',
            'gestionar_materias',
            'gestionar_cursos',
            'crear_actividades',
            'registrar_notas',
        ])) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'No tienes permisos para ver este horario.');
        }

        $actividades = $horario->actividades()
            ->withCount('notas')
            ->orderBy('fecha_entrega')
            ->orderBy('titulo')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'actividades' => $horario->actividades()->count(),
            'notas' => Actividad::where('horario_id', $horario->id)->withCount('notas')->get()->sum('notas_count'),
        ];

        return view('academico.horarios.show', [
            'periodo' => $periodo,
            'horario' => $horario,
            'actividades' => $actividades,
            'resumen' => $resumen,
        ]);
    }

    public function edit(Periodo $periodo, Horario $horario): View|RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios'])) {
            return $redirect;
        }

        if ($horario->periodo_id !== $periodo->id) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'El horario no pertenece al periodo indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios'])) {
            return redirect()->route('academico.periodos.horarios.show', [$periodo, $horario])->with('error', 'No tienes permisos para editar este horario.');
        }

        return view('academico.horarios.edit', [
            'periodo' => $periodo,
            'horario' => $horario,
        ]);
    }

    public function update(Request $request, Periodo $periodo, Horario $horario): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios'])) {
            return $redirect;
        }

        if ($horario->periodo_id !== $periodo->id) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'El horario no pertenece al periodo indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios'])) {
            return redirect()->route('academico.periodos.horarios.show', [$periodo, $horario])->with('error', 'No tienes permisos para editar este horario.');
        }

        $data = $request->validate([
            'dia_semana' => ['required', 'string', 'max:20'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'aula' => ['nullable', 'string', 'max:100'],
            'modalidad' => ['nullable', 'string', 'max:100'],
        ]);

        $horario->update($data);

        return redirect()->route('academico.periodos.horarios.show', [$periodo, $horario])->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Periodo $periodo, Horario $horario): RedirectResponse
    {
        if ($redirect = $this->asegurarTablas(['horarios'])) {
            return $redirect;
        }

        if ($horario->periodo_id !== $periodo->id) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'El horario no pertenece al periodo indicado.');
        }

        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_horarios'])) {
            return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('error', 'No tienes permisos para eliminar horarios.');
        }

        if ($horario->actividades()->exists()) {
            return redirect()->route('academico.periodos.horarios.show', [$periodo, $horario])->with('error', 'No se puede eliminar un horario con actividades asociadas.');
        }

        $horario->delete();

        return redirect()->route('academico.materias.periodos.show', [$periodo->materia, $periodo])->with('success', 'Horario eliminado correctamente.');
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
