@extends('layouts.app')

@section('title', 'Materia: ' . $materia->nombre)

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
                        <a href="{{ route('academico.cursos.materias.index', $curso) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a materias
                        </a>
                        <h1 class="h3 mb-1">{{ $materia->nombre }}</h1>
                        <p class="text-muted mb-0">Configura los periodos y horarios asociados.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                            <a href="{{ route('academico.cursos.materias.edit', [$curso, $materia]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar materia
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                            <a href="{{ route('academico.materias.periodos.create', $materia) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nuevo periodo
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Detalles generales</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Curso</dt>
                                    <dd class="col-sm-8">{{ $curso->nombre }}</dd>
                                    <dt class="col-sm-4">Código</dt>
                                    <dd class="col-sm-8">{{ $materia->codigo ?? '—' }}</dd>
                                    <dt class="col-sm-4">Intensidad</dt>
                                    <dd class="col-sm-8">{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' horas/semana' : '—' }}</dd>
                                    <dt class="col-sm-4">Descripción</dt>
                                    <dd class="col-sm-8">{{ $materia->descripcion ?? 'Sin descripción registrada.' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Resumen</h2>
                                <p class="mb-2"><i class="fas fa-calendar-check me-2 text-primary"></i>{{ $resumen['periodos'] }} periodos creados</p>
                                <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i>{{ $resumen['horarios'] }} horarios configurados</p>
                                <p class="mb-0"><i class="fas fa-tasks me-2 text-primary"></i>{{ $resumen['actividades'] }} actividades programadas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Periodos académicos</h2>
                        @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                            <a href="{{ route('academico.materias.periodos.create', $materia) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Nuevo periodo
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Fechas</th>
                                        <th>Orden</th>
                                        <th>Horarios</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periodos as $periodo)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $periodo->nombre }}</div>
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($periodo->descripcion ?? '', 80) }}</div>
                                            </td>
                                            <td>
                                                {{ optional($periodo->fecha_inicio)->format('Y-m-d') ?? '—' }}
                                                —
                                                {{ optional($periodo->fecha_fin)->format('Y-m-d') ?? '—' }}
                                            </td>
                                            <td>{{ $periodo->orden }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $periodo->horarios_count }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.materias.periodos.show', [$materia, $periodo]) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                                                    <a href="{{ route('academico.materias.periodos.edit', [$materia, $periodo]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.materias.periodos.destroy', [$materia, $periodo]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar el periodo {{ $periodo->nombre }}?');">
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
                                            <td colspan="5" class="text-center text-muted py-4">Aún no hay periodos académicos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            {{ $periodos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
