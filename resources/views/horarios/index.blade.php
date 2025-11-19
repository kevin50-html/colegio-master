@extends('layouts.app')

@section('title', 'Horarios por curso')

@php
    $menuActivo = 'horarios';
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
                            <h1 class="h3 mb-1">Horarios</h1>
                            <p class="text-muted mb-0">Selecciona un curso para crear o consultar sus bloques horarios.</p>
                        </div>
                        <form method="GET" action="{{ route('horarios.index') }}" class="d-flex gap-2">
                            <input type="search" name="buscar" class="form-control" placeholder="Buscar curso" value="{{ $busqueda }}" aria-label="Buscar curso">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            @if($cursos->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-clock fa-2x mb-3"></i>
                                    <p class="mb-0">No hay cursos disponibles para configurar horarios.</p>
                                </div>
                            @else
                                <div class="row g-3">
                                    @foreach($cursos as $curso)
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card border-0 h-100 shadow-sm">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <h5 class="fw-semibold mb-0">{{ $curso->nombre }}</h5>
                                                            <small class="text-muted">ID #{{ $curso->id }}</small>
                                                        </div>
                                                        <span class="badge bg-primary-subtle text-primary">
                                                            {{ $curso->horarios_count ?? 0 }} bloques
                                                        </span>
                                                    </div>
                                                    <ul class="list-unstyled text-muted small mb-4">
                                                        <li><i class="fas fa-book me-2 text-primary"></i>{{ $curso->materias_count }} materias</li>
                                                        <li><i class="fas fa-user-graduate me-2 text-primary"></i>{{ $curso->estudiantes_count }} estudiantes</li>
                                                    </ul>
                                                    <div class="mt-auto">
                                                        <a href="{{ route('horarios.curso', $curso) }}" class="btn btn-primary w-100">
                                                            <i class="fas fa-arrow-right me-1"></i> Gestionar horario
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    {{ $cursos->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
