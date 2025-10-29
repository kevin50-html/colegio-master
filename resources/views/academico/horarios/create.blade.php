@extends('layouts.app')

@section('title', 'Nuevo Horario - ' . $periodo->nombre)

@section('content')
@php
    $menuActivo = 'academico';
    $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="mb-4">
                    <a href="{{ route('academico.materias.periodos.show', [$periodo->materia, $periodo]) }}" class="btn btn-link text-decoration-none p-0">
                        <i class="fas fa-arrow-left me-1"></i>Volver al periodo
                    </a>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Nuevo horario</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('academico.periodos.horarios.store', $periodo) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label for="dia_semana" class="form-label">Día de la semana</label>
                                <select name="dia_semana" id="dia_semana" class="form-select @error('dia_semana') is-invalid @enderror" required>
                                    <option value="">Selecciona un día</option>
                                    @foreach($diasSemana as $dia)
                                        <option value="{{ $dia }}" @selected(old('dia_semana') === $dia)>{{ $dia }}</option>
                                    @endforeach
                                </select>
                                @error('dia_semana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="hora_inicio" class="form-label">Hora de inicio</label>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
                                @error('hora_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="hora_fin" class="form-label">Hora de finalización</label>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin') }}" required>
                                @error('hora_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="aula" class="form-label">Aula</label>
                                <input type="text" name="aula" id="aula" class="form-control @error('aula') is-invalid @enderror" value="{{ old('aula') }}" placeholder="Ej: 201A">
                                @error('aula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="modalidad" class="form-label">Modalidad</label>
                                <input type="text" name="modalidad" id="modalidad" class="form-control @error('modalidad') is-invalid @enderror" value="{{ old('modalidad', 'Presencial') }}">
                                @error('modalidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar
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
