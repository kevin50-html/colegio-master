@extends('layouts.app')

@section('title', 'Periodo: ' . $periodo->nombre)

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
                        <a href="{{ route('academico.curso-materias.periodos.index', $cursoMateria) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a periodos
                        </a>
                        <h1 class="h3 mb-1">{{ $periodo->nombre }}</h1>
                        <p class="text-muted mb-0">Curso: {{ $cursoMateria->curso->nombre }} · Materia: {{ $cursoMateria->materia->nombre }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                            <a href="{{ route('academico.curso-materias.periodos.edit', [$cursoMateria, $periodo]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar periodo
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['gestionar_horarios', 'acceso_total']))
                            <a href="{{ route('academico.curso-materias.horarios.index', $cursoMateria) }}" class="btn btn-outline-primary">
                                <i class="fas fa-clock me-1"></i>Horarios curso-materia
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Detalles</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Orden</dt>
                                    <dd class="col-sm-8">{{ $periodo->orden }}</dd>
                                    <dt class="col-sm-4">Inicio</dt>
                                    <dd class="col-sm-8">{{ optional($periodo->fecha_inicio)->format('Y-m-d') ?? '—' }}</dd>
                                    <dt class="col-sm-4">Fin</dt>
                                    <dd class="col-sm-8">{{ optional($periodo->fecha_fin)->format('Y-m-d') ?? '—' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Resumen</h2>
                                <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i>{{ $periodo->horarios->count() }} horarios relacionados</p>
                                <p class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i>{{ $periodo->actividades->count() }} actividades registradas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Actividades</h2>
                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                            <a href="{{ route('academico.periodos.actividades.create', $periodo) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva actividad
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($periodo->actividades as $actividad)
                                <a href="{{ route('academico.periodos.actividades.show', [$periodo, $actividad]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $actividad->titulo }}</div>
                                        <div class="text-muted small">Entrega: {{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }} · Porcentaje: {{ $actividad->porcentaje ?? '—' }}%</div>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fw-normal">{{ $actividad->notas->count() }} notas</span>
                                </a>
                            @empty
                                <div class="list-group-item text-muted text-center">No hay actividades registradas en este periodo.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
