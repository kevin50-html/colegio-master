@extends('layouts.app')

@section('title', 'Materias por curso')

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
                        <h1 class="h3 mb-1">Materias por curso</h1>
                        <p class="text-muted mb-0">Selecciona un curso para administrar las materias asociadas.</p>
                    </div>
                    <a href="{{ route('curso-materias.create', ['curso' => $selectedCurso?->id]) }}" class="btn btn-primary mt-3 mt-md-0 {{ $selectedCurso ? '' : 'disabled' }}">
                        <i class="fas fa-plus me-1"></i> Asignar materia
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('curso-materias.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="curso" class="form-label">Curso</label>
                        <select name="curso" id="curso" class="form-select">
                            <option value="">Selecciona un curso</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" {{ $selectedCurso && $selectedCurso->id === $curso->id ? 'selected' : '' }}>
                                    {{ $curso->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <a href="{{ route('curso-materias.index') }}" class="btn btn-link text-decoration-none">
                            Limpiar
                        </a>
                    </div>
                </form>

                @if($selectedCurso)
                    @php
                        $ultimaActualizacion = $asignaciones->pluck('updated_at')->filter()->max();
                    @endphp

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                <div>
                                    <h2 class="h5 mb-1">{{ $selectedCurso->nombre }}</h2>
                                    <p class="text-muted mb-0">{{ $asignaciones->count() }} materias asignadas.</p>
                                </div>
                                <div class="text-lg-end">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>
                                        Última actualización: {{ $ultimaActualizacion ? $ultimaActualizacion->format('d/m/Y') : 'Sin datos' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Materia</th>
                                            <th>Alias</th>
                                            <th>Asignada el</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($asignaciones as $asignacion)
                                            <tr>
                                                <td class="fw-semibold">{{ $asignacion->materia->nombre }}</td>
                                                <td>{{ $asignacion->alias ? $asignacion->alias : 'Sin alias' }}</td>
                                                <td>{{ optional($asignacion->created_at)->format('d/m/Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('curso-materias.show', $asignacion) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('curso-materias.edit', $asignacion) }}" class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('curso-materias.destroy', $asignacion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la materia {{ $asignacion->materia->nombre }} del curso {{ $selectedCurso->nombre }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">El curso no tiene materias asignadas todavía.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Selecciona un curso para ver las materias asociadas y gestionar nuevas asignaciones.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
