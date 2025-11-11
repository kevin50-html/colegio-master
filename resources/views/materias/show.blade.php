@extends('layouts.app')

@section('title', $materia->nombre)

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
                        <h1 class="h3 mb-1">{{ $materia->nombre }}</h1>
                        <p class="text-muted mb-0">Información detallada de la materia.</p>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                        <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <a href="{{ route('materias.edit', $materia) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <form action="{{ route('materias.destroy', $materia) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar la materia {{ $materia->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Código</h6>
                                <p class="fs-5 fw-semibold mb-0">{{ $materia->codigo }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Intensidad</h6>
                                <p class="fs-5 fw-semibold mb-0">{{ $materia->intensidad_horaria }} horas por semana</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Creada</h6>
                                <p class="mb-0">{{ $materia->created_at?->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">Última actualización: {{ $materia->updated_at?->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Descripción</h6>
                                <p class="mb-0">{{ $materia->descripcion ?: 'Sin descripción registrada.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
