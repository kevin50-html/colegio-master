@extends('layouts.app')

@section('title', 'Detalle Estudiante')

@section('content')
@php
    $menuActivo = 'estudiantes';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4">
                    <div>
                        <h1 class="h3 mb-1">{{ $estudiante->nombre_completo }}</h1>
                        <p class="text-muted mb-0">Documento: {{ $estudiante->documento_identidad }}</p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('estudiantes.editar', $estudiante) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i>Volver al listado
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h2 class="h5 mb-0">Información general</h2>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Nombre completo:</strong>
                                        <div>{{ $estudiante->nombre_completo }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Documento:</strong>
                                        <div>{{ $estudiante->documento_identidad }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Correo electrónico:</strong>
                                        <div>{{ $estudiante->email ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Teléfono:</strong>
                                        <div>{{ $estudiante->telefono ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Curso asignado:</strong>
                                        <div>{{ optional($estudiante->curso)->nombre ?? 'Sin asignar' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Estado:</strong>
                                        <div>
                                            <span class="badge @class(['bg-success' => $estudiante->estado === 'activo', 'bg-secondary' => $estudiante->estado === 'inactivo', 'bg-info text-dark' => $estudiante->estado === 'egresado'])">
                                                {{ ucfirst($estudiante->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Fecha de matrícula:</strong>
                                        <div>{{ optional($estudiante->fecha_matricula)->format('Y-m-d') ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Usuario asociado:</strong>
                                        <div>{{ optional($estudiante->usuario)->email ?? 'Sin cuenta asignada' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong>Observaciones:</strong>
                                        <div class="border rounded p-3 bg-light">{{ $estudiante->observaciones ?? 'Sin observaciones registradas.' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h2 class="h5 mb-0">Acciones rápidas</h2>
                            </div>
                            <div class="card-body d-grid gap-2">
                                <a href="{{ route('matriculas.index', ['q' => $estudiante->documento_identidad]) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt me-1"></i>Ver matrículas relacionadas
                                </a>
                                <a href="{{ route('estudiantes.editar', $estudiante) }}" class="btn btn-primary">
                                    <i class="fas fa-pen me-1"></i>Actualizar datos
                                </a>
                                <form action="{{ route('estudiantes.eliminar', $estudiante) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $estudiante->nombre_completo }}? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash-alt me-1"></i>Eliminar estudiante
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
