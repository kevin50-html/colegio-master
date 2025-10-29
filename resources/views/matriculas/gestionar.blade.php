@extends('layouts.app')

@section('title', 'Gestión de Matrículas')

@section('content')
@php
    $menuActivo = 'matriculas';
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
                        <h1 class="h3 mb-1">Gestión de Matrículas</h1>
                        <p class="text-muted mb-0">Administra las solicitudes registradas por acudientes y personal interno.</p>
                    </div>
                    <a href="{{ route('matriculas.crear') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva matrícula
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @isset($moduloEstudiantesListo)
                    @if(!$moduloEstudiantesListo)
                        <div class="alert alert-warning">
                            Debes ejecutar las migraciones más recientes (<code>php artisan migrate</code>) para habilitar la vinculación automática entre matrículas y estudiantes.
                        </div>
                    @endif
                @endisset

                <form method="GET" action="{{ route('matriculas.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre, documento o correo">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <label for="estado" class="form-label small text-muted">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            @foreach($estadosDisponibles as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected($estadoSeleccionado === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                    @if($busqueda !== '' || $estadoSeleccionado !== 'todos')
                        <div class="col-12 col-md-3 col-lg-2">
                            <a href="{{ route('matriculas.index') }}" class="btn btn-link text-decoration-none w-100">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>Estado</th>
                                <th>Registrada</th>
                                <th>Documentos</th>
                                <th>Creado por</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matriculas as $matricula)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $matricula->nombres }} {{ $matricula->apellidos }}</div>
                                        <div class="text-muted small">{{ $matricula->documento_identidad }}</div>
                                    </td>
                                    <td>{{ optional($matricula->curso)->nombre ?? 'Por asignar' }}</td>
                                    <td>
                                        <span class="badge @class([
                                            'bg-warning text-dark' => $matricula->estado === 'pendiente',
                                            'bg-success' => $matricula->estado === 'aprobada',
                                            'bg-danger' => $matricula->estado === 'rechazada',
                                        ])">
                                            {{ ucfirst($matricula->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($matricula->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ is_array($matricula->documentos) ? count($matricula->documentos) : 0 }}</td>
                                    <td>{{ optional($matricula->acudiente)->name ?? 'Sistema' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('matriculas.mostrar', $matricula) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Revisar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No se encontraron matrículas con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $matriculas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
