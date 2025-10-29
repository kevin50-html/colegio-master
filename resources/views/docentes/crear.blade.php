@extends('layouts.app')

@section('title', 'Registrar Docente')

@section('content')
@php
    $menuActivo = 'docentes';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Registrar docente</h1>
                        <p class="text-muted mb-0">Crea el perfil de un nuevo docente y asígnalo a los cursos correspondientes.</p>
                    </div>
                    <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver al listado
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('docentes.guardar') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombres" class="form-label">Nombres</label>
                                    <input type="text" id="nombres" name="nombres" value="{{ old('nombres') }}" class="form-control @error('nombres') is-invalid @enderror" required>
                                    @error('nombres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" class="form-control @error('apellidos') is-invalid @enderror" required>
                                    @error('apellidos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="documento_identidad" class="form-label">Documento de identidad</label>
                                    <input type="text" id="documento_identidad" name="documento_identidad" value="{{ old('documento_identidad') }}" class="form-control @error('documento_identidad') is-invalid @enderror" required>
                                    @error('documento_identidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}" class="form-control @error('telefono') is-invalid @enderror">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="especialidad" class="form-label">Especialidad</label>
                                    <input type="text" id="especialidad" name="especialidad" value="{{ old('especialidad') }}" class="form-control @error('especialidad') is-invalid @enderror">
                                    @error('especialidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_ingreso" class="form-label">Fecha de ingreso</label>
                                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}" class="form-control @error('fecha_ingreso') is-invalid @enderror">
                                    @error('fecha_ingreso')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                        <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                                        <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                                        <option value="suspendido" @selected(old('estado') === 'suspendido')>Suspendido</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="cursos" class="form-label">Cursos asignados</label>
                                    <select id="cursos" name="cursos[]" class="form-select @error('cursos') is-invalid @enderror" multiple>
                                        @foreach($cursos as $curso)
                                            <option value="{{ $curso->id }}" @selected(collect(old('cursos', []))->contains($curso->id))>{{ $curso->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar varios cursos.</small>
                                    @error('cursos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('cursos.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea id="observaciones" name="observaciones" rows="4" class="form-control @error('observaciones') is-invalid @enderror" placeholder="Notas internas sobre el docente">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar docente
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
