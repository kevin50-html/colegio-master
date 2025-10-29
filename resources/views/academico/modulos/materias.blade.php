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
                        <h1 class="h3 mb-1">Módulo de Materias</h1>
                        <p class="text-muted mb-0">Consulta todas las materias disponibles y filtra por curso o coincidencia.</p>
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

                <form method="GET" action="{{ route('academico.modulos.materias') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="q" class="form-label small text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" id="q" value="{{ $busqueda }}" class="form-control" placeholder="Nombre o código de la materia">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="curso_id" class="form-label small text-muted">Curso</label>
                        <select name="curso_id" id="curso_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" @selected($cursoId === $curso->id)>{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('academico.modulos.materias') }}" class="btn btn-link w-100 text-decoration-none">Limpiar</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Materia</th>
                                <th>Curso</th>
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
                                        <div class="text-muted small">ID #{{ $materia->id }}</div>
                                    </td>
                                    <td>{{ $materia->curso?->nombre ?? 'Sin curso' }}</td>
                                    <td>{{ $materia->codigo ?? '—' }}</td>
                                    <td>{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' h' : '—' }}</td>
                                    <td class="text-end">
                                        @if($materia->curso)
                                            <a href="{{ route('academico.cursos.materias.show', [$materia->curso, $materia]) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay materias registradas con los criterios seleccionados.</td>
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
