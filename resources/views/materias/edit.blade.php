@extends('layouts.app')

@section('title', 'Editar materia')

@section('content')
@php
    $menuActivo = 'materias';
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
                        <h1 class="h3 mb-1">Editar materia</h1>
                        <p class="text-muted mb-0">Actualiza la información de la materia.</p>
                    </div>
                    <a href="{{ route('materias.show', $materia) }}" class="btn btn-outline-secondary mt-3 mt-md-0">
                        <i class="fas fa-arrow-left me-1"></i> Volver a la materia
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('materias.update', $materia) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $materia->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $materia->codigo) }}" class="form-control @error('codigo') is-invalid @enderror" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="intensidad_horaria" class="form-label">Intensidad (h/sem)</label>
                                <input type="number" name="intensidad_horaria" id="intensidad_horaria" value="{{ old('intensidad_horaria', $materia->intensidad_horaria) }}" class="form-control @error('intensidad_horaria') is-invalid @enderror" min="1" max="40" required>
                                @error('intensidad_horaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Opcional">{{ old('descripcion', $materia->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-end">
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
