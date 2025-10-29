@extends('layouts.app')

@section('title', 'Gestión Académica - Cursos')

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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Gestión Académica</h1>
                        <p class="text-muted mb-0">Administra los cursos y navega por toda la secuencia académica.</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['gestionar_cursos', 'acceso_total']))
                        <a href="{{ route('academico.cursos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nuevo curso
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('academico.cursos.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre del curso">
                        </div>
                    </div>
                    <div class="col-sm-3 col-lg-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            Filtrar
                        </button>
                    </div>
                    @if($busqueda !== '')
                        <div class="col-sm-3 col-lg-2">
                            <a href="{{ route('academico.cursos.index') }}" class="btn btn-link text-decoration-none w-100">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th class="text-center">Materias</th>
                                <th>Última actualización</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cursos as $curso)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $curso->nombre }}</div>
                                        <div class="text-muted small">ID #{{ $curso->id }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-normal">{{ $curso->materias_count }}</span>
                                    </td>
                                    <td>{{ optional($curso->updated_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('academico.cursos.show', $curso) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()?->hasAnyPermission(['gestionar_cursos', 'acceso_total']))
                                            <a href="{{ route('academico.cursos.edit', $curso) }}" class="btn btn-warning btn-sm me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('academico.cursos.destroy', $curso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar el curso {{ $curso->nombre }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No hay cursos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $cursos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
