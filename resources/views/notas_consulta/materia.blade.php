@extends('layouts.app')

@section('title', 'Consulta de ' . $materia->nombre)

@php
    $menuActivo = 'notas-consulta';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                @include('partials.sidebar', ['menuActivo' => $menuActivo])
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h1 class="h3 mb-1">{{ $materia->nombre }}</h1>
                            <p class="text-muted mb-0">Curso {{ $curso->nombre }} · Consulta la evolución de calificaciones por actividad y periodo.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('consulta-notas.curso', $curso) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver al curso
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Promedio del grupo</p>
                                    <h4 class="fw-bold mb-0">{{ $promedioCurso !== null ? number_format($promedioCurso, 2) : '—' }}</h4>
                                    <small class="text-muted">Con notas registradas: {{ $conNotas }}/{{ $estudiantes->count() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Estudiantes aprobados</p>
                                    <h4 class="fw-bold text-success mb-0">{{ $aprobados }}</h4>
                                    <small class="text-muted">Promedio ≥ 3.0</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">En riesgo</p>
                                    <h4 class="fw-bold text-warning mb-0">{{ $reprobados }}</h4>
                                    <small class="text-muted">Con promedio menor a 3.0</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Estudiantes y actividades</h5>
                            <small class="text-muted">Las celdas muestran la última calificación registrada por actividad.</small>
                        </div>
                        <div class="card-body">
                            @if($estudiantes->isEmpty())
                                <div class="alert alert-info mb-0">Este curso aún no tiene estudiantes matriculados.</div>
                            @elseif($actividades->isEmpty())
                                <div class="alert alert-info mb-0">Aún no se han definido actividades para esta materia.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th rowspan="2" class="align-middle" style="min-width: 220px;">Estudiante</th>
                                                @foreach($actividadesPorPeriodo as $grupo)
                                                    <th class="text-center" colspan="{{ $grupo->count() }}">
                                                        {{ $grupo->first()->periodo?->nombre ?? 'Sin periodo' }}
                                                    </th>
                                                @endforeach
                                                <th rowspan="2" class="align-middle text-center" style="min-width: 140px;">
                                                    Promedio general
                                                </th>
                                            </tr>
                                            <tr>
                                                @foreach($actividadesPorPeriodo as $grupo)
                                                    @foreach($grupo as $actividad)
                                                        <th class="text-center" style="min-width: 140px;">
                                                            <div class="fw-semibold small">{{ $actividad->titulo }}</div>
                                                            <div class="text-muted small">{{ $actividad->fecha_entrega?->format('d/m') ?? 'Sin fecha' }}</div>
                                                        </th>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($estudiantes as $estudiante)
                                                @php
                                                    $resumen = $resumenEstudiantes->get($estudiante->id);
                                                    $promedio = $resumen['general'] ?? null;
                                                    $aprobado = $promedio !== null && $promedio >= 3;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $estudiante->nombre_completo }}</div>
                                                        <div class="text-muted small">Documento: {{ $estudiante->documento_identidad ?? 'N/D' }}</div>
                                                    </td>
                                                    @foreach($actividades as $actividad)
                                                        @php
                                                            $nota = $notas->get($estudiante->id . '-' . $actividad->id);
                                                        @endphp
                                                        <td class="text-center">
                                                            @if($nota)
                                                                <span class="fw-semibold">{{ number_format($nota->valor, 2) }}</span>
                                                                <div class="text-muted small">{{ $nota->updated_at?->format('d/m/Y H:i') }}</div>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center">
                                                        @if($promedio !== null)
                                                            <span class="badge @class(['bg-success' => $aprobado, 'bg-warning text-dark' => !$aprobado])">
                                                                {{ number_format($promedio, 2) }}
                                                            </span>
                                                            <div class="small text-muted mt-1">
                                                                {{ $aprobado ? 'Aprobado' : 'En riesgo' }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Sin notas</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!$estudiantes->isEmpty() && !$actividades->isEmpty())
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Seguimiento por periodo</h5>
                                <small class="text-muted">Identifica qué periodos requieren refuerzo según el promedio del estudiante.</small>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            @foreach($periodosMeta as $periodo)
                                                <th class="text-center">{{ $periodo['nombre'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($estudiantes as $estudiante)
                                            @php
                                                $resumen = $resumenEstudiantes->get($estudiante->id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $estudiante->nombre_completo }}</div>
                                                    <div class="text-muted small">{{ $estudiante->documento_identidad ?? 'N/D' }}</div>
                                                </td>
                                                @foreach($periodosMeta as $key => $periodo)
                                                    @php
                                                        $promedioPeriodo = $resumen['periodos'][$key] ?? null;
                                                        $aprobadoPeriodo = $promedioPeriodo !== null && $promedioPeriodo >= 3;
                                                    @endphp
                                                    <td class="text-center">
                                                        @if($promedioPeriodo !== null)
                                                            <span class="badge @class(['bg-success' => $aprobadoPeriodo, 'bg-warning text-dark' => !$aprobadoPeriodo])">
                                                                {{ number_format($promedioPeriodo, 2) }}
                                                            </span>
                                                            <div class="small text-muted">{{ $aprobadoPeriodo ? 'Aprobado' : 'En riesgo' }}</div>
                                                        @else
                                                            <span class="text-muted">Sin notas</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
