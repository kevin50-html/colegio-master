@extends('layouts.app')

@section('title', 'Materias - ' . $curso->nombre)

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
                        <a href="{{ route('academico.cursos.show', $curso) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver al curso
                        </a>
                        <h1 class="h3 mb-1">Materias de {{ $curso->nombre }}</h1>
                        <p class="text-muted mb-0">Selecciona una materia para continuar con periodos y horarios.</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                        <a href="{{ route('academico.cursos.materias.create', $curso) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nueva materia
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('academico.cursos.materias.index', $curso) }}" class="row g-2 align-items-end mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre, código o descripción">
                        </div>
                    </div>
                    <div class="col-sm-3 col-lg-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                    @if($busqueda !== '')
                        <div class="col-sm-3 col-lg-2">
                            <a href="{{ route('academico.cursos.materias.index', $curso) }}" class="btn btn-link text-decoration-none w-100">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Materia</th>
                                <th>Código</th>
                                <th>Intensidad</th>
                                <th>Periodos</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materias as $materia)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $materia->nombre }}</div>
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($materia->descripcion ?? '', 80) }}</div>
                                    </td>
                                    <td>{{ $materia->codigo ?? '—' }}</td>
                                    <td>{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' h/sem' : '—' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-normal">{{ $materia->periodos_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('academico.cursos.materias.show', [$curso, $materia]) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                        @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                                            <a href="{{ route('academico.cursos.materias.edit', [$curso, $materia]) }}" class="btn btn-warning btn-sm me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('academico.cursos.materias.destroy', [$curso, $materia]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la materia {{ $materia->nombre }}?');">
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
                                    <td colspan="5" class="text-center text-muted py-4">No se encontraron materias.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $materias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
