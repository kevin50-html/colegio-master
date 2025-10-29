@extends('layouts.app')

@section('title', 'Nuevo periodo - ' . $cursoMateria->curso->nombre . ' / ' . $cursoMateria->materia->nombre)

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
                    <a href="{{ route('academico.curso-materias.periodos.index', $cursoMateria) }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver a periodos
                    </a>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Crear periodo para {{ $cursoMateria->materia->nombre }} ({{ $cursoMateria->curso->nombre }})</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.curso-materias.periodos.store', $cursoMateria) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $ordenSugerido) }}" min="1">
                                @error('orden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Curso</label>
                                <input type="text" class="form-control" value="{{ $cursoMateria->curso->nombre }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}">
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_fin" class="form-label">Fecha de cierre</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar periodo
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
