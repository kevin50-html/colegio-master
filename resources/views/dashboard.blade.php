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
        $fecha = now()->format('d \d\e F, Y');
        $financeMetrics = [
            [
                'label' => 'Total Cuentas',
                'value' => '0',
                'icon' => 'fa-file-invoice',
                'color' => 'primary',
                'description' => 'Cuentas registradas'
            ],
            [
                'label' => 'Pagadas',
                'value' => '0',
                'icon' => 'fa-check-circle',
                'color' => 'success',
                'description' => 'Cuentas pagadas'
            ],
            [
                'label' => 'Pendientes',
                'value' => '0',
                'icon' => 'fa-clock',
                'color' => 'warning',
                'description' => 'Por cobrar'
            ],
            [
                'label' => 'Total Facturado (mes)',
                'value' => '$0',
                'icon' => 'fa-dollar-sign',
                'color' => 'info',
                'description' => 'Actualizado al día'
            ],
        ];

        $academicStats = [
            ['label' => 'Estudiantes activos', 'value' => 0, 'icon' => 'fa-user-graduate'],
            ['label' => 'Docentes activos', 'value' => 0, 'icon' => 'fa-chalkboard-teacher'],
            ['label' => 'Cursos en ejecución', 'value' => 0, 'icon' => 'fa-layer-group'],
        ];

        $upcomingItems = [
            ['title' => 'Revisión de matrículas', 'time' => 'Próxima semana', 'icon' => 'fa-folder-open'],
            ['title' => 'Generar reportes académicos', 'time' => '15 días', 'icon' => 'fa-chart-line'],
            ['title' => 'Reunión general de docentes', 'time' => 'Fin de mes', 'icon' => 'fa-users'],
        ];
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
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div class="p-4 rounded-3 bg-primary text-white shadow-sm h-100 d-flex flex-column justify-content-center">
                            <h1 class="h3 mb-2">Hola, {{ $usuario?->name }} 👋</h1>
                            <p class="mb-1">Hoy es {{ $fecha }}. Este es el estado general de tu colegio.</p>
                            <p class="mb-0 opacity-75">Mantente al día con las matrículas, docentes y reportes desde esta vista principal.</p>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted mb-1">Resumen Académico</p>
                                <div class="d-flex flex-column gap-3">
                                    @foreach($academicStats as $stat)
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="fw-semibold">{{ $stat['label'] }}</span>
                                                <div class="progress progress-thin mt-2" style="height: 4px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 35%"></div>
                                                </div>
                                            </div>
                                            <span class="badge bg-light text-dark fs-5">
                                                <i class="fas {{ $stat['icon'] }} me-1 text-primary"></i>{{ $stat['value'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finance snapshot -->
                <div class="row g-3 mb-4">
                    @foreach($financeMetrics as $metric)
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="icon-circle bg-{{ $metric['color'] }} bg-opacity-10 text-{{ $metric['color'] }}">
                                            <i class="fas {{ $metric['icon'] }}"></i>
                                        </div>
                                        <small class="text-muted">{{ $metric['description'] }}</small>
                                    </div>
                                    <p class="text-muted small mb-1">{{ $metric['label'] }}</p>
                                    <h3 class="fw-bold text-{{ $metric['color'] }}">{{ $metric['value'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                                    <small class="text-muted">Accede rápidamente a las tareas más frecuentes</small>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary"><i class="fas fa-rocket me-1"></i>Productividad</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if($rol && $rol->nombre === 'Acudiente')
                                        <div class="col-md-6">
                                            <a href="{{ route('matriculas.crear') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-primary text-white">
                                                    <i class="fas fa-file-upload"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Nueva Matrícula</h6>
                                                    <small class="text-muted">Crea una solicitud para tus acudidos</small>
                                                </div>
                                            </a>
                                        </div>
                                    @elseif($rol && ($rol->tienePermiso('gestionar_estudiantes') || $rol->tienePermiso('matricular_estudiantes') || $rol->tienePermiso('acceso_total')))
                                        <div class="col-md-6">
                                            <a href="{{ route('matriculas.crear') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-primary text-white">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Registrar Matrícula</h6>
                                                    <small class="text-muted">Inscribe estudiantes y asigna cursos</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if($rol && ($rol->tienePermiso('gestionar_estudiantes') || $rol->tienePermiso('acceso_total')))
                                        <div class="col-md-6">
                                            <a href="{{ route('estudiantes.crear') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-success text-white">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Nuevo Estudiante</h6>
                                                    <small class="text-muted">Registra información personal y académica</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if($rol && ($rol->tienePermiso('gestionar_docentes') || $rol->tienePermiso('acceso_total')))
                                        <div class="col-md-6">
                                            <a href="{{ route('docentes.crear') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-warning text-dark">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Nuevo Docente</h6>
                                                    <small class="text-muted">Integra docentes a tu plantilla</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if($rol && ($rol->tienePermiso('gestionar_cursos') || $rol->tienePermiso('acceso_total')))
                                        <div class="col-md-6">
                                            <a href="{{ route('cursos.index') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-info text-white">
                                                    <i class="fas fa-layer-group"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Gestionar Cursos</h6>
                                                    <small class="text-muted">Actualiza cupos, horarios y aulas</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if($rol && ($rol->tienePermiso('gestionar_materias') || $rol->tienePermiso('ver_materias')|| $rol->tienePermiso('acceso_total')))
                                        <div class="col-md-6">
                                            <a href="{{ route('materias.index') }}" class="quick-action-card">
                                                <div class="icon-wrapper bg-secondary text-white">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Materias</h6>
                                                    <small class="text-muted">Define asignaturas y contenidos</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if($rol && $rol->tienePermiso('ver_reportes_generales'))
                                        <div class="col-md-6">
                                            <a href="#" class="quick-action-card">
                                                <div class="icon-wrapper bg-dark text-white">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Ver Reportes</h6>
                                                    <small class="text-muted">Analiza indicadores académicos y financieros</small>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Próximos pasos</h5>
                                <small class="text-muted">Elementos que requieren tu atención</small>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($upcomingItems as $item)
                                        <li class="list-group-item px-0 d-flex align-items-center">
                                            <span class="icon-circle bg-light text-primary me-3">
                                                <i class="fas {{ $item['icon'] }}"></i>
                                            </span>
                                            <div>
                                                <strong>{{ $item['title'] }}</strong>
                                                <p class="mb-0 text-muted small">{{ $item['time'] }}</p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity + Modules -->
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Actividad reciente</h5>
                                    <small class="text-muted">Últimos movimientos registrados</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" type="button">Ver todo</button>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aún no registras actividades. Cuando generes matrículas, docentes o reportes aparecerán aquí.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Estado de módulos</h5>
                                <small class="text-muted">Monitorea qué áreas requieren configuración</small>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span><i class="fas fa-user-graduate text-primary me-2"></i>Estudiantes</span>
                                    <span class="badge bg-light text-dark">Configurar</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span><i class="fas fa-layer-group text-success me-2"></i>Cursos</span>
                                    <span class="badge bg-success bg-opacity-10 text-success">Al día</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span><i class="fas fa-book text-info me-2"></i>Materias</span>
                                    <span class="badge bg-info bg-opacity-10 text-info">Pendiente</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chalkboard-teacher text-warning me-2"></i>Docentes</span>
                                    <span class="badge bg-warning bg-opacity-25 text-warning">Sin asignar</span>
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

<style>
    body {
        background-color: #f4f6f9;
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .quick-action-card {
        border: 1px solid #f1f1f1;
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: inherit;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .quick-action-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-4px);
    }

    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .progress-thin {
        background-color: #f0f0f0;
        border-radius: 12px;
    }
</style>
@endsection
