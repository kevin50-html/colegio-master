@extends('layouts.app')

@section('title', 'Gestión de Estudiantes')

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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Gestión de Estudiantes</h1>
                        <p class="text-muted mb-0">Controla el estado académico y la asignación de cursos.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('matriculas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-file-alt me-1"></i>Ver matrículas
                        </a>
                        <a href="{{ route('estudiantes.crear') }}" class="btn btn-primary">
                            <i class="fas fa-user-graduate me-1"></i>Nuevo estudiante
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('estudiantes.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre, apellidos o documento">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3">
                        <label for="estado" class="form-label small text-muted">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            @foreach($estadosDisponibles as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected($estadoSeleccionado === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            Filtrar
                        </button>
                    </div>
                    @if($busqueda !== '' || $estadoSeleccionado !== 'todos')
                        <div class="col-12 col-md-2 col-lg-2">
                            <a href="{{ route('estudiantes.index') }}" class="btn btn-link text-decoration-none w-100">
                                Limpiar
                            </a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Curso</th>
                                <th>Estado</th>
                                <th>Fecha matrícula</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estudiantes as $estudiante)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $estudiante->nombre_completo }}</div>
                                        <div class="text-muted small">ID #{{ $estudiante->id }}</div>
                                    </td>
                                    <td>{{ $estudiante->documento_identidad }}</td>
                                    <td>{{ optional($estudiante->curso)->nombre ?? 'Sin asignar' }}</td>
                                    <td>
                                        <span class="badge @class(['bg-success' => $estudiante->estado === 'activo', 'bg-secondary' => $estudiante->estado === 'inactivo', 'bg-info text-dark' => $estudiante->estado === 'egresado'])">
                                            {{ ucfirst($estudiante->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($estudiante->fecha_matricula)->format('Y-m-d') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('estudiantes.mostrar', $estudiante) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('estudiantes.editar', $estudiante) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('estudiantes.eliminar', $estudiante) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar al estudiante {{ $estudiante->nombre_completo }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No se encontraron estudiantes con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $estudiantes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
