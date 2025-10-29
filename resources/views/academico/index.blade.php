@extends('layouts.app')

@section('title', 'Gestión Académica')

@section('content')
@php
    $menuActivo = 'academico';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Gestión Académica</h1>
                        <p class="text-muted mb-0">Accede a los módulos independientes para administrar cursos, materias, periodos y horarios.</p>
                    </div>
                    <a href="{{ route('academico.cursos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-stream me-1"></i>Ver flujo completo
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-primary mb-2"><i class="fas fa-school fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Cursos</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['cursos']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-success mb-2"><i class="fas fa-book-open fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Materias</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['materias']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-info mb-2"><i class="fas fa-project-diagram fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Asignaciones</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['asignaciones']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-warning mb-2"><i class="fas fa-calendar-alt fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Periodos</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['periodos']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-secondary mb-2"><i class="fas fa-clock fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Horarios</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['horarios']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-danger mb-2"><i class="fas fa-tasks fa-2x"></i></div>
                                <h6 class="text-muted text-uppercase small mb-1">Actividades</h6>
                                <h3 class="fw-semibold mb-0">{{ number_format($resumen['actividades']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-primary mb-2"><i class="fas fa-layer-group fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Módulo de Cursos</h5>
                                    <p class="text-muted small mb-0">Administra la oferta de cursos, su información y estados generales.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.cursos.index') }}" class="btn btn-primary w-100">Ir al módulo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-success mb-2"><i class="fas fa-book fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Módulo de Materias</h5>
                                    <p class="text-muted small mb-0">Consulta y gestiona materias de forma global filtrando por curso y código.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.modulos.materias') }}" class="btn btn-success w-100">Ver materias</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-warning mb-2"><i class="fas fa-calendar-check fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Módulo de Periodos</h5>
                                    <p class="text-muted small mb-0">Monitorea los periodos académicos y su relación con cursos y materias.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.modulos.periodos') }}" class="btn btn-warning w-100 text-white">Gestionar periodos</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-info mb-2"><i class="fas fa-business-time fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Módulo de Horarios</h5>
                                    <p class="text-muted small mb-0">Consulta los horarios planificados por día, curso, materia o periodo.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.modulos.horarios') }}" class="btn btn-info w-100 text-white">Revisar horarios</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-secondary mb-2"><i class="fas fa-project-diagram fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Cursos por Materias</h5>
                                    <p class="text-muted small mb-0">Analiza la relación entre cursos, materias, periodos y horarios en un solo resumen.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.modulos.cursos-materias') }}" class="btn btn-secondary w-100">Ver resumen</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <div class="text-danger mb-2"><i class="fas fa-tasks fa-2x"></i></div>
                                    <h5 class="card-title mb-1">Módulo de Actividades</h5>
                                    <p class="text-muted small mb-0">Consulta y gestiona actividades evaluativas filtrando por curso, materia o periodo.</p>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('academico.modulos.actividades') }}" class="btn btn-danger w-100">Gestionar actividades</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
