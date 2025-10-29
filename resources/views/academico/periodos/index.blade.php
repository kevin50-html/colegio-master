@extends('layouts.app')

@section('title', 'Periodos - ' . $cursoMateria->curso->nombre . ' / ' . $cursoMateria->materia->nombre)

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
                        <a href="{{ route('academico.cursos.materias.index', $cursoMateria->curso) }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a materias del curso
                        </a>
                        <h1 class="h3 mb-1">Periodos de {{ $cursoMateria->materia->nombre }}</h1>
                        <p class="text-muted mb-0">Curso: {{ $cursoMateria->curso->nombre }} · Alias: {{ $cursoMateria->alias ?? '—' }}</p>
                    </div>
                    @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                        <a href="{{ route('academico.curso-materias.periodos.create', $cursoMateria) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nuevo periodo
                        </a>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periodo</th>
                                        <th>Fechas</th>
                                        <th>Orden</th>
                                        <th>Actividades</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periodos as $periodo)
                                        <tr>
                                            <td>{{ $periodo->nombre }}</td>
                                            <td>
                                                {{ optional($periodo->fecha_inicio)->format('Y-m-d') ?? '—' }} –
                                                {{ optional($periodo->fecha_fin)->format('Y-m-d') ?? '—' }}
                                            </td>
                                            <td>{{ $periodo->orden }}</td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary fw-normal">{{ $periodo->actividades_count }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.curso-materias.periodos.show', [$cursoMateria, $periodo]) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_periodos', 'acceso_total']))
                                                    <a href="{{ route('academico.curso-materias.periodos.edit', [$cursoMateria, $periodo]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.curso-materias.periodos.destroy', [$cursoMateria, $periodo]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar el periodo {{ $periodo->nombre }}?');">
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
                                            <td colspan="5" class="text-center text-muted py-4">No se han configurado periodos para esta materia en el curso.</td>
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
