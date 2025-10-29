@extends('layouts.app')

@section('title', 'Resumen cursos-materias')

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
                        <h1 class="h3 mb-1">Resumen cursos ↔ materias</h1>
                        <p class="text-muted mb-0">Visualiza rápidamente qué materias tiene cada curso y sus periodos asociados.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('academico.modulos.cursos-materias') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursosListado as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                </form>

                @forelse($cursos as $curso)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h5 mb-0">{{ $curso->nombre }}</h2>
                                <small class="text-muted">{{ $curso->curso_materias_count }} materias asignadas</small>
                            </div>
                            <a href="{{ route('academico.cursos.show', $curso) }}" class="btn btn-outline-primary btn-sm">
                                Ver curso
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Materia</th>
                                            <th>Alias</th>
                                            <th>Periodos</th>
                                            <th>Actividades</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($curso->cursoMaterias as $asignacion)
                                            <tr>
                                                <td>{{ $asignacion->materia->nombre }}</td>
                                                <td>{{ $asignacion->alias ?? '—' }}</td>
                                                <td>{{ $asignacion->periodos->count() }}</td>
                                                <td>{{ $asignacion->periodos->sum(fn($p) => $p->actividades->count()) }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('academico.curso-materias.periodos.index', $asignacion) }}" class="btn btn-info btn-sm me-1">
                                                        Periodos
                                                    </a>
                                                    <a href="{{ route('academico.curso-materias.horarios.index', $asignacion) }}" class="btn btn-outline-secondary btn-sm">
                                                        Horarios
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">Este curso no tiene materias asociadas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No hay cursos configurados.</div>
                @endforelse

                <div class="d-flex justify-content-end">
                    {{ $cursos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
