@extends('layouts.app')

@section('title', $curso->nombre)

@section('content')
@php
    $menuActivo = 'cursos';
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
                        <h1 class="h3 mb-1">{{ $curso->nombre }}</h1>
                        <p class="text-muted mb-0">Información general del curso.</p>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-md-0">
                        <a href="{{ route('cursos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <a href="{{ route('cursos.edit', $curso) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <form action="{{ route('cursos.destroy', $curso) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar el curso {{ $curso->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Creado</h6>
                                <p class="mb-0">{{ $curso->created_at?->format('d/m/Y H:i') }}</p>
                                <small class="text-muted">Última actualización: {{ $curso->updated_at?->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Materias asociadas</h6>
                                <p class="fs-4 fw-semibold mb-0">{{ $curso->materias_count }}</p>
                                <small class="text-muted">Materias enlazadas al curso.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Docentes asignados</h6>
                                <p class="fs-4 fw-semibold mb-0">{{ $curso->docentes_count }}</p>
                                <small class="text-muted">Docentes vinculados a este curso.</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase">Estudiantes inscritos</h6>
                                <p class="fs-4 fw-semibold mb-0">{{ $curso->estudiantes_count }}</p>
                                <small class="text-muted">Solicitudes registradas: {{ $curso->matriculas_count }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><i class="fas fa-book me-2"></i>Materias asociadas</h5>
                                @if($curso->materias->isEmpty())
                                    <p class="text-muted mb-0">No hay materias asociadas al curso.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($curso->materias as $materia)
                                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                <span>{{ $materia->nombre }}</span>
                                                <span class="badge bg-light text-muted">Código {{ $materia->codigo }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Docentes asignados</h5>
                                @if($curso->docentes->isEmpty())
                                    <p class="text-muted mb-0">No hay docentes asignados a este curso.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($curso->docentes as $docente)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>{{ $docente->nombres }} {{ $docente->apellidos }}</span>
                                                    @if($docente->email)
                                                        <span class="badge bg-light text-muted">{{ $docente->email }}</span>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><i class="fas fa-user-graduate me-2"></i>Estudiantes inscritos</h5>
                                @php
                                    $estudiantesListado = $curso->estudiantes->take(10);
                                @endphp
                                @if($estudiantesListado->isEmpty())
                                    <p class="text-muted mb-0">No hay estudiantes asignados a este curso.</p>
                                @else
                                    <p class="text-muted">Mostrando {{ $estudiantesListado->count() }} de {{ $curso->estudiantes_count }} estudiantes.</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Documento</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($estudiantesListado as $estudiante)
                                                    <tr>
                                                        <td>{{ $estudiante->nombre_completo }}</td>
                                                        <td>{{ $estudiante->documento_identidad }}</td>
                                                        <td>{{ $estudiante->email }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($curso->estudiantes_count > $estudiantesListado->count())
                                        <p class="text-muted mb-0">Consulta el módulo de estudiantes para ver el listado completo.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
