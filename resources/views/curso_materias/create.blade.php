@extends('layouts.app')

@section('title', 'Asignar materia a curso')

@section('content')
@php
    $menuActivo = 'curso-materias';
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
                        <h1 class="h3 mb-1">Asignar materia a un curso</h1>
                        <p class="text-muted mb-0">Selecciona el curso y la materia que deseas vincular.</p>
                    </div>
                    <a href="{{ route('curso-materias.index', ['curso' => $selectedCurso?->id]) }}" class="btn btn-outline-secondary mt-3 mt-md-0">
                        <i class="fas fa-arrow-left me-1"></i> Volver al listado
                    </a>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('curso-materias.create') }}" class="row g-2 align-items-end mb-4">
                            <div class="col-md-6">
                                <label for="curso" class="form-label">Curso</label>
                                <select name="curso" id="curso" class="form-select">
                                    <option value="">Selecciona un curso</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" {{ $selectedCurso && $selectedCurso->id === $curso->id ? 'selected' : '' }}>
                                            {{ $curso->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-check me-1"></i> Seleccionar curso
                                </button>
                                <a href="{{ route('curso-materias.create') }}" class="btn btn-link text-decoration-none">
                                    Limpiar
                                </a>
                            </div>
                        </form>

                        @if($selectedCurso)
                            <form action="{{ route('curso-materias.store') }}" method="POST" class="row g-3">
                                @csrf
                                <input type="hidden" name="curso_id" value="{{ $selectedCurso->id }}">

                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <strong>Curso seleccionado:</strong> {{ $selectedCurso->nombre }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="materia_id" class="form-label">Materia</label>
                                    <select name="materia_id" id="materia_id" class="form-select @error('materia_id') is-invalid @enderror" {{ $materiasDisponibles->isEmpty() ? 'disabled' : '' }} required>
                                        <option value="">Selecciona una materia</option>
                                        @foreach($materiasDisponibles as $materia)
                                            <option value="{{ $materia->id }}" {{ (int) old('materia_id') === $materia->id ? 'selected' : '' }}>
                                                {{ $materia->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('materia_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($materiasDisponibles->isEmpty())
                                        <div class="form-text text-danger">Este curso ya tiene todas las materias registradas asignadas.</div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="alias" class="form-label">Alias (opcional)</label>
                                    <input type="text" name="alias" id="alias" value="{{ old('alias') }}" class="form-control @error('alias') is-invalid @enderror" placeholder="Nombre alternativo para identificar la materia">
                                    @error('alias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" {{ $materiasDisponibles->isEmpty() ? 'disabled' : '' }}>
                                        <i class="fas fa-save me-1"></i> Guardar asignación
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                Debes seleccionar un curso para poder asignar una materia.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
