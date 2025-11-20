@extends('layouts.app')

@section('title', 'Detalle de asignación de materia')

@section('content')
@php
    $menuActivo = 'curso-materias';
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
                        <h1 class="h3 mb-1">Detalle de asignación</h1>
                        <p class="text-muted mb-0">Consulta la información de la materia asociada al curso.</p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <a href="{{ route('curso-materias.edit', $cursoMateria) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('curso-materias.index', ['curso' => $cursoMateria->curso_id]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Volver al listado
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 col-lg-3 text-muted">Curso</dt>
                            <dd class="col-sm-8 col-lg-9 fw-semibold">{{ $cursoMateria->curso->nombre }}</dd>

                            <dt class="col-sm-4 col-lg-3 text-muted">Materia</dt>
                            <dd class="col-sm-8 col-lg-9 fw-semibold">{{ $cursoMateria->materia->nombre }}</dd>

                            <dt class="col-sm-4 col-lg-3 text-muted">Código de la materia</dt>
                            <dd class="col-sm-8 col-lg-9">{{ $cursoMateria->materia->codigo ?? 'No registrado' }}</dd>

                            <dt class="col-sm-4 col-lg-3 text-muted">Alias</dt>
                            <dd class="col-sm-8 col-lg-9">{{ $cursoMateria->alias ?: 'Sin alias asignado' }}</dd>

                            <dt class="col-sm-4 col-lg-3 text-muted">Fecha de asignación</dt>
                            <dd class="col-sm-8 col-lg-9">{{ optional($cursoMateria->created_at)->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4 col-lg-3 text-muted">Última actualización</dt>
                            <dd class="col-sm-8 col-lg-9">{{ optional($cursoMateria->updated_at)->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
