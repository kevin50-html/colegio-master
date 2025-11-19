@extends('layouts.app')

@section('title', 'Períodos de ' . $materia->nombre)

@php
    $menuActivo = 'periodos';
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
                            <p class="text-muted mb-0">Configura los períodos de esta materia.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('periodos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a materias
                            </a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0">Nuevo período</h5>
                                    <small class="text-muted">Asocia períodos para habilitar actividades.</small>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('periodos.store', $materia) }}" method="POST" class="row g-3">
                                        @csrf
                                        <div class="col-12">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" placeholder="Ej. Primer período" required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="fecha_inicio" class="form-label">Fecha inicio</label>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" class="form-control @error('fecha_inicio') is-invalid @enderror" required>
                                            @error('fecha_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="fecha_fin" class="form-label">Fecha fin</label>
                                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}" class="form-control @error('fecha_fin') is-invalid @enderror" required>
                                            @error('fecha_fin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="orden" class="form-label">Orden</label>
                                            <input type="number" name="orden" id="orden" value="{{ old('orden') }}" class="form-control @error('orden') is-invalid @enderror" min="1" placeholder="Ej. 1" required>
                                            @error('orden')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Guardar período
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">Períodos registrados</h5>
                                        <small class="text-muted">Listado de períodos asociados a la materia.</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">{{ $periodos->count() }} en total</span>
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

                                    @if($periodos->isEmpty())
                                        <div class="p-4 text-center text-muted">
                                            <i class="fas fa-calendar-alt fa-2x mb-3"></i>
                                            <p class="mb-0">Aún no hay períodos registrados para esta materia.</p>
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Rango</th>
                                                        <th>Orden</th>
                                                        <th class="text-end">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($periodos as $periodo)
                                                        <tr>
                                                            <td>{{ $periodo->nombre }}</td>
                                                            <td>
                                                                @if($periodo->fecha_inicio || $periodo->fecha_fin)
                                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                                        {{ optional($periodo->fecha_inicio)->format('d/m/Y') ?? 'Sin inicio' }}
                                                                        -
                                                                        {{ optional($periodo->fecha_fin)->format('d/m/Y') ?? 'Sin fin' }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">Fechas no definidas</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $periodo->orden ?? '-' }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('periodos.edit', $periodo) }}" class="btn btn-sm btn-outline-primary me-2">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('periodos.destroy', $periodo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar este período?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="fas fa-trash-alt"></i>
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
