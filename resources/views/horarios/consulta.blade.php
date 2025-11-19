@extends('layouts.app')

@section('title', 'Consulta de horario ' . $curso->nombre)

@php
    $menuActivo = 'horarios';
    $rolActual = isset($rolActual) ? $rolActual : (Auth::check() ? App\Models\RolesModel::find(Auth::user()->roles_id) : null);
    $docentesCurso = $curso->docentes ?? collect();
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
                            <h1 class="h3 mb-1">{{ $curso->nombre }} - Consulta de horario</h1>
                            <p class="text-muted mb-0">Visualiza el horario completo del curso con materias, aulas y docentes asignados.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('horarios.curso', $curso) }}" class="btn btn-outline-primary">
                                <i class="fas fa-sliders-h me-1"></i> Gestionar bloques
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
                                    <p class="text-muted text-uppercase small mb-1">Bloques programados</p>
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

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Docentes del curso</h5>
                                    <small class="text-muted">Listado de profesores asignados al curso.</small>
                                </div>
                                <div class="card-body">
                                    @if($docentesCurso->isEmpty())
                                        <p class="text-muted mb-0">No hay docentes registrados para este curso.</p>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($docentesCurso as $docente)
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="fw-semibold">{{ $docente->nombre_completo }}</div>
                                                        <small class="text-muted">{{ $docente->email ?? 'Sin correo' }}</small>
                                                    </div>
                                                    <span class="badge bg-light text-muted">{{ ucfirst($docente->estado ?? 'activo') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Resumen por día</h5>
                                        <small class="text-muted">Bloques ordenados según el día de la semana.</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($totalHorarios === 0)
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                            <p class="mb-0">No hay clases programadas todavía.</p>
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
                                                                            <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                                            <div class="text-muted small">{{ $horario->hora_inicio?->format('H:i') }} - {{ $horario->hora_fin?->format('H:i') }}</div>
                                                                            <div class="text-muted small">Aula: {{ $horario->aula ?? 'Sin asignar' }}</div>
                                                                            @if($registro['alias'])
                                                                                <div class="text-muted small">Alias: {{ $registro['alias'] }}</div>
                                                                            @endif
                                                                            @if($horario->periodo)
                                                                                <div class="text-muted small">Periodo: {{ $horario->periodo->nombre }}</div>
                                                                            @endif
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

                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0">Detalle completo del horario</h5>
                                <small class="text-muted">Listado consolidado con materia, aula, periodo y docentes.</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">{{ $totalHorarios }} bloques</span>
                        </div>
                        <div class="card-body p-0">
                            @if($totalHorarios === 0)
                                <div class="p-4 text-center text-muted">No hay información registrada.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Materia</th>
                                                <th>Día</th>
                                                <th>Horario</th>
                                                <th>Aula</th>
                                                <th>Periodo</th>
                                                <th>Docente(s)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $docentesTexto = $docentesCurso->isNotEmpty() ? $docentesCurso->map->nombre_completo->join(', ') : 'Sin docentes asignados';
                                            @endphp
                                            @foreach($horariosPlano as $registro)
                                                @php
                                                    $horario = $registro['horario'];
                                                    $materia = $registro['materia'];
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $materia->nombre }}</div>
                                                        <small class="text-muted">{{ $registro['alias'] ?? 'Sin alias' }}</small>
                                                    </td>
                                                    <td>{{ $horario->dia ?? 'Sin día' }}</td>
                                                    <td>{{ $horario->hora_inicio?->format('H:i') }} - {{ $horario->hora_fin?->format('H:i') }}</td>
                                                    <td>{{ $horario->aula ?? 'Sin asignar' }}</td>
                                                    <td>{{ $horario->periodo?->nombre ?? '—' }}</td>
                                                    <td>{{ $docentesTexto }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
