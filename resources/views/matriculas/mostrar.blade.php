@extends('layouts.app')

@section('title', 'Detalle Matrícula')

@php
    /** @var \App\Models\MatriculaAcudiente $matricula */
    $puedeGestionar = $puedeGestionar ?? false;
    $menuActivo = 'matriculas';
    $cursos = $cursos ?? collect();
    $moduloEstudiantesListo = $moduloEstudiantesListo ?? false;
    $estudianteRegistrado = $moduloEstudiantesListo ? $matricula->estudianteRegistro : null;
@endphp

@section('content')
@if($puedeGestionar)
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row mb-4">
                    <div>
                        <h1 class="h3 mb-1">Matrícula de {{ $matricula->nombres }} {{ $matricula->apellidos }}</h1>
                        <p class="text-muted mb-0">Documento: {{ $matricula->documento_identidad }}</p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('matriculas.index') }}" class="btn btn-outline-secondary">
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
                @unless($moduloEstudiantesListo)
                    <div class="alert alert-warning">
                        Debes ejecutar las migraciones más recientes (<code>php artisan migrate</code>) para habilitar la creación automática de estudiantes desde las matrículas.
                    </div>
                @endunless
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h2 class="h5 mb-0">Información general</h2>
                                <span class="badge @class([
                                    'bg-warning text-dark' => $matricula->estado === 'pendiente',
                                    'bg-success' => $matricula->estado === 'aprobada',
                                    'bg-danger' => $matricula->estado === 'rechazada',
                                ])">{{ ucfirst($matricula->estado) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Curso solicitado:</strong>
                                        <div>{{ optional($matricula->curso)->nombre ?? 'Por asignar' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Registrada:</strong>
                                        <div>{{ optional($matricula->created_at)->format('Y-m-d H:i') }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Email:</strong>
                                        <div>{{ $matricula->email ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Teléfono:</strong>
                                        <div>{{ $matricula->telefono ?? '—' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Acudiente:</strong>
                                        <div>{{ optional($matricula->acudiente)->name ?? 'Registrado por el sistema' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Documentos:</strong>
                                        <div>
                                            @if(is_array($matricula->documentos) && count($matricula->documentos))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($matricula->documentos as $ruta)
                                                        <li>
                                                            <a href="{{ route('matriculas.descargar', urlencode($ruta)) }}">
                                                                <i class="fas fa-file-download me-1"></i>{{ basename($ruta) }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">Sin archivos adjuntos.</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($estudianteRegistrado)
                                        <div class="col-12">
                                            <strong>Estudiante generado:</strong>
                                            <div class="border rounded p-3 bg-light">
                                                <div class="fw-semibold">{{ $estudianteRegistrado->nombre_completo }}</div>
                                                <div class="text-muted small">Curso: {{ optional($estudianteRegistrado->curso)->nombre ?? 'Por asignar' }}</div>
                                                <div class="text-muted small">Estado: {{ ucfirst($estudianteRegistrado->estado) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h2 class="h5 mb-0">Actualizar estado</h2>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('matriculas.actualizarEstado', $matricula) }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="estado" class="form-label">Estado</label>
                                        <select name="estado" id="estado" class="form-select">
                                            <option value="pendiente" @selected(old('estado', $matricula->estado) === 'pendiente')>Pendiente</option>
                                            <option value="aprobada" @selected(old('estado', $matricula->estado) === 'aprobada')>Aprobada</option>
                                            <option value="rechazada" @selected(old('estado', $matricula->estado) === 'rechazada')>Rechazada</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="curso_id" class="form-label">Curso asignado</label>
                                        <select name="curso_id" id="curso_id" class="form-select">
                                            <option value="">Selecciona un curso</option>
                                            @foreach($cursos as $curso)
                                                <option value="{{ $curso->id }}" @selected(old('curso_id', $matricula->curso_id) == $curso->id)>{{ $curso->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="fecha_matricula" class="form-label">Fecha de matrícula</label>
                                        <input type="date" name="fecha_matricula" id="fecha_matricula" value="{{ old('fecha_matricula', optional($estudianteRegistrado?->fecha_matricula ?? now())->format('Y-m-d')) }}" class="form-control">
                                    </div>
                                    <div>
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Notas internas sobre la aprobación">{{ old('observaciones', $estudianteRegistrado?->observaciones) }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container py-4">
    <h2 class="mb-3">Matrícula de {{ $matricula->nombres }} {{ $matricula->apellidos }}</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <strong>Curso:</strong> {{ optional($matricula->curso)->nombre }}
                </div>
                <div class="col-md-6">
                    <strong>Documento:</strong> {{ $matricula->documento_identidad }}
                </div>
                <div class="col-md-6">
                    <strong>Email:</strong> {{ $matricula->email ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Teléfono:</strong> {{ $matricula->telefono ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>Estado:</strong> {{ ucfirst($matricula->estado) }}
                </div>
                <div class="col-12">
                    <strong>Documentos:</strong>
                    @if(is_array($matricula->documentos) && count($matricula->documentos))
                        <ul class="mt-2">
                        @foreach($matricula->documentos as $ruta)
                            <li>
                                <a href="{{ route('matriculas.descargar', urlencode($ruta)) }}">Descargar {{ basename($ruta) }}</a>
                            </li>
                        @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Sin archivos adjuntos.</div>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('matriculas.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
