<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'ver_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a estudiantes.');
        }

        $busqueda = trim((string) $request->input('q'));
        $estado = $request->input('estado', 'todos');

        $estudiantes = Estudiante::with(['curso', 'usuario'])
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombres', 'like', "%{$busqueda}%")
                        ->orWhere('apellidos', 'like', "%{$busqueda}%")
                        ->orWhere('documento_identidad', 'like', "%{$busqueda}%");
                });
            })
            ->when($estado !== 'todos', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        $estadosDisponibles = [
            'todos' => 'Todos',
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'egresado' => 'Egresado',
        ];

        return view('estudiantes.index', [
            'estudiantes' => $estudiantes,
            'busqueda' => $busqueda,
            'estadoSeleccionado' => $estado,
            'estadosDisponibles' => $estadosDisponibles,
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function crear(): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'matricular_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear estudiantes.');
        }

        return view('estudiantes.crear', [
            'cursos' => $this->cursosDisponibles(),
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function guardar(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'matricular_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear estudiantes.');
        }

        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100|unique:estudiantes,documento_identidad',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'curso_id' => 'nullable|exists:cursos,id',
            'fecha_matricula' => 'nullable|date',
            'estado' => 'required|string|in:activo,inactivo,egresado',
            'observaciones' => 'nullable|string',
        ]);

        Estudiante::create($data);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante registrado correctamente.');
    }

    /**
     * Display the specified student.
     */
    public function mostrar(Estudiante $estudiante): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'ver_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para ver estudiantes.');
        }

        return view('estudiantes.mostrar', [
            'estudiante' => $estudiante->load(['curso', 'usuario']),
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function editar(Estudiante $estudiante): View|RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'matricular_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para editar estudiantes.');
        }

        return view('estudiantes.editar', [
            'estudiante' => $estudiante,
            'cursos' => $this->cursosDisponibles(),
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function actualizar(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'matricular_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para actualizar estudiantes.');
        }

        $data = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100|unique:estudiantes,documento_identidad,' . $estudiante->id,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'curso_id' => 'nullable|exists:cursos,id',
            'fecha_matricula' => 'nullable|date',
            'estado' => 'required|string|in:activo,inactivo,egresado',
            'observaciones' => 'nullable|string',
        ]);

        $estudiante->update($data);

        return redirect()->route('estudiantes.mostrar', $estudiante)->with('success', 'Estudiante actualizado correctamente.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function eliminar(Estudiante $estudiante): RedirectResponse
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->hasAnyPermission(['gestionar_estudiantes', 'matricular_estudiantes'])) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para eliminar estudiantes.');
        }

        $estudiante->delete();

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante eliminado correctamente.');
    }

    /**
     * Obtener cursos ordenados para los formularios.
     */
    private function cursosDisponibles()
    {
        return Curso::orderBy('nombre')->get();
    }
}
