@extends('layouts.app')

@section('title', 'Editar Nota - ' . $actividad->titulo)

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
                    <a href="{{ route('academico.horarios.actividades.show', [$actividad->horario, $actividad]) }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la actividad
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Editar nota</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.actividades.notas.update', [$actividad, $nota]) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label class="form-label">Estudiante</label>
                                <input type="text" class="form-control" value="{{ $nota->estudiante?->nombre_completo ?? 'Estudiante eliminado' }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="valor" class="form-label">Calificación</label>
                                <input type="number" name="valor" id="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $nota->valor) }}" step="0.1" min="0" max="100">
                                @error('valor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" rows="3" class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $nota->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Actualizar
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
