@extends('layouts.app')

@section('title', 'Módulo de Horarios')

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
                        <h1 class="h3 mb-1">Módulo de horarios</h1>
                        <p class="text-muted mb-0">Filtra los horarios por curso, materia, periodo y día.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('academico.modulos.horarios') }}" class="row g-2 align-items-end mb-4">
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
                        <label for="periodo_id" class="form-label small text-muted">Periodo</label>
                        <select name="periodo_id" id="periodo_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" @selected($periodoId == $periodo->id)>{{ $periodo->nombre }} ({{ $periodo->cursoMateria->materia->nombre }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="dia" class="form-label small text-muted">Día</label>
                        <select name="dia" id="dia" class="form-select">
                            <option value="">Todos</option>
                            @foreach($diasDisponibles as $diaDisponible)
                                <option value="{{ $diaDisponible }}" @selected($dia === $diaDisponible)>{{ $diaDisponible }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th>Materia</th>
                                <th>Periodo</th>
                                <th>Día</th>
                                <th>Horario</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($horarios as $horario)
                                <tr>
                                    <td>{{ $horario->cursoMateria->curso->nombre }}</td>
                                    <td>{{ $horario->cursoMateria->materia->nombre }}</td>
                                    <td>{{ $horario->periodo?->nombre ?? '—' }}</td>
                                    <td>{{ $horario->dia }}</td>
                                    <td>{{ $horario->hora_inicio }} – {{ $horario->hora_fin }}</td>
                                    <td>{{ $horario->aula ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No se encontraron horarios con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $horarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
