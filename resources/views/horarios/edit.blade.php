@extends('layouts.app')

@section('title', 'Editar bloque horario')

@php
    $menuActivo = 'horarios';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                @include('partials.sidebar', ['menuActivo' => $menuActivo])
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h1 class="h3 mb-1">Editar bloque</h1>
                            <p class="text-muted mb-0">Actualiza el bloque seleccionado del curso {{ $curso->nombre }}.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('horarios.curso', $curso) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver al curso
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Estudiantes</p>
                                    <h4 class="fw-bold mb-0">{{ $curso->estudiantes_count }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Materias vinculadas</p>
                                    <h4 class="fw-bold mb-0">{{ $cursoMaterias->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Bloque actual</p>
                                    <h4 class="fw-bold mb-0">{{ $horario->dia ?? 'Sin día' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Datos del bloque</h5>
                            <small class="text-muted">Modifica únicamente los campos necesarios.</small>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('horarios.update', ['curso' => $curso, 'horario' => $horario]) }}" method="POST" class="row g-3">
                                @csrf
                                @method('PUT')

                                <div class="col-12">
                                    <label for="curso_materia_id" class="form-label">Materia</label>
                                    <select name="curso_materia_id" id="curso_materia_id" class="form-select @error('curso_materia_id') is-invalid @enderror" required>
                                        <option value="">Selecciona una materia</option>
                                        @foreach($cursoMaterias as $asignacion)
                                            <option value="{{ $asignacion->id }}" data-materia="{{ $asignacion->materia_id }}" @selected(old('curso_materia_id', $horario->curso_materia_id) == $asignacion->id)>
                                                {{ $asignacion->materia->nombre }} {{ $asignacion->alias ? '(' . $asignacion->alias . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('curso_materia_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="periodo_id" class="form-label">Periodo (opcional)</label>
                                    <select name="periodo_id" id="periodo_id" class="form-select @error('periodo_id') is-invalid @enderror">
                                        <option value="">Sin periodo específico</option>
                                        @foreach($periodosPorMateria as $materiaId => $periodos)
                                            @foreach($periodos as $periodo)
                                                <option value="{{ $periodo->id }}" data-materia="{{ $materiaId }}" @selected(old('periodo_id', $horario->periodo_id) == $periodo->id)>
                                                    {{ $periodo->nombre }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('periodo_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Los periodos disponibles dependen de la materia seleccionada.</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="dia" class="form-label">Día</label>
                                    <select name="dia" id="dia" class="form-select @error('dia') is-invalid @enderror" required>
                                        <option value="">Selecciona un día</option>
                                        @foreach($diasSemana as $dia)
                                            <option value="{{ $dia }}" @selected(old('dia', $horario->dia) == $dia)>{{ $dia }}</option>
                                        @endforeach
                                    </select>
                                    @error('dia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="aula" class="form-label">Aula (opcional)</label>
                                    <input type="text" name="aula" id="aula" class="form-control @error('aula') is-invalid @enderror" value="{{ old('aula', $horario->aula) }}" placeholder="Ej. 302">
                                    @error('aula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="hora_inicio" class="form-label">Hora inicio</label>
                                    <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', optional($horario->hora_inicio)->format('H:i')) }}" required>
                                    @error('hora_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="hora_fin" class="form-label">Hora fin</label>
                                    <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', optional($horario->hora_fin)->format('H:i')) }}" required>
                                    @error('hora_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <a href="{{ route('horarios.curso', $curso) }}" class="btn btn-outline-secondary">
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Guardar cambios
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const materiaSelect = document.getElementById('curso_materia_id');
            const periodoSelect = document.getElementById('periodo_id');

            if (!materiaSelect || !periodoSelect) {
                return;
            }

            const togglePeriodos = () => {
                const selectedOption = materiaSelect.options[materiaSelect.selectedIndex];
                const materiaId = selectedOption ? selectedOption.dataset.materia : null;
                let hasVisible = false;

                periodoSelect.querySelectorAll('option[data-materia]').forEach((option) => {
                    const match = !materiaId || option.dataset.materia === materiaId;
                    option.hidden = !match;
                    option.disabled = !match;
                    if (match) {
                        hasVisible = true;
                    }
                });

                if (!hasVisible) {
                    periodoSelect.value = '';
                }
            };

            materiaSelect.addEventListener('change', togglePeriodos);
            togglePeriodos();
        });
    </script>
@endpush
