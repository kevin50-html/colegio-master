@extends('layouts.app')

@section('title', 'Materia: ' . $materia->nombre)

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
                        <a href="{{ route('academico.materias.index') }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a materias
                        </a>
                        <h1 class="h3 mb-1">{{ $materia->nombre }}</h1>
                        <p class="text-muted mb-0">Consulta los cursos asociados y los periodos configurados.</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                        <a href="{{ route('academico.materias.edit', $materia) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar materia
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Detalles generales</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Código</dt>
                                    <dd class="col-sm-8">{{ $materia->codigo ?? '—' }}</dd>
                                    <dt class="col-sm-4">Intensidad</dt>
                                    <dd class="col-sm-8">{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' horas/semana' : '—' }}</dd>
                                    <dt class="col-sm-4">Descripción</dt>
                                    <dd class="col-sm-8">{{ $materia->descripcion ?? 'Sin descripción registrada.' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Resumen</h2>
                                <p class="mb-2"><i class="fas fa-layer-group me-2 text-primary"></i>{{ $resumen['cursos'] }} cursos asociados</p>
                                <p class="mb-0"><i class="fas fa-calendar-check me-2 text-primary"></i>{{ $resumen['periodos'] }} periodos configurados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Cursos que imparten la materia</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Curso</th>
                                        <th>Alias</th>
                                        <th>Periodos</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materia->cursoMaterias as $asignacion)
                                        <tr>
                                            <td>{{ $asignacion->curso->nombre }}</td>
                                            <td>{{ $asignacion->alias ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $asignacion->periodos->count() }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.cursos.materias.index', $asignacion->curso) }}" class="btn btn-outline-secondary btn-sm">
                                                    Ver asignación
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                                                    <a href="{{ route('academico.curso-materias.periodos.index', $asignacion) }}" class="btn btn-primary btn-sm ms-1">
                                                        Periodos
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">La materia aún no está asignada a ningún curso.</td>
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
