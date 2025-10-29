@extends('layouts.app')

@section('title', 'Módulo de Materias')

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
                        <h1 class="h3 mb-1">Módulo de materias</h1>
                        <p class="text-muted mb-0">Explora todas las materias y los cursos en los que está asignada cada una.</p>
                    </div>
                    <a href="{{ route('academico.materias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva materia
                    </a>
                </div>

                <form method="GET" action="{{ route('academico.modulos.materias') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="buscar" class="form-label small text-muted">Buscar</label>
                        <input type="text" name="q" id="buscar" value="{{ $busqueda }}" class="form-control" placeholder="Nombre o código de materia">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
                    </div>
                    @if($cursoId || $busqueda)
                        <div class="col-md-2">
                            <a href="{{ route('academico.modulos.materias') }}" class="btn btn-link text-decoration-none w-100">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Materia</th>
                                <th>Cursos asignados</th>
                                <th>Código</th>
                                <th>Intensidad</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materias as $materia)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $materia->nombre }}</div>
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($materia->descripcion ?? '', 80) }}</div>
                                    </td>
                                    <td>
                                        @if($materia->cursos->isEmpty())
                                            <span class="text-muted small">Sin asignaciones</span>
                                        @else
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($materia->cursos as $curso)
                                                    <li>{{ $curso->nombre }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>{{ $materia->codigo ?? '—' }}</td>
                                    <td>{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' h/sem' : '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('academico.materias.show', $materia) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('academico.materias.edit', $materia) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No se encontraron materias con los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $materias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
