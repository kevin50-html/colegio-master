@extends('layouts.app')

@section('title', 'Cursos')

@section('content')
@php
    $menuActivo = 'cursos';
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
                        <h1 class="h3 mb-1">Cursos</h1>
                        <p class="text-muted mb-0">Administra los cursos disponibles en el colegio.</p>
                    </div>
                    <a href="{{ route('cursos.create') }}" class="btn btn-primary mt-3 mt-md-0">
                        <i class="fas fa-plus me-1"></i> Nuevo curso
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('cursos.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="buscar" class="form-label">Buscar</label>
                        <input type="text" name="q" id="buscar" value="{{ $search }}" class="form-control" placeholder="Nombre del curso">
                    </div>
                    <div class="col-md-6 d-flex">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('cursos.index') }}" class="btn btn-link text-decoration-none">
                            Limpiar
                        </a>
                    </div>
                </form>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="text-center">Docentes</th>
                                        <th class="text-center">Materias</th>
                                        <th class="text-center">Estudiantes</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cursos as $curso)
                                        <tr>
                                            <td class="fw-semibold">{{ $curso->nombre }}</td>
                                            <td class="text-center">{{ $curso->docentes_count }}</td>
                                            <td class="text-center">{{ $curso->materias_count }}</td>
                                            <td class="text-center">{{ $curso->estudiantes_count }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('cursos.show', $curso) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('cursos.destroy', $curso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar el curso {{ $curso->nombre }}?');">
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
                                            <td colspan="5" class="text-center py-4 text-muted">No se encontraron cursos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($cursos->hasPages())
                        <div class="card-footer bg-white">
                            {{ $cursos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
