@extends('layouts.app')

@section('title', 'Gestión de Docentes')

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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">Gestión de Docentes</h1>
                        <p class="text-muted mb-0">Controla la planta docente y sus asignaciones académicas.</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['gestionar_docentes', 'acceso_total']))
                        <a href="{{ route('docentes.crear') }}" class="btn btn-primary">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Nuevo docente
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('docentes.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre, apellidos, documento o correo">
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
                    <div class="col-12 col-md-4 col-lg-3">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoSeleccionado == $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            Filtrar
                        </button>
                    </div>
                    @if($busqueda !== '' || $estadoSeleccionado !== 'todos' || !empty($cursoSeleccionado))
                        <div class="col-12 col-md-2 col-lg-2">
                            <a href="{{ route('docentes.index') }}" class="btn btn-link text-decoration-none w-100">
                                Limpiar
                            </a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Docente</th>
                                <th>Documento</th>
                                <th>Especialidad</th>
                                <th>Asignaciones</th>
                                <th>Estado</th>
                                <th>Fecha ingreso</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($docentes as $docente)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $docente->nombre_completo }}</div>
                                        <div class="text-muted small">ID #{{ $docente->id }} · {{ $docente->email ?? 'Sin correo' }}</div>
                                    </td>
                                    <td>{{ $docente->documento_identidad }}</td>
                                    <td>{{ $docente->especialidad ?? 'Sin definir' }}</td>
                                    <td>
                                        @if($docente->cursos->isEmpty())
                                            <span class="text-muted small">Sin cursos</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary fw-normal">
                                                {{ $docente->cursos->pluck('nombre')->join(', ') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge @class(['bg-success' => $docente->estado === 'activo', 'bg-secondary' => $docente->estado === 'inactivo', 'bg-warning text-dark' => $docente->estado === 'suspendido'])">
                                            {{ ucfirst($docente->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($docente->fecha_ingreso)->format('Y-m-d') ?? '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('docentes.mostrar', $docente) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()?->hasAnyPermission(['gestionar_docentes', 'acceso_total']))
                                            <a href="{{ route('docentes.editar', $docente) }}" class="btn btn-warning btn-sm me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('docentes.eliminar', $docente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar al docente {{ $docente->nombre_completo }}?');">
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
                                    <td colspan="7" class="text-center text-muted py-4">No se encontraron docentes con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $docentes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
