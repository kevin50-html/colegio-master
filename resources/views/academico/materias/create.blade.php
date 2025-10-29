@extends('layouts.app')

@section('title', 'Nueva Materia')

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
                <div class="mb-4">
                    <a href="{{ route('academico.materias.index') }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver a materias
                    </a>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Registrar nueva materia</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.materias.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" name="codigo" id="codigo" class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}">
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="intensidad_horaria" class="form-label">Intensidad (h/sem)</label>
                                <input type="number" name="intensidad_horaria" id="intensidad_horaria" min="1" max="40" class="form-control @error('intensidad_horaria') is-invalid @enderror" value="{{ old('intensidad_horaria') }}">
                                @error('intensidad_horaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Objetivos, competencias, etc.">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar
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
