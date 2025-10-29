@extends('layouts.app')

@section('title', 'Resumen Cursos por Materias')

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
                        <h1 class="h3 mb-1">Cursos por Materias</h1>
                        <p class="text-muted mb-0">Analiza cada curso con sus materias asociadas, periodos y horarios planificados.</p>
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

                <form method="GET" action="{{ route('academico.modulos.cursos-materias') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursosListado as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId === $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('academico.modulos.cursos-materias') }}" class="btn btn-link w-100 text-decoration-none">Limpiar</a>
                    </div>
                </form>

                @forelse($cursos as $curso)
                    @php
                        $totalPeriodos = $curso->materias->sum(fn ($materia) => $materia->periodos_count ?? $materia->periodos->count());
                        $totalHorarios = $curso->materias->sum(fn ($materia) => $materia->periodos->sum(fn ($periodo) => $periodo->horarios_count ?? $periodo->horarios->count()));
                    @endphp
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div>
                                <h5 class="mb-1">{{ $curso->nombre }}</h5>
                                <div class="text-muted small">{{ $curso->materias_count }} materias • {{ $totalPeriodos }} periodos • {{ $totalHorarios }} horarios</div>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <a href="{{ route('academico.cursos.show', $curso) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Detalle del curso
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($curso->materias->isEmpty())
                                <p class="text-muted mb-0">No hay materias registradas para este curso.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Materia</th>
                                                <th class="text-center">Periodos</th>
                                                <th class="text-center">Horarios</th>
                                                <th>Última actualización</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($curso->materias as $materia)
                                                @php
                                                    $horariosMateria = $materia->periodos->sum(fn ($periodo) => $periodo->horarios_count ?? $periodo->horarios->count());
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                        <div class="text-muted small">Código: {{ $materia->codigo ?? '—' }}</div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary-subtle text-primary fw-normal">{{ $materia->periodos_count ?? $materia->periodos->count() }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info-subtle text-info fw-normal">{{ $horariosMateria }}</span>
                                                    </td>
                                                    <td>{{ optional($materia->updated_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('academico.cursos.materias.show', [$curso, $materia]) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">
                            No hay cursos registrados con materias para mostrar.
                        </div>
                    </div>
                @endforelse

                <div class="d-flex justify-content-end">
                    {{ $cursos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
