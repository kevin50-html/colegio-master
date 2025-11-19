@extends('layouts.app')

@section('title', 'Horarios del curso ' . $curso->nombre)

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
                            <h1 class="h3 mb-1">{{ $curso->nombre }}</h1>
                            <p class="text-muted mb-0">Gestiona los bloques de clase y consulta el horario consolidado del curso.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('horarios.consulta', $curso) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Vista de consulta
                            </a>
                            <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a cursos
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
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
                                    <p class="text-muted text-uppercase small mb-1">Bloques creados</p>
                                    <h4 class="fw-bold mb-0">{{ $totalHorarios }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <p class="text-muted text-uppercase small mb-1">Estudiantes</p>
                                    <h4 class="fw-bold mb-0">{{ $curso->estudiantes_count }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Nuevo bloque</h5>
                                    <small class="text-muted">Define día, horas y aula para una materia del curso.</small>
                                </div>
                                <div class="card-body">
                                    @if($cursoMaterias->isEmpty())
                                        <div class="alert alert-info mb-0">
                                            Este curso no tiene materias asignadas. Agrega materias desde el módulo correspondiente.
                                        </div>
                                    @else
                                        <form action="{{ route('horarios.store', $curso) }}" method="POST" class="row g-3">
                                            @csrf
                                            <div class="col-12">
                                                <label for="curso_materia_id" class="form-label">Materia</label>
                                                <select name="curso_materia_id" id="curso_materia_id" class="form-select @error('curso_materia_id') is-invalid @enderror" required>
                                                    <option value="">Selecciona una materia</option>
                                                    @foreach($cursoMaterias as $asignacion)
                                                        <option value="{{ $asignacion->id }}" data-materia="{{ $asignacion->materia_id }}" @selected(old('curso_materia_id') == $asignacion->id)>
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
                                                            <option value="{{ $periodo->id }}" data-materia="{{ $materiaId }}" @selected(old('periodo_id') == $periodo->id)>
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
                                                        <option value="{{ $dia }}" @selected(old('dia') == $dia)> {{ $dia }}</option>
                                                    @endforeach
                                                </select>
                                                @error('dia')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="aula" class="form-label">Aula (opcional)</label>
                                                <input type="text" name="aula" id="aula" class="form-control @error('aula') is-invalid @enderror" value="{{ old('aula') }}" placeholder="Ej. 302">
                                                @error('aula')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="hora_inicio" class="form-label">Hora inicio</label>
                                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
                                                @error('hora_inicio')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="hora_fin" class="form-label">Hora fin</label>
                                                <input type="time" name="hora_fin" id="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin') }}" required>
                                                @error('hora_fin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Guardar bloque
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Horario del curso</h5>
                                        <small class="text-muted">Bloques agrupados por día de la semana.</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">{{ $totalHorarios }} bloques</span>
                                </div>
                                <div class="card-body">
                                    @if($totalHorarios === 0)
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                            <p class="mb-0">Aún no se han definido bloques para este curso.</p>
                                        </div>
                                    @else
                                        <div class="row g-3">
                                            @foreach($diasSemana as $diaSemana)
                                                <div class="col-12 col-md-6">
                                                    <div class="card h-100 border-0 shadow-sm">
                                                        <div class="card-header bg-primary-subtle text-primary fw-semibold">
                                                            {{ $diaSemana }}
                                                        </div>
                                                        @php
                                                            $bloques = $horariosPorDia->get($diaSemana, collect());
                                                        @endphp
                                                        <div class="card-body p-0">
                                                            @if($bloques->isEmpty())
                                                                <div class="p-3 text-center text-muted small">Sin clases programadas.</div>
                                                            @else
                                                                <ul class="list-group list-group-flush">
                                                                    @foreach($bloques as $registro)
                                                                        @php
                                                                            $horario = $registro['horario'];
                                                                            $materia = $registro['materia'];
                                                                        @endphp
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                                                                <div>
                                                                                    <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                                                    <div class="text-muted small">
                                                                                        {{ $horario->hora_inicio?->format('H:i') }} - {{ $horario->hora_fin?->format('H:i') }}
                                                                                        @if($registro['alias'])
                                                                                            <span class="ms-1">({{ $registro['alias'] }})</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="text-muted small">Aula: {{ $horario->aula ?? 'Sin asignar' }}</div>
                                                                                    @if($horario->periodo)
                                                                                        <div class="text-muted small">Periodo: {{ $horario->periodo->nombre }}</div>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="d-flex gap-2">
                                                                                    <a href="{{ route('horarios.edit', ['curso' => $curso, 'horario' => $horario]) }}"
                                                                                       class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center"
                                                                                       style="width: 2.25rem; height: 2.25rem;"
                                                                                       title="Editar bloque">
                                                                                        <i class="fas fa-pen"></i>
                                                                                    </a>
                                                                                    <form action="{{ route('horarios.destroy', ['curso' => $curso, 'horario' => $horario]) }}" method="POST" class="text-end" onsubmit="return confirm('¿Deseas eliminar este bloque horario?');">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center"
                                                                                                style="width: 2.25rem; height: 2.25rem;">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
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
