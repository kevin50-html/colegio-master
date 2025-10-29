@extends('layouts.app')

@section('title', 'Curso: ' . $curso->nombre)

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
                        <a href="{{ route('academico.cursos.index') }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a cursos
                        </a>
                        <h1 class="h3 mb-1">{{ $curso->nombre }}</h1>
                        <p class="text-muted mb-0">Administra las materias asignadas y accede a sus periodos y horarios.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_cursos', 'acceso_total']))
                            <a href="{{ route('academico.cursos.edit', $curso) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar curso
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                            <a href="{{ route('academico.cursos.materias.index', $curso) }}" class="btn btn-primary">
                                <i class="fas fa-layer-group me-1"></i>Gestionar materias
                            </a>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Materias asignadas</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Materia</th>
                                        <th>Alias</th>
                                        <th>Periodos</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($curso->cursoMaterias as $asignacion)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $asignacion->materia->nombre }}</div>
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($asignacion->materia->descripcion ?? '', 80) }}</div>
                                            </td>
                                            <td>{{ $asignacion->alias ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $asignacion->periodos->count() }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.curso-materias.periodos.index', $asignacion) }}" class="btn btn-info btn-sm me-1">
                                                    Periodos
                                                </a>
                                                <a href="{{ route('academico.curso-materias.horarios.index', $asignacion) }}" class="btn btn-outline-secondary btn-sm me-1">
                                                    Horarios
                                                </a>
                                                <a href="{{ route('academico.materias.show', $asignacion->materia) }}" class="btn btn-outline-primary btn-sm">
                                                    Materia
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Aún no hay materias asociadas a este curso.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
