@extends('layouts.app')

@section('title', 'Materias de ' . $curso->nombre)

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
                        <h1 class="h3 mb-1">Materias en {{ $curso->nombre }}</h1>
                        <p class="text-muted mb-0">Administra las asignaciones y define alias específicos para este curso.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h2 class="h5 mb-0">Materias asignadas</h2>
                                <span class="badge bg-primary-subtle text-primary">{{ $asignadas->count() }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @forelse($asignadas as $asignacion)
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold">{{ $asignacion->materia->nombre }}</div>
                                                <div class="text-muted small">Alias: {{ $asignacion->alias ?? '—' }} · Periodos: {{ $asignacion->periodos()->count() }}</div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                                                    <a href="{{ route('academico.curso-materias.periodos.index', $asignacion) }}" class="btn btn-outline-primary btn-sm">Periodos</a>
                                                @endif
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                                                    <form action="{{ route('academico.cursos.materias.update', [$curso, $asignacion]) }}" method="POST" class="d-flex gap-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" name="alias" value="{{ $asignacion->alias }}" class="form-control form-control-sm" placeholder="Alias opcional">
                                                        <button type="submit" class="btn btn-sm btn-warning">Guardar</button>
                                                    </form>
                                                    <form action="{{ route('academico.cursos.materias.destroy', [$curso, $asignacion]) }}" method="POST" onsubmit="return confirm('¿Deseas quitar la materia {{ $asignacion->materia->nombre }} del curso?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Quitar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-muted text-center">No hay materias asignadas.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h2 class="h5 mb-0">Agregar materia existente</h2>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('academico.cursos.materias.index', $curso) }}" class="row g-2 align-items-end mb-3">
                                    <div class="col-8">
                                        <label for="buscar" class="form-label small text-muted">Buscar materias disponibles</label>
                                        <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre o código">
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Materia</th>
                                                <th class="text-end">Asignar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($disponibles as $materia)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($materia->descripcion ?? '', 60) }}</div>
                                                    </td>
                                                    <td class="text-end">
                                                        @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                                                            <form action="{{ route('academico.cursos.materias.store', $curso) }}" method="POST" class="d-inline-flex gap-2">
                                                                @csrf
                                                                <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                                                <input type="text" name="alias" class="form-control form-control-sm" placeholder="Alias opcional">
                                                                <button type="submit" class="btn btn-sm btn-primary">Agregar</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted py-3">No hay materias disponibles para asignar.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end">
                                    {{ $disponibles->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
