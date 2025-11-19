@extends('layouts.app')

@section('title', 'Notas del curso ' . $curso->nombre)

@php
    $menuActivo = 'notas';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                @include('partials.sidebar', ['menuActivo' => $menuActivo])
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h1 class="h3 mb-1">{{ $curso->nombre }}</h1>
                            <p class="text-muted mb-0">Resumen del curso y sus materias para registrar calificaciones.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a cursos
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Estudiantes</p>
                                    <h4 class="fw-bold mb-0">{{ $curso->estudiantes->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Docentes</p>
                                    <h4 class="fw-bold mb-0">{{ $curso->docentes->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Materias vinculadas</p>
                                    <h4 class="fw-bold mb-0">{{ $curso->materias->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-0">Materias del curso</h5>
                                    <small class="text-muted">Consulta los periodos y actividades definidos para cada materia.</small>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">{{ $curso->materias->count() }} en total</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($curso->materias->isEmpty())
                                <div class="alert alert-info mb-0">
                                    Este curso aún no tiene materias asociadas.
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($curso->materias as $materia)
                                        @php
                                            $totalActividades = $materia->periodos->sum(fn($periodo) => $periodo->actividades->count());
                                        @endphp
                                        <div class="col-12 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <h5 class="fw-semibold mb-0">{{ $materia->nombre }}</h5>
                                                            <small class="text-muted">{{ $materia->pivot->alias ?? 'Sin alias' }}</small>
                                                        </div>
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $totalActividades }} actividades</span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <p class="text-muted text-uppercase small mb-2">Periodos</p>
                                                        @if($materia->periodos->isEmpty())
                                                            <p class="text-muted small mb-0">Sin periodos configurados.</p>
                                                        @else
                                                            <ul class="list-group list-group-flush">
                                                                @foreach($materia->periodos as $periodo)
                                                                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                                        <span>{{ $periodo->nombre }}</span>
                                                                        <span class="badge bg-light text-muted">{{ $periodo->actividades->count() }} act.</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <div class="mt-auto">
                                                        <a href="{{ route('notas.materia', [$curso, $materia]) }}" class="btn btn-primary w-100">
                                                            <i class="fas fa-clipboard-check me-1"></i> Calificar materia
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
