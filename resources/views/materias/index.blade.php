@extends('layouts.app')

@section('title', 'Materias')

@section('content')
@php
    $menuActivo = 'materias';
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
                        <h1 class="h3 mb-1">Materias</h1>
                        <p class="text-muted mb-0">Gestiona el catálogo de materias del colegio.</p>
                    </div>
                    <a href="{{ route('materias.create') }}" class="btn btn-primary mt-3 mt-md-0">
                        <i class="fas fa-plus me-1"></i> Nueva materia
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('materias.index') }}" class="row g-2 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="buscar" class="form-label">Buscar</label>
                        <input type="text" name="q" id="buscar" value="{{ $search }}" class="form-control" placeholder="Nombre, código o descripción">
                    </div>
                    <div class="col-md-6 d-flex">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('materias.index') }}" class="btn btn-link text-decoration-none">
                            Limpiar
                        </a>
                    </div>
                </form>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Intensidad (h/sem)</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materias as $materia)
                                        <tr>
                                            <td class="fw-semibold">{{ $materia->nombre }}</td>
                                            <td>{{ $materia->codigo }}</td>
                                            <td>{{ $materia->intensidad_horaria }}</td>
                                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($materia->descripcion, 80) }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('materias.show', $materia) }}" class="btn btn-sm btn-outline-secondary me-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('materias.edit', $materia) }}" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('materias.destroy', $materia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Deseas eliminar la materia {{ $materia->nombre }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No se encontraron materias registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($materias->hasPages())
                        <div class="card-footer bg-white">
                            {{ $materias->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
