@extends('layouts.app')

@section('title', 'Horario: ' . $horario->dia_semana . ' ' . \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i'))

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
                        <a href="{{ route('academico.materias.periodos.show', [$periodo->materia, $periodo]) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver al periodo
                        </a>
                        <h1 class="h3 mb-1">{{ $horario->dia_semana }} {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</h1>
                        <p class="text-muted mb-0">Gestiona las actividades académicas asociadas a este horario.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_horarios', 'acceso_total']))
                            <a href="{{ route('academico.periodos.horarios.edit', [$periodo, $horario]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar horario
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                            <a href="{{ route('academico.horarios.actividades.create', $horario) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva actividad
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Detalles del horario</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Periodo</dt>
                                    <dd class="col-sm-8">{{ $periodo->nombre }}</dd>
                                    <dt class="col-sm-4">Materia</dt>
                                    <dd class="col-sm-8">{{ $periodo->materia->nombre }}</dd>
                                    <dt class="col-sm-4">Horario</dt>
                                    <dd class="col-sm-8">{{ $horario->dia_semana }} {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</dd>
                                    <dt class="col-sm-4">Aula</dt>
                                    <dd class="col-sm-8">{{ $horario->aula ?? '—' }}</dd>
                                    <dt class="col-sm-4">Modalidad</dt>
                                    <dd class="col-sm-8">{{ $horario->modalidad ?? 'Presencial' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Resumen de actividades</h2>
                                <p class="mb-2"><i class="fas fa-tasks me-2 text-primary"></i>{{ $resumen['actividades'] }} actividades programadas</p>
                                <p class="mb-0"><i class="fas fa-check-circle me-2 text-primary"></i>{{ $resumen['notas'] }} calificaciones registradas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Actividades</h2>
                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                            <a href="{{ route('academico.horarios.actividades.create', $horario) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva actividad
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Fecha entrega</th>
                                        <th>Porcentaje</th>
                                        <th>Notas</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($actividades as $actividad)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $actividad->titulo }}</div>
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($actividad->descripcion ?? '', 80) }}</div>
                                            </td>
                                            <td>{{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $actividad->porcentaje ? $actividad->porcentaje . '%' : '—' }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $actividad->notas_count }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.horarios.actividades.show', [$horario, $actividad]) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                                                    <a href="{{ route('academico.horarios.actividades.edit', [$horario, $actividad]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.horarios.actividades.destroy', [$horario, $actividad]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la actividad {{ $actividad->titulo }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aún no hay actividades registradas para este horario.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            {{ $actividades->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
