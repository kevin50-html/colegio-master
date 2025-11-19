@extends('layouts.app')

@section('title', 'Actividades por materia')

@php
    $menuActivo = 'actividades';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
@endphp

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h1 class="h3 mb-1">Actividades</h1>
                <p class="text-muted mb-0">Selecciona una materia para gestionar sus tareas y actividades.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al dashboard
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Materias disponibles</h5>
                        <small class="text-muted">Elige una materia para ver o crear actividades.</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($materias->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-book-open fa-2x mb-3"></i>
                        <p class="mb-0">No hay materias registradas actualmente.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($materias as $materia)
                            <div class="col-12 col-md-6 col-lg-4">
                                <a href="{{ route('actividades.materia', $materia) }}" class="text-decoration-none text-dark">
                                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div>
                                                    <h6 class="text-uppercase text-muted small mb-2">Materia</h6>
                                                    <h5 class="fw-semibold mb-1">{{ $materia->nombre }}</h5>
                                                    <p class="text-muted small mb-2">Código: {{ $materia->codigo ?? 'N/D' }}</p>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        <i class="fas fa-tasks me-1"></i>
                                                        {{ $materia->actividades_count }} actividades
                                                    </span>
                                                </div>
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
