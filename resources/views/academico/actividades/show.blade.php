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
                        <a href="{{ route('academico.periodos.actividades.index', $periodo) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a actividades
                        </a>
                        <h1 class="h3 mb-1">{{ $actividad->titulo }}</h1>
                        <p class="text-muted mb-0">Periodo: {{ $periodo->nombre }} · Curso: {{ $periodo->cursoMateria->curso->nombre }}</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['crear_actividades', 'acceso_total']))
                            <a href="{{ route('academico.periodos.actividades.edit', [$periodo, $actividad]) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar actividad
                            </a>
                            <form action="{{ route('academico.periodos.actividades.destroy', [$periodo, $actividad]) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta actividad?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2"><strong>Fecha de entrega:</strong> {{ optional($actividad->fecha_entrega)->format('Y-m-d') ?? '—' }}</p>
                        <p class="mb-2"><strong>Porcentaje:</strong> {{ $actividad->porcentaje ? $actividad->porcentaje . '%' : '—' }}</p>
                        <p class="mb-0"><strong>Descripción:</strong></p>
                        <p class="text-muted">{{ $actividad->descripcion ?? 'Sin descripción registrada.' }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Notas registradas</h2>
                        @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                            <a href="{{ route('academico.actividades.notas.create', $actividad) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>Registrar nota
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
                                    @forelse($actividad->notas as $nota)
                                        <tr>
                                            <td>{{ $nota->estudiante?->nombre_completo ?? '—' }}</td>
                                            <td>{{ $nota->valor ?? '—' }}</td>
                                            <td>{{ $nota->observaciones ?? '—' }}</td>
                                            <td class="text-end">
                                                @if(Auth::user()?->hasAnyPermission(['registrar_notas', 'acceso_total']))
                                                    <a href="{{ route('academico.actividades.notas.edit', [$actividad, $nota]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.actividades.notas.destroy', [$actividad, $nota]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar esta nota?');">
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
                                            <td colspan="4" class="text-center text-muted py-4">No se han registrado notas para esta actividad.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
