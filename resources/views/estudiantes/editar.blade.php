@extends('layouts.app')

@section('title', 'Editar Estudiante')

@section('content')
@php
    $menuActivo = 'estudiantes';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="mb-4">
                    <a href="{{ route('estudiantes.mostrar', $estudiante) }}" class="btn btn-link text-decoration-none px-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver al detalle
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Editar estudiante</h2>
                        <span class="badge bg-warning text-dark">Actualización de datos</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('estudiantes.actualizar', $estudiante) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $estudiante->nombres) }}" class="form-control @error('nombres') is-invalid @enderror" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $estudiante->apellidos) }}" class="form-control @error('apellidos') is-invalid @enderror" required>
                                @error('apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="documento_identidad" class="form-label">Documento de identidad</label>
                                <input type="text" name="documento_identidad" id="documento_identidad" value="{{ old('documento_identidad', $estudiante->documento_identidad) }}" class="form-control @error('documento_identidad') is-invalid @enderror" required>
                                @error('documento_identidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="curso_id" class="form-label">Curso asignado</label>
                                <select name="curso_id" id="curso_id" class="form-select @error('curso_id') is-invalid @enderror">
                                    <option value="">Selecciona un curso</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" @selected(old('curso_id', $estudiante->curso_id) == $curso->id)>{{ $curso->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('curso_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $estudiante->email) }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $estudiante->telefono) }}" class="form-control @error('telefono') is-invalid @enderror">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_matricula" class="form-label">Fecha de matrícula</label>
                                <input type="date" name="fecha_matricula" id="fecha_matricula" value="{{ old('fecha_matricula', optional($estudiante->fecha_matricula)->format('Y-m-d')) }}" class="form-control @error('fecha_matricula') is-invalid @enderror">
                                @error('fecha_matricula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                                    <option value="activo" @selected(old('estado', $estudiante->estado) === 'activo')>Activo</option>
                                    <option value="inactivo" @selected(old('estado', $estudiante->estado) === 'inactivo')>Inactivo</option>
                                    <option value="egresado" @selected(old('estado', $estudiante->estado) === 'egresado')>Egresado</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" rows="3" class="form-control @error('observaciones') is-invalid @enderror" placeholder="Actualiza la información relevante">{{ old('observaciones', $estudiante->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <a href="{{ route('estudiantes.mostrar', $estudiante) }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Actualizar estudiante
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
