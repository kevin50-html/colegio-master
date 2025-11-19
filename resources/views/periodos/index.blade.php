@extends('layouts.app')

@section('title', 'Períodos por materia')

@php
    $menuActivo = 'periodos';
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
                            <h1 class="h3 mb-1">Períodos</h1>
                            <p class="text-muted mb-0">Selecciona una materia para crear y gestionar sus períodos.</p>
                        </div>
                        <form method="GET" action="{{ route('periodos.index') }}" class="d-flex gap-2">
                            <input type="search" name="buscar" class="form-control" placeholder="Buscar materia" value="{{ $busqueda }}" aria-label="Buscar materia">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    @if ($materias->isEmpty())
                        <div class="alert alert-info">No hay materias registradas.</div>
                    @else
                        <div class="row g-3">
                            @foreach ($materias as $materia)
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title mb-1">{{ $materia->nombre }}</h5>
                                            <p class="text-muted mb-2">Código: {{ $materia->codigo }}</p>
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge bg-primary-subtle text-primary me-2">{{ $materia->periodos_count }} períodos</span>
                                            </div>
                                            <div class="mt-auto">
                                                <a href="{{ route('periodos.materia', $materia) }}" class="btn btn-primary w-100">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    Gestionar períodos
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
@endsection
