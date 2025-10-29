<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $busqueda = $request->input('q');

        $usuarios = User::with('rol')
            ->when($busqueda, function ($query, $busqueda) {
                $query->where(function ($inner) use ($busqueda) {
                    $inner->where('name', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
        ]);
    }

    public function crear()
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear usuarios.');
        }

        $roles = RolesModel::orderBy('nombre')->get();

        return view('usuarios.crear', compact('roles'));
    }

    public function guardar(Request $request)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear usuarios.');
        }

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles_id' => 'required|exists:roles,id',
        ], [
            'roles_id.required' => 'Selecciona un rol para el usuario.',
            'roles_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'roles_id' => $datos['roles_id'],
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function mostrar(User $usuario)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver este usuario.');
        }

        $usuario->load('rol');

        return view('usuarios.mostrar', compact('usuario'));
    }

    public function editar(User $usuario)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para editar usuarios.');
        }

        $roles = RolesModel::orderBy('nombre')->get();
        $usuario->load('rol');

        return view('usuarios.editar', compact('usuario', 'roles'));
    }

    public function actualizar(Request $request, User $usuario)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para actualizar usuarios.');
        }

        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'roles_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'roles_id.required' => 'Selecciona un rol para el usuario.',
            'roles_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->roles_id = $datos['roles_id'];

        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.mostrar', $usuario)->with('success', 'Usuario actualizado correctamente.');
    }

    public function eliminar(User $usuario)
    {
        if (!$this->puedeGestionarUsuarios()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para eliminar usuarios.');
        }

        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    private function puedeGestionarUsuarios(): bool
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->roles_id) {
            return false;
        }

        $rol = RolesModel::find($usuario->roles_id);

        return $rol?->tienePermiso('gestionar_usuarios') ?? false;
    }
}
