@extends('layouts.app')

@section('title', 'Editar horario - ' . $cursoMateria->curso->nombre . ' / ' . $cursoMateria->materia->nombre)

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
                <div class="mb-4">
                    <a href="{{ route('academico.curso-materias.horarios.index', $cursoMateria) }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver a horarios
                    </a>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Editar horario</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.curso-materias.horarios.update', [$cursoMateria, $horario]) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label for="dia" class="form-label">Día</label>
                                <select name="dia" id="dia" class="form-select @error('dia') is-invalid @enderror" required>
                                    @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $dia)
                                        <option value="{{ $dia }}" @selected(old('dia', $horario->dia) === $dia)>{{ $dia }}</option>
                                    @endforeach
                                </select>
                                @error('dia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicio" class="form-label">Hora inicio</label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', $horario->hora_inicio) }}" required>
                                @error('hora_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="hora_fin" class="form-label">Hora fin</label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', $horario->hora_fin) }}" required>
                                @error('hora_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="periodo_id" class="form-label">Periodo (opcional)</label>
                                <select name="periodo_id" id="periodo_id" class="form-select @error('periodo_id') is-invalid @enderror">
                                    <option value="">Sin periodo específico</option>
                                    @foreach($periodos as $periodo)
                                        <option value="{{ $periodo->id }}" @selected(old('periodo_id', $horario->periodo_id) == $periodo->id)>{{ $periodo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('periodo_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="aula" class="form-label">Aula</label>
                                <input type="text" name="aula" id="aula" class="form-control @error('aula') is-invalid @enderror" value="{{ old('aula', $horario->aula) }}" placeholder="Ubicación opcional">
                                @error('aula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
