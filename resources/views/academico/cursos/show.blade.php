@extends('layouts.app')

@section('title', 'Curso: ' . $curso->nombre)

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
                        <a href="{{ route('academico.cursos.index') }}" class="btn btn-link text-decoration-none p-0 mb-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver a cursos
                        </a>
                        <h1 class="h3 mb-1">{{ $curso->nombre }}</h1>
                        <p class="text-muted mb-0">Gestiona las materias asignadas a este curso.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if(Auth::user()?->hasAnyPermission(['gestionar_cursos', 'acceso_total']))
                            <a href="{{ route('academico.cursos.edit', $curso) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Editar curso
                            </a>
                        @endif
                        @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                            <a href="{{ route('academico.cursos.materias.create', $curso) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nueva materia
                            </a>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Materias del curso</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Materia</th>
                                        <th>Código</th>
                                        <th>Intensidad</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($curso->materias as $materia)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($materia->descripcion ?? '', 80) }}</div>
                                            </td>
                                            <td>{{ $materia->codigo ?? '—' }}</td>
                                            <td>{{ $materia->intensidad_horaria ? $materia->intensidad_horaria . ' h/sem' : '—' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('academico.cursos.materias.show', [$curso, $materia]) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()?->hasAnyPermission(['gestionar_materias', 'acceso_total']))
                                                    <a href="{{ route('academico.cursos.materias.edit', [$curso, $materia]) }}" class="btn btn-warning btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('academico.cursos.materias.destroy', [$curso, $materia]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la materia {{ $materia->nombre }}?');">
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
                                            <td colspan="4" class="text-center text-muted py-4">Aún no hay materias asignadas a este curso.</td>
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
