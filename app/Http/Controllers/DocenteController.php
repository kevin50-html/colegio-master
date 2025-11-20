<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Docente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (!$this->puedeVerDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a docentes.');
        }

        $busqueda = trim((string) $request->input('q'));
        $estado = $request->input('estado', 'todos');
        $cursoId = $request->input('curso_id');

        $docentes = Docente::with('cursos')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('nombres', 'like', "%{$busqueda}%")
                        ->orWhere('apellidos', 'like', "%{$busqueda}%")
                        ->orWhere('documento_identidad', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->when($estado !== 'todos', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when(!empty($cursoId), function ($query) use ($cursoId) {
                $query->whereHas('cursos', function ($sub) use ($cursoId) {
                    $sub->where('cursos.id', $cursoId);
                });
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        $estadosDisponibles = [
            'todos' => 'Todos',
            'activo' => 'Activo',
            'inactivo' => 'Inactivo',
            'suspendido' => 'Suspendido',
        ];

        return view('docentes.index', [
            'docentes' => $docentes,
            'busqueda' => $busqueda,
            'estadoSeleccionado' => $estado,
            'cursoSeleccionado' => $cursoId,
            'cursos' => Curso::orderBy('nombre')->get(),
            'estadosDisponibles' => $estadosDisponibles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function crear(): View|RedirectResponse
    {
        if (!$this->puedeGestionarDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear docentes.');
        }

        return view('docentes.crear', [
            'cursos' => Curso::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function guardar(Request $request): RedirectResponse
    {
        if (!$this->puedeGestionarDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para crear docentes.');
        }

        $datos = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100|unique:docentes,documento_identidad',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'especialidad' => 'nullable|string|max:255',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|string|in:activo,inactivo,suspendido',
            'observaciones' => 'nullable|string',
            'cursos' => 'nullable|array',
            'cursos.*' => 'exists:cursos,id',
        ]);

        $cursos = $datos['cursos'] ?? [];
        unset($datos['cursos']);

        $docente = Docente::create($datos);
        $docente->cursos()->sync($cursos);

        return redirect()->route('docentes.index')->with('success', 'Docente registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function mostrar(Docente $docente): View|RedirectResponse
    {
        if (!$this->puedeVerDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para ver docentes.');
        }

        return view('docentes.mostrar', [
            'docente' => $docente->load('cursos'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editar(Docente $docente): View|RedirectResponse
    {
        if (!$this->puedeGestionarDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para editar docentes.');
        }

        return view('docentes.editar', [
            'docente' => $docente->load('cursos'),
            'cursos' => Curso::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function actualizar(Request $request, Docente $docente): RedirectResponse
    {
        if (!$this->puedeGestionarDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para actualizar docentes.');
        }

        $datos = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100|unique:docentes,documento_identidad,' . $docente->id,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'especialidad' => 'nullable|string|max:255',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|string|in:activo,inactivo,suspendido',
            'observaciones' => 'nullable|string',
            'cursos' => 'nullable|array',
            'cursos.*' => 'exists:cursos,id',
        ]);

        $cursos = $datos['cursos'] ?? [];
        unset($datos['cursos']);

        $docente->update($datos);
        $docente->cursos()->sync($cursos);

        return redirect()->route('docentes.mostrar', $docente)->with('success', 'Docente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function eliminar(Docente $docente): RedirectResponse
    {
        if (!$this->puedeGestionarDocentes()) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para eliminar docentes.');
        }

        $nombre = $docente->nombre_completo;
        $docente->delete();

        return redirect()->route('docentes.index')->with('success', "Docente {$nombre} eliminado correctamente.");
    }

    private function puedeVerDocentes(): bool
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return false;
        }

        return $usuario->hasAnyPermission([
            'gestionar_docentes',
            'ver_docentes',
            'acceso_total',
        ]);
    }

    private function puedeGestionarDocentes(): bool
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return false;
        }

        return $usuario->hasAnyPermission([
            'gestionar_docentes',
            'acceso_total',
        ]);
    }
}
