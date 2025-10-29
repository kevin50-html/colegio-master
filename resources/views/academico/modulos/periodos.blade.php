@extends('layouts.app')

@section('title', 'Módulo de Periodos')

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
                        <h1 class="h3 mb-1">Módulo de periodos</h1>
                        <p class="text-muted mb-0">Consulta periodos por curso y materia sin navegar la jerarquía completa.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('academico.modulos.periodos') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-3">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="materia_id" class="form-label small text-muted">Materia</label>
                        <select name="materia_id" id="materia_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia->id }}" @selected($materiaId == $materia->id)>{{ $materia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre de periodo">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                    @if($cursoId || $materiaId || $busqueda)
                        <div class="col-md-1">
                            <a href="{{ route('academico.modulos.periodos') }}" class="btn btn-link text-decoration-none w-100">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Periodo</th>
                                <th>Curso</th>
                                <th>Materia</th>
                                <th>Fechas</th>
                                <th>Actividades</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodos as $periodo)
                                @php
                                    $cursoMateria = $periodo->cursoMateria;
                                @endphp
                                <tr>
                                    <td>{{ $periodo->nombre }}</td>
                                    <td>{{ $cursoMateria?->curso?->nombre ?? 'Sin asignar' }}</td>
                                    <td>{{ $cursoMateria?->materia?->nombre ?? 'Sin asignar' }}</td>
                                    <td>
                                        {{ optional($periodo->fecha_inicio)->format('Y-m-d') ?? '—' }} –
                                        {{ optional($periodo->fecha_fin)->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary fw-normal">{{ $periodo->actividades_count }}</span></td>
                                    <td class="text-end">
                                        @if($cursoMateria)
                                            <a href="{{ route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo]) }}" class="btn btn-info btn-sm me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('academico.curso-materias.periodos.edit', [$cursoMateria, $periodo]) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @else
                                            <span class="text-muted small">Sin asignación de curso/materia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No se encontraron periodos para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $periodos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
