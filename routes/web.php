<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MatriculaAcudienteController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PeriodoController;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Crear usuarios
Route::get('/register', [CrearUsuario::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CrearUsuario::class, 'register']);


// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Rutas de gestión de usuarios
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/crear', [UsuarioController::class, 'crear'])->name('crear');
        Route::post('/', [UsuarioController::class, 'guardar'])->name('guardar');
        Route::get('/{usuario}', [UsuarioController::class, 'mostrar'])->name('mostrar');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'editar'])->name('editar');
        Route::put('/{usuario}', [UsuarioController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{usuario}', [UsuarioController::class, 'eliminar'])->name('eliminar');
    });

    // Rutas de gestión de estudiantes
    Route::prefix('estudiantes')->name('estudiantes.')->group(function () {
        Route::get('/', [EstudianteController::class, 'index'])->name('index');
        Route::get('/crear', [EstudianteController::class, 'crear'])->name('crear');
        Route::post('/', [EstudianteController::class, 'guardar'])->name('guardar');
        Route::get('/{estudiante}', [EstudianteController::class, 'mostrar'])->name('mostrar');
        Route::get('/{estudiante}/editar', [EstudianteController::class, 'editar'])->name('editar');
        Route::put('/{estudiante}', [EstudianteController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{estudiante}', [EstudianteController::class, 'eliminar'])->name('eliminar');
    });

    // Rutas de gestión de docentes
    Route::prefix('docentes')->name('docentes.')->group(function () {
        Route::get('/', [DocenteController::class, 'index'])->name('index');
        Route::get('/crear', [DocenteController::class, 'crear'])->name('crear');
        Route::post('/', [DocenteController::class, 'guardar'])->name('guardar');
        Route::get('/{docente}', [DocenteController::class, 'mostrar'])->name('mostrar');
        Route::get('/{docente}/editar', [DocenteController::class, 'editar'])->name('editar');
        Route::put('/{docente}', [DocenteController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{docente}', [DocenteController::class, 'eliminar'])->name('eliminar');
    });

    // Rutas de gestión de roles
    Route::prefix('roles')->name('roles.')->group(function () {
        // Rutas web principales
        Route::get('/', [RolController::class, 'index'])->name('index');
        Route::get('/crear', [RolController::class, 'crear'])->name('crear');
        Route::post('/', [RolController::class, 'guardar'])->name('guardar');
        Route::get('/{rol}', [RolController::class, 'mostrar'])->name('mostrar');
        Route::get('/{rol}/editar', [RolController::class, 'editar'])->name('editar');
        Route::put('/{rol}', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{rol}', [RolController::class, 'eliminar'])->name('eliminar');
        
        // Rutas AJAX para gestión de roles
        Route::post('/asignar-rol', [RolController::class, 'asignarRol'])->name('asignar');
        Route::post('/remover-rol', [RolController::class, 'removerRol'])->name('remover');
        Route::get('/usuarios-sin-rol', [RolController::class, 'obtenerUsuariosSinRol'])->name('usuarios-sin-rol');
        Route::get('/roles-sistema', [RolController::class, 'obtenerRolesSistema'])->name('roles-sistema');
    });

    // Rutas de matrículas por acudientes
    Route::prefix('matriculas')->name('matriculas.')->group(function () {
        Route::get('/', [MatriculaAcudienteController::class, 'listar'])->name('index');
        Route::get('/crear', [MatriculaAcudienteController::class, 'crear'])->name('crear');
        Route::post('/guardar', [MatriculaAcudienteController::class, 'guardar'])->name('guardar');
        Route::get('/{matricula}', [MatriculaAcudienteController::class, 'mostrar'])->name('mostrar');
        Route::get('/descargar/{ruta}', [MatriculaAcudienteController::class, 'descargarDocumento'])->name('descargar');
        Route::patch('/{matricula}/estado', [MatriculaAcudienteController::class, 'actualizarEstado'])->name('actualizarEstado');
    });

    // Rutas de gestión académica (cursos → materias → periodos → horarios → actividades → notas)
    Route::prefix('academico')->name('academico.')->group(function () {
        Route::resource('cursos', CursoController::class);
        Route::resource('cursos.materias', MateriaController::class);
        Route::resource('materias.periodos', PeriodoController::class);
        Route::resource('periodos.horarios', HorarioController::class);
        Route::resource('horarios.actividades', ActividadController::class);
        Route::resource('actividades.notas', NotaController::class)->except(['show']);
    });
});
