@extends('layouts.app')

@section('title', 'Dashboard - Colegio')

@section('content')
@php
    $usuario = $usuario ?? Auth::user();
    $rol = $rol ?? ($usuario ? $usuario->rol : null);
    $pendientesMatriculas = $pendientesMatriculas ?? 0;
@endphp
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-file-invoice-dollar me-2"></i>Colegio
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @if($rol && $rol->nombre === 'Acudiente')
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="matriculasDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-folder me-1"></i>Matrículas
                        @if($pendientesMatriculas > 0)
                            <span class="badge rounded-pill bg-warning text-dark ms-1">{{ $pendientesMatriculas }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="matriculasDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('matriculas.index') }}">
                                <i class="fas fa-list me-1"></i>Mis Matrículas
                            @if($pendientesMatriculas > 0)<span class="badge bg-warning text-dark ms-1">{{ $pendientesMatriculas }}</span>@endif</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('matriculas.crear') }}">
                                <i class="fas fa-file-upload me-1"></i>Nueva Matrícula
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ $usuario?->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-cog me-1"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-1"></i>Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    @php
        $menuActivo = 'dashboard';
    @endphp
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo, 'rolActual' => $rol])
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 text-dark">¡Bienvenido, {{ $usuario?->name }}!</h1>
                        <p class="text-muted">Gestiona tus Colegio de manera eficiente</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-file-invoice fa-2x"></i>
                                </div>
                                <h5 class="card-title">Total Cuentas</h5>
                                <h3 class="text-primary">0</h3>
                                <small class="text-muted">Cuentas registradas</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h5 class="card-title">Pagadas</h5>
                                <h3 class="text-success">0</h3>
                                <small class="text-muted">Cuentas pagadas</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-warning mb-2">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h5 class="card-title">Pendientes</h5>
                                <h3 class="text-warning">0</h3>
                                <small class="text-muted">Por cobrar</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-info mb-2">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                                <h5 class="card-title">Total Facturado</h5>
                                <h3 class="text-info">$0</h3>
                                <small class="text-muted">Este mes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-rocket me-2"></i>Acciones Rápidas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($rol && $rol->nombre === 'Acudiente')
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ route('matriculas.crear') }}" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-file-upload me-2"></i>
                                            Nueva Matrícula
                                        </a>
                                    </div>
                                    @elseif($rol && ($rol->tienePermiso('gestionar_estudiantes') || $rol->tienePermiso('matricular_estudiantes') || $rol->tienePermiso('acceso_total')))
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ route('matriculas.crear') }}" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-user-check me-2"></i>
                                            Registrar Matrícula
                                        </a>
                                    </div>
                                    @endif
                                    @if($rol && ($rol->tienePermiso('gestionar_estudiantes') || $rol->tienePermiso('acceso_total')))
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ route('estudiantes.crear') }}" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-user-plus me-2"></i>
                                            Nuevo Estudiante
                                        </a>
                                    </div>
                                    @endif
                                    @if($rol && ($rol->tienePermiso('gestionar_docentes') || $rol->tienePermiso('acceso_total')))
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ route('docentes.crear') }}" class="btn btn-outline-primary btn-lg w-100">
                                            <i class="fas fa-chalkboard-teacher me-2"></i>
                                            Nuevo Docente
                                        </a>
                                    </div>
                                    @endif
                                    @if($rol && ($rol->tienePermiso('gestionar_cursos') || $rol->tienePermiso('gestionar_materias') || $rol->tienePermiso('gestionar_periodos') || $rol->tienePermiso('gestionar_horarios') || $rol->tienePermiso('crear_actividades') || $rol->tienePermiso('registrar_notas') || $rol->tienePermiso('acceso_total')))
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ route('academico.cursos.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                            <i class="fas fa-school me-2"></i>
                                            Gestión Académica
                                        </a>
                                    </div>
                                    @endif
                                    @if($rol && $rol->tienePermiso('ver_reportes_generales'))
                                    <div class="col-md-4 mb-3">
                                        <a href="#" class="btn btn-outline-primary btn-lg w-100">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Ver Reportes
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>Actividad Reciente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay actividad reciente para mostrar.</p>
                                    <p class="text-muted">¡Comienza creando tu primera cuenta de cobro!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection
