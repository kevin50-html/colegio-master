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
                        <h1 class="h3 mb-1">Módulo de Periodos</h1>
                        <p class="text-muted mb-0">Supervisa los periodos académicos filtrando por curso, materia o nombre.</p>
                    </div>
                    <a href="{{ route('academico.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver al panel académico
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('academico.modulos.periodos') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="q" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="q" value="{{ $busqueda }}" class="form-control" placeholder="Nombre del periodo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId === $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="materia_id" class="form-label small text-muted">Materia</label>
                        <select name="materia_id" id="materia_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($materias as $materia)
                                <option value="{{ $materia->id }}" @selected($materiaId === $materia->id)>
                                    {{ $materia->nombre }}
                                    @if($materia->curso)
                                        ({{ $materia->curso->nombre }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('academico.modulos.periodos') }}" class="btn btn-link w-100 text-decoration-none">Limpiar</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Periodo</th>
                                <th>Materia</th>
                                <th>Curso</th>
                                <th>Fechas</th>
                                <th class="text-center">Horarios</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodos as $periodo)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $periodo->nombre }}</div>
                                        <div class="text-muted small">Orden {{ $periodo->orden ?? '—' }}</div>
                                    </td>
                                    <td>{{ $periodo->materia?->nombre ?? 'Sin materia' }}</td>
                                    <td>{{ $periodo->materia?->curso?->nombre ?? 'Sin curso' }}</td>
                                    <td>
                                        @if($periodo->fecha_inicio && $periodo->fecha_fin)
                                            {{ $periodo->fecha_inicio->format('d/m/Y') }} – {{ $periodo->fecha_fin->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-normal">
                                            {{ $periodo->horarios_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($periodo->materia && $periodo->materia->curso)
                                            <a href="{{ route('academico.materias.periodos.show', [$periodo->materia, $periodo]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay periodos con los criterios seleccionados.</td>
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
