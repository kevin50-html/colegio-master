
@extends('layouts.app')

@section('content')
@php
    $menuActivo = 'roles';
@endphp
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <h1>Detalles del rol: {{ $rol->nombre }}</h1>
                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <div>{{ $rol->descripcion }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Permisos por módulo</label>
                    <div class="row g-3">
                        @foreach($gruposPermisos as $modulo => $permisos)
                            @php
                                $permisosSeleccionados = array_intersect(array_keys($permisos), $rol->permisos ?? []);
                            @endphp
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                        <span>{{ $modulo }}</span>
                                        <small class="text-muted">{{ count($permisosSeleccionados) }}/{{ count($permisos) }} seleccionados</small>
                                    </div>
                                    <div class="card-body">
                                        @if(count($permisosSeleccionados))
                                            <ul class="mb-0">
                                                @foreach($permisosSeleccionados as $permiso)
                                                    <li>{{ $permisosDisponibles[$permiso] ?? $permiso }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Sin permisos seleccionados en este módulo</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <h3>Usuarios asignados a este rol</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $usuarios->links() }}
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Volver a la lista</a>
                <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-warning">Editar rol</a>
            </div>
        </div>
    </div>
</div>
@endsection
