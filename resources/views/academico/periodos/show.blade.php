@extends('layouts.app')

@section('title', 'Periodo: ' . $periodo->nombre)

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
                        <a href="{{ route('academico.cursos.materias.show', [$materia->curso, $materia]) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a la materia
                        </a>
                        <h1 class="h3 mb-1">{{ $periodo->nombre }}</h1>
                        <p class="text-muted mb-0">Gestiona los horarios de clase y sus actividades.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                            <a href="{{ route('academico.materias.periodos.edit', [$materia, $periodo]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar periodo
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['gestionar_horarios', 'acceso_total']))
                            <a href="{{ route('academico.periodos.horarios.create', $periodo) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nuevo horario
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Información del periodo</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Materia</dt>
                                    <dd class="col-sm-8">{{ $materia->nombre }}</dd>
                                    <dt class="col-sm-4">Orden</dt>
                                    <dd class="col-sm-8">{{ $periodo->orden }}</dd>
                                    <dt class="col-sm-4">Inicio</dt>
                                    <dd class="col-sm-8">{{ optional($periodo->fecha_inicio)->format('Y-m-d') ?? '—' }}</dd>
                                    <dt class="col-sm-4">Fin</dt>
                                    <dd class="col-sm-8">{{ optional($periodo->fecha_fin)->format('Y-m-d') ?? '—' }}</dd>
                                    <dt class="col-sm-4">Descripción</dt>
                                    <dd class="col-sm-8">{{ $periodo->descripcion ?? 'Sin descripción.' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Resumen</h2>
                                <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i>{{ $resumen['horarios'] }} horarios configurados</p>
                                <p class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i>{{ $resumen['actividades'] }} actividades programadas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Horarios</h2>
                        @if(Auth::user()?->hasAnyPermission(['gestionar_horarios', 'acceso_total']))
                            <a href="{{ route('academico.periodos.horarios.create', $periodo) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Nuevo horario
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Día</th>
                                        <th>Horas</th>
                                        <th>Aula</th>
                                        <th>Modalidad</th>
                                        <th>Actividades</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($horarios as $horario)
                                        <tr>
                                            <td class="fw-semibold">{{ $horario->dia_semana }}</td>
                                            <td>{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</td>
                                            <td>{{ $horario->aula ?? '—' }}</td>
                                            <td>{{ $horario->modalidad ?? 'Presencial' }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $horario->actividades_count }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.periodos.horarios.show', [$periodo, $horario]) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_horarios', 'acceso_total']))
                                                    <a href="{{ route('academico.periodos.horarios.edit', [$periodo, $horario]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.periodos.horarios.destroy', [$periodo, $horario]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este horario?');">
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
                                            <td colspan="6" class="text-center text-muted py-4">Aún no hay horarios registrados para este periodo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            {{ $horarios->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
