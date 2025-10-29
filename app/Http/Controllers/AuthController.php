<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\MatriculaAcudiente;
use App\Models\User;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Mostrar dashboard/menú principal
    public function dashboard()
    {
        $usuario = Auth::user();
        $rol = $usuario?->rol;

        $pendientesMatriculas = 0;

        if ($rol && $rol->nombre === 'Acudiente') {
            $pendientesMatriculas = MatriculaAcudiente::where('user_id', $usuario->id)
                ->where('estado', 'pendiente')
                ->count();
        }

        return view('dashboard', [
            'usuario' => $usuario,
            'rol' => $rol,
            'pendientesMatriculas' => $pendientesMatriculas,
        ]);
    }

    // Procesar logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
