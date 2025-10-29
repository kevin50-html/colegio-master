@extends('layouts.app')

@section('title', 'Nueva actividad - ' . $periodo->nombre)

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
                    <a href="{{ route('academico.periodos.actividades.index', $periodo) }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver a actividades
                    </a>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Registrar actividad para {{ $periodo->nombre }}</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.periodos.actividades.store', $periodo) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-8">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_entrega" class="form-label">Fecha de entrega</label>
                                <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control @error('fecha_entrega') is-invalid @enderror" value="{{ old('fecha_entrega') }}">
                                @error('fecha_entrega')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="porcentaje" class="form-label">Porcentaje</label>
                                <input type="number" name="porcentaje" id="porcentaje" min="1" max="100" class="form-control @error('porcentaje') is-invalid @enderror" value="{{ old('porcentaje') }}">
                                @error('porcentaje')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="4" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Instrucciones, objetivos, criterios...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar actividad
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
