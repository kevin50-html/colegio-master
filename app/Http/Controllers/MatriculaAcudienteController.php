<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatriculaAcudiente;
use App\Models\RolesModel;
use App\Models\Estudiante;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatriculaAcudienteController extends Controller
{
    /**
     * Mostrar formulario de creación de matrícula.
     */
    public function crear(): View|RedirectResponse
    {
        $usuario = Auth::user();
        $rol = $usuario?->rol ?? ($usuario?->roles_id ? RolesModel::find($usuario->roles_id) : null);

        $puedeGestionar = $this->puedeGestionarMatrículas($usuario);
        $esAcudiente = $rol && $rol->nombre === 'Acudiente';

        if (!$esAcudiente && !$puedeGestionar) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $cursos = Curso::orderBy('nombre')->get();

        return view('matriculas.crear', compact('cursos', 'esAcudiente'));
    }

    /**
     * Guardar una matrícula y cargar sus documentos.
     */
    public function guardar(Request $request): RedirectResponse
    {
        $usuario = Auth::user();
        $rol = $usuario?->rol ?? ($usuario?->roles_id ? RolesModel::find($usuario->roles_id) : null);

        $puedeGestionar = $this->puedeGestionarMatrículas($usuario);
        $esAcudiente = $rol && $rol->nombre === 'Acudiente';

        if (!$esAcudiente && !$puedeGestionar) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:100',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:50',
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $rutasDocumentos = [];
        $disk = config('filesystems.upload_disk', 'ftp');
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $ruta = 'matriculas/' . date('Y/m') . '/' . $usuario->id . '/' . $nombre . '-' . time() . '.' . $ext;
                $saved = Storage::disk($disk)->put($ruta, fopen($file->getRealPath(), 'r+'));
                if ($saved) {
                    $rutasDocumentos[] = $ruta;
                }
            }
        }

        MatriculaAcudiente::create([
            'user_id' => $usuario->id,
            'curso_id' => $validated['curso_id'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'documento_identidad' => $validated['documento_identidad'],
            'email' => $validated['email'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'documentos' => $rutasDocumentos,
            'estado' => 'pendiente',
        ]);

        $ruta = $puedeGestionar ? 'matriculas.index' : 'matriculas.crear';

        return redirect()->route($ruta)->with('success', 'Matrícula registrada correctamente.');
    }

    /**
     * Listado de matrículas.
     */
    public function listar(Request $request): View|RedirectResponse
    {
        $usuario = Auth::user();

        if ($this->puedeGestionarMatrículas($usuario)) {
            $moduloEstudiantesListo = $this->moduloEstudiantesListo();

            $busqueda = trim((string) $request->input('q'));
            $estado = $request->input('estado', 'todos');

            $matriculasQuery = MatriculaAcudiente::with(['curso', 'acudiente']);

            if ($moduloEstudiantesListo) {
                $matriculasQuery->with('estudianteRegistro');
            }

            $matriculas = $matriculasQuery
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
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();

            $estadosDisponibles = [
                'todos' => 'Todas',
                'pendiente' => 'Pendientes',
                'aprobada' => 'Aprobadas',
                'rechazada' => 'Rechazadas',
            ];

            return view('matriculas.gestionar', [
                'matriculas' => $matriculas,
                'busqueda' => $busqueda,
                'estadoSeleccionado' => $estado,
                'estadosDisponibles' => $estadosDisponibles,
                'moduloEstudiantesListo' => $moduloEstudiantesListo,
            ]);
        }

        $rol = $usuario?->rol ?? ($usuario?->roles_id ? RolesModel::find($usuario->roles_id) : null);
        if ($rol && $rol->nombre === 'Acudiente') {
            $matriculas = MatriculaAcudiente::where('user_id', $usuario->id)
                ->orderByDesc('created_at')
                ->paginate(10);

            return view('matriculas.index', compact('matriculas'));
        }

        return redirect()->route('dashboard')->with('error', 'No tienes permisos para ver las matrículas.');
    }

    /**
     * Mostrar detalle de una matrícula.
     */
    public function mostrar(MatriculaAcudiente $matricula): View|RedirectResponse
    {
        $usuario = Auth::user();

        $puedeGestionar = $this->puedeGestionarMatrículas($usuario);

        if ($matricula->user_id !== $usuario->id && !$puedeGestionar) {
            return redirect()->route('matriculas.index')->with('error', 'No autorizado.');
        }

        $moduloEstudiantesListo = $this->moduloEstudiantesListo();

        $relaciones = ['curso', 'acudiente'];

        if ($moduloEstudiantesListo) {
            $relaciones[] = 'estudianteRegistro';
        }

        return view('matriculas.mostrar', [
            'matricula' => $matricula->load($relaciones),
            'puedeGestionar' => $puedeGestionar,
            'cursos' => $puedeGestionar ? Curso::orderBy('nombre')->get() : collect(),
            'moduloEstudiantesListo' => $moduloEstudiantesListo,
        ]);
    }

    /**
     * Actualizar el estado de la matrícula (aprobar / rechazar).
     */
    public function actualizarEstado(Request $request, MatriculaAcudiente $matricula): RedirectResponse
    {
        $usuario = Auth::user();

        if (!$this->puedeGestionarMatrículas($usuario)) {
            return redirect()->route('matriculas.index')->with('error', 'No tienes permisos para actualizar matrículas.');
        }

        $data = $request->validate([
            'estado' => 'required|string|in:pendiente,aprobada,rechazada',
            'curso_id' => 'nullable|exists:cursos,id',
            'fecha_matricula' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $estado = $data['estado'];
        $cursoId = $data['curso_id'] ?? $matricula->curso_id;
        $fechaMatricula = $data['fecha_matricula'] ?? null;

        if ($estado === 'aprobada' && !$cursoId) {
            return back()->withInput()->with('error', 'Debes asignar un curso para aprobar la matrícula.');
        }

        if ($estado === 'aprobada') {
            if (!$this->moduloEstudiantesListo()) {
                return back()->with('error', 'Debes ejecutar las migraciones más recientes (php artisan migrate) antes de aprobar matrículas.');
            }

            $estudiante = Estudiante::updateOrCreate(
                ['documento_identidad' => $matricula->documento_identidad],
                [
                    'nombres' => $matricula->nombres,
                    'apellidos' => $matricula->apellidos,
                    'email' => $matricula->email,
                    'telefono' => $matricula->telefono,
                    'curso_id' => $cursoId,
                    'fecha_matricula' => $fechaMatricula ?: now()->toDateString(),
                    'estado' => 'activo',
                ]
            );

            if (!empty($data['observaciones'])) {
                $estudiante->observaciones = $data['observaciones'];
            }

            $estudiante->save();

            $matricula->estudiante_registro_id = $estudiante->id;
        }

        if ($estado !== 'aprobada') {
            $matricula->estudiante_registro_id = $estado === 'pendiente' ? $matricula->estudiante_registro_id : null;
        }

        if ($cursoId) {
            $matricula->curso_id = $cursoId;
        }

        $matricula->estado = $estado;
        $matricula->save();

        if (isset($estudiante)) {
            $estudiante->refresh();
        }

        return redirect()->route('matriculas.mostrar', $matricula)
            ->with('success', 'Estado de la matrícula actualizado correctamente.');
    }

    /**
     * Descargar un documento desde el almacenamiento configurado.
     */
    public function descargarDocumento($ruta)
    {
        $ruta = urldecode($ruta);
        $disk = config('filesystems.upload_disk', 'ftp');
        if (Storage::disk($disk)->exists($ruta)) {
            $stream = Storage::disk($disk)->readStream($ruta);
            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => Storage::disk($disk)->mimeType($ruta) ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . basename($ruta) . '"',
            ]);
        }
        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }

    private function puedeGestionarMatrículas($usuario): bool
    {
        return $usuario && $usuario->hasAnyPermission([
            'gestionar_estudiantes',
            'matricular_estudiantes',
        ]);
    }

    private function moduloEstudiantesListo(): bool
    {
        return Schema::hasTable('estudiantes')
            && Schema::hasColumn('matriculas_acudientes', 'estudiante_registro_id');
    }
}
