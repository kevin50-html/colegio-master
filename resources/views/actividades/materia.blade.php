@extends('layouts.app')

@section('title', 'Actividades de ' . $materia->nombre)

@php
    use Illuminate\Support\Str;

    $menuActivo = 'actividades';
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
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-1">{{ $materia->nombre }}</h1>
                            <p class="text-muted mb-0">Asocia actividades y tareas a esta materia.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('actividades.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a materias
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Periodos de la materia</h5>
                                    <small class="text-muted">Gestiona los periodos desde el módulo dedicado.</small>
                                </div>
                                <div class="card-body">
                                    @if($periodos->isEmpty())
                                        <div class="alert alert-info">No hay periodos configurados para esta materia.</div>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($periodos as $periodo)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="fw-semibold">{{ $periodo->nombre }}</span>
                                                        <span class="text-muted small">Orden {{ $periodo->orden ?? '-' }}</span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        @if($periodo->fecha_inicio || $periodo->fecha_fin)
                                                            {{ optional($periodo->fecha_inicio)->format('d/m/Y') ?? 'Sin inicio' }}
                                                            -
                                                            {{ optional($periodo->fecha_fin)->format('d/m/Y') ?? 'Sin fin' }}
                                                        @else
                                                            Fechas no definidas
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="d-grid mt-3">
                                        <a href="{{ route('periodos.materia', $materia) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-calendar-alt me-1"></i> Ir a periodos
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Nueva actividad</h5>
                                    <small class="text-muted">Registra tareas para esta materia.</small>
                                </div>
                                <div class="card-body">
                                    @if($periodos->isEmpty())
                                        <div class="alert alert-info mb-0">
                                            No hay periodos configurados para esta materia. Crea uno en el módulo de periodos.
                                        </div>
                                    @else
                                        <form action="{{ route('actividades.store', $materia) }}" method="POST" class="row g-3">
                                            @csrf
                                            <div class="col-12">
                                                <label for="periodo_id" class="form-label">Periodo</label>
                                                <select name="periodo_id" id="periodo_id" class="form-select @error('periodo_id') is-invalid @enderror" required>
                                                    <option value="">Selecciona un periodo</option>
                                                    @foreach($periodos as $periodo)
                                                        <option value="{{ $periodo->id }}" @selected(old('periodo_id') == $periodo->id)>
                                                            {{ $periodo->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('periodo_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label for="titulo" class="form-label">Título</label>
                                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" class="form-control @error('titulo') is-invalid @enderror" placeholder="Ej. Taller de lectura" required>
                                                @error('titulo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="fecha_entrega" class="form-label">Fecha de entrega</label>
                                                <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ old('fecha_entrega') }}" class="form-control @error('fecha_entrega') is-invalid @enderror">
                                                @error('fecha_entrega')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="porcentaje" class="form-label">Porcentaje</label>
                                                <div class="input-group">
                                                    <input type="number" name="porcentaje" id="porcentaje" value="{{ old('porcentaje') }}" class="form-control @error('porcentaje') is-invalid @enderror" min="1" max="100" placeholder="Ej. 20">
                                                    <span class="input-group-text">%</span>
                                                    @error('porcentaje')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label for="descripcion" class="form-label">Descripción</label>
                                                <textarea name="descripcion" id="descripcion" rows="3" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Detalles, instrucciones o materiales necesarios">{{ old('descripcion') }}</textarea>
                                                @error('descripcion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 d-grid">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i> Guardar actividad
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
                                        <h5 class="mb-0">Actividades registradas</h5>
                                        <small class="text-muted">Listado de tareas asociadas a la materia.</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">{{ $actividades->count() }} en total</span>
                                </div>
                                <div class="card-body p-0">
                                    @if(session('success'))
                                        <div class="alert alert-success m-3">{{ session('success') }}</div>
                                    @endif
                                    @if($errors->any())
                                        <div class="alert alert-danger m-3">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($actividades->isEmpty())
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                                            <p class="mb-0">Aún no hay actividades registradas para esta materia.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Título</th>
                                                        <th>Periodo</th>
                                                        <th>Entrega</th>
                                                        <th class="text-center">%</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($actividades as $actividad)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $actividad->titulo }}</div>
                                                                <div class="text-muted small">
                                                                    {{ $actividad->descripcion ? Str::limit($actividad->descripcion, 70) : 'Sin descripción' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="fw-semibold">{{ $actividad->periodo->nombre }}</div>
                                                                <div class="text-muted small">{{ $materia->nombre }}</div>
                                                            </td>
                                                            <td>{{ $actividad->fecha_entrega?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                                            <td class="text-center">{{ $actividad->porcentaje ? $actividad->porcentaje . '%' : 'N/D' }}</td>
                                                            <td class="text-end">
                                                                <form action="{{ route('actividades.destroy', $actividad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la actividad {{ $actividad->titulo }}?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
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
        </div>
    </div>
@endsection
