@extends('layouts.app')

@section('title', 'Actividades - ' . $periodo->nombre)

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
                        <a href="{{ route('academico.curso-materias.periodos.show', [$periodo->cursoMateria, $periodo]) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver al periodo
                        </a>
                        <h1 class="h3 mb-1">Actividades - {{ $periodo->nombre }}</h1>
                        <p class="text-muted mb-0">Curso: {{ $periodo->cursoMateria->curso->nombre }} · Materia: {{ $periodo->cursoMateria->materia->nombre }}</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                        <a href="{{ route('academico.periodos.actividades.create', $periodo) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nueva actividad
                        </a>
                    @endif
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($actividades as $actividad)
                                <a href="{{ route('academico.periodos.actividades.show', [$periodo, $actividad]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $actividad->titulo }}</div>
                                        <div class="text-muted small">Entrega: {{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }} · Porcentaje: {{ $actividad->porcentaje ?? '—' }}%</div>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fw-normal">{{ $actividad->notas_count }} notas</span>
                                </a>
                            @empty
                                <div class="list-group-item text-muted text-center">No hay actividades registradas para este periodo.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
