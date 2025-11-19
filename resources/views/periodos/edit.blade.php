@extends('layouts.app')

@section('title', 'Editar período')

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
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">Editar período</h1>
                            <p class="text-muted mb-0">{{ $materia->nombre }}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('periodos.materia', $materia) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a períodos
                            </a>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('periodos.update', $periodo) }}" method="POST" class="row g-3">
                                @csrf
                                @method('PUT')

                                <div class="col-12">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $periodo->nombre) }}" class="form-control @error('nombre') is-invalid @enderror" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio', optional($periodo->fecha_inicio)->format('Y-m-d')) }}" class="form-control @error('fecha_inicio') is-invalid @enderror">
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="fecha_fin" class="form-label">Fecha fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin', optional($periodo->fecha_fin)->format('Y-m-d')) }}" class="form-control @error('fecha_fin') is-invalid @enderror">
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="orden" class="form-label">Orden</label>
                                    <input type="number" name="orden" id="orden" value="{{ old('orden', $periodo->orden) }}" class="form-control @error('orden') is-invalid @enderror" min="1">
                                    @error('orden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <a href="{{ route('periodos.materia', $materia) }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Actualizar período</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
