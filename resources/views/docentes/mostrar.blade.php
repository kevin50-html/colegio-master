@extends('layouts.app')

@section('title', 'Detalle del Docente')

@section('content')
@php
    $menuActivo = 'docentes';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">{{ $docente->nombre_completo }}</h1>
                        <p class="text-muted mb-0">Información detallada del docente y sus asignaciones.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver al listado
                        </a>
                        @if(Auth::user()?->hasAnyPermission(['gestionar_docentes', 'acceso_total']))
                            <a href="{{ route('docentes.editar', $docente) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>Editar docente
                            </a>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h5 mb-3">Datos generales</h2>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Documento</p>
                                        <p class="mb-0">{{ $docente->documento_identidad }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Correo electrónico</p>
                                        <p class="mb-0">{{ $docente->email ?? 'Sin correo registrado' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Teléfono</p>
                                        <p class="mb-0">{{ $docente->telefono ?? 'Sin teléfono' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Especialidad</p>
                                        <p class="mb-0">{{ $docente->especialidad ?? 'Sin definir' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Fecha de ingreso</p>
                                        <p class="mb-0">{{ optional($docente->fecha_ingreso)->format('Y-m-d') ?? '—' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="text-muted small mb-1">Estado</p>
                                        <span class="badge @class(['bg-success' => $docente->estado === 'activo', 'bg-secondary' => $docente->estado === 'inactivo', 'bg-warning text-dark' => $docente->estado === 'suspendido'])">
                                            {{ ucfirst($docente->estado) }}
                                        </span>
                                    </div>
                                </div>

                                <hr>

                                <h2 class="h5 mb-3">Observaciones</h2>
                                <p class="mb-0">{{ $docente->observaciones ?? 'Sin observaciones registradas.' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body p-4">
                                <h2 class="h5 mb-3">Cursos asignados</h2>
                                @if($docente->cursos->isEmpty())
                                    <p class="text-muted mb-0">Este docente aún no tiene cursos asignados.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($docente->cursos as $curso)
                                            <li class="list-group-item px-0">
                                                <i class="fas fa-book me-2 text-primary"></i>{{ $curso->nombre }}
                                            </li>
                                        @endforeach
                                    </ul>
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
