@extends('layouts.app')

@section('title', 'Módulo de Actividades')

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
                        <h1 class="h3 mb-1">Módulo de actividades</h1>
                        <p class="text-muted mb-0">Filtra las actividades por curso, materia o periodo para acceder rápidamente a su detalle.</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('academico.modulos.actividades') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-12 col-lg-3">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-3">
                        <label for="materia_id" class="form-label small text-muted">Materia</label>
                        <select name="materia_id" id="materia_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia->id }}" @selected($materiaId == $materia->id)>{{ $materia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label for="periodo_id" class="form-label small text-muted">Periodo</label>
                        <select name="periodo_id" id="periodo_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" @selected($periodoId == $periodo->id)>
                                    {{ $periodo->nombre }} · {{ $periodo->cursoMateria->curso->nombre }} / {{ $periodo->cursoMateria->materia->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Título de actividad">
                    </div>
                    <div class="col-12 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-secondary flex-fill">Filtrar</button>
                        @if($cursoId || $materiaId || $periodoId || $busqueda)
                            <a href="{{ route('academico.modulos.actividades') }}" class="btn btn-link text-decoration-none flex-fill">Limpiar</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Actividad</th>
                                <th>Periodo</th>
                                <th>Curso</th>
                                <th>Materia</th>
                                <th>Entrega</th>
                                <th>Notas</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actividades as $actividad)
                                <tr>
                                    <td>{{ $actividad->titulo }}</td>
                                    <td>{{ $actividad->periodo->nombre }}</td>
                                    <td>{{ $actividad->periodo->cursoMateria->curso->nombre }}</td>
                                    <td>{{ $actividad->periodo->cursoMateria->materia->nombre }}</td>
                                    <td>{{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-normal">{{ $actividad->notas_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('academico.periodos.actividades.show', [$actividad->periodo, $actividad]) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                                            <a href="{{ route('academico.actividades.notas.create', $actividad) }}" class="btn btn-success btn-sm me-1">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                                            <a href="{{ route('academico.periodos.actividades.edit', [$actividad->periodo, $actividad]) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No se encontraron actividades para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $actividades->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
