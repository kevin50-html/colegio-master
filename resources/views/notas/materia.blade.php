@extends('layouts.app')

@section('title', 'Notas de ' . $materia->nombre)

@php
    $menuActivo = 'notas';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
    $actividadesPorPeriodo = $actividades->groupBy(fn($actividad) => $actividad->periodo?->id ?? 'sin-periodo');
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
                            <p class="text-muted mb-0">Curso {{ $curso->nombre }} · Registra las calificaciones por actividad.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('notas.curso', $curso) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver al curso
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-0">Estudiantes y actividades</h5>
                                    <small class="text-muted">Cada columna corresponde a una actividad registrada en la materia.</small>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">{{ $actividades->count() }} actividades</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($estudiantes->isEmpty())
                                <div class="alert alert-info mb-0">Este curso aún no tiene estudiantes matriculados.</div>
                            @elseif($actividades->isEmpty())
                                <div class="alert alert-info mb-0">Aún no se han definido actividades para esta materia. Regístralas desde el módulo de actividades.</div>
                            @else
                                <p class="text-muted small mb-3">
                                    Ingresa calificaciones entre 0.0 y 5.0. Deja el campo vacío y guarda para eliminar una nota existente.
                                </p>
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
                                                    Promedio
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
                                                    $valores = $actividades->map(function ($actividad) use ($notas, $estudiante) {
                                                        $nota = $notas->get($estudiante->id . '-' . $actividad->id);
                                                        return $nota?->valor;
                                                    })->filter(fn ($valor) => $valor !== null);

                                                    $promedio = $valores->isNotEmpty() ? round($valores->avg(), 2) : null;
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
                                                        <td>
                                                            <form action="{{ route('notas.guardar', [$curso, $materia]) }}" method="POST" class="d-flex flex-column gap-1">
                                                                @csrf
                                                                <input type="hidden" name="actividad_id" value="{{ $actividad->id }}">
                                                                <input type="hidden" name="estudiante_id" value="{{ $estudiante->id }}">
                                                                <div class="input-group input-group-sm">
                                                                    <input type="number" name="valor" min="0" max="5" step="0.1" class="form-control" value="{{ $nota->valor ?? '' }}" placeholder="0-5">
                                                                    <button class="btn btn-outline-primary" type="submit">
                                                                        <i class="fas fa-save"></i>
                                                                    </button>
                                                                </div>
                                                            </form>
                                                            @if($nota)
                                                                <small class="text-muted">Actualizado {{ $nota->updated_at?->format('d/m/Y H:i') }}</small>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center">
                                                        @if($promedio !== null)
                                                            <span @class(['badge', 'text-dark', 'bg-success' => $aprobado, 'bg-warning' => !$aprobado])>
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
                </div>
            </div>
        </div>
    </div>
@endsection
