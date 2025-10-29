@extends('layouts.app')

@section('title', 'Actividad: ' . $actividad->titulo)

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
                        <a href="{{ route('academico.periodos.horarios.show', [$horario->periodo, $horario]) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver al horario
                        </a>
                        <h1 class="h3 mb-1">{{ $actividad->titulo }}</h1>
                        <p class="text-muted mb-0">Registra y consulta las calificaciones de los estudiantes.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                            <a href="{{ route('academico.horarios.actividades.edit', [$horario, $actividad]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar actividad
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                            <a href="{{ route('academico.actividades.notas.create', $actividad) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva nota
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Detalles de la actividad</h2>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Materia</dt>
                                    <dd class="col-sm-8">{{ $horario->periodo->materia->nombre }}</dd>
                                    <dt class="col-sm-4">Periodo</dt>
                                    <dd class="col-sm-8">{{ $horario->periodo->nombre }}</dd>
                                    <dt class="col-sm-4">Horario</dt>
                                    <dd class="col-sm-8">{{ $horario->dia_semana }} {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</dd>
                                    <dt class="col-sm-4">Fecha entrega</dt>
                                    <dd class="col-sm-8">{{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }}</dd>
                                    <dt class="col-sm-4">Porcentaje</dt>
                                    <dd class="col-sm-8">{{ $actividad->porcentaje ? $actividad->porcentaje . '%' : '—' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h2 class="h5">Descripción</h2>
                                <p class="mb-0">{{ $actividad->descripcion ?? 'Sin descripción registrada.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Notas registradas</h2>
                        @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                            <a href="{{ route('academico.actividades.notas.create', $actividad) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva nota
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Valor</th>
                                        <th>Observaciones</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($notas as $nota)
                                        <tr>
                                            <td>{{ $nota->estudiante?->nombre_completo ?? 'Estudiante eliminado' }}</td>
                                            <td>{{ $nota->valor !== null ? number_format($nota->valor, 2) : '—' }}</td>
                                            <td>{{ $nota->observaciones ?? '—' }}</td>
                                            <td class="text-end">
                                                @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                                                    <a href="{{ route('academico.actividades.notas.edit', [$actividad, $nota]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.actividades.notas.destroy', [$actividad, $nota]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la nota de {{ $nota->estudiante?->nombre_completo }}?');">
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
                                            <td colspan="4" class="text-center text-muted py-4">No hay notas registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-end">
                            {{ $notas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
