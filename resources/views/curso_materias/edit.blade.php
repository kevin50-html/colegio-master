@extends('layouts.app')

@section('title', 'Editar asignación de materia')

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
                        <h1 class="h3 mb-1">Editar asignación</h1>
                        <p class="text-muted mb-0">Actualiza el alias de la materia dentro del curso.</p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('curso-materias.show', $cursoMateria) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i> Ver detalle
                        </a>
                        <a href="{{ route('curso-materias.index', ['curso' => $cursoMateria->curso_id]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <span class="text-muted text-uppercase small">Curso</span>
                                <p class="mb-0 fw-semibold">{{ $cursoMateria->curso->nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted text-uppercase small">Materia</span>
                                <p class="mb-0 fw-semibold">{{ $cursoMateria->materia->nombre }}</p>
                                <span class="text-muted small">Código: {{ $cursoMateria->materia->codigo ?? 'N/D' }}</span>
                            </div>
                        </div>

                        <form action="{{ route('curso-materias.update', $cursoMateria) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label for="alias" class="form-label">Alias (opcional)</label>
                                <input type="text" name="alias" id="alias" value="{{ old('alias', $cursoMateria->alias) }}" class="form-control @error('alias') is-invalid @enderror" placeholder="Nombre alternativo para identificar la materia">
                                @error('alias')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 d-flex align-items-end justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
