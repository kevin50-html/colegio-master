@extends('layouts.app')

@section('content')
@php
    $menuActivo = 'usuarios';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 p-0">
            @include('partials.sidebar', ['menuActivo' => $menuActivo])
        </div>
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <h1 class="h3 mb-3 mb-md-0">Gestión de Usuarios</h1>
                    <a href="{{ route('usuarios.crear') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>Nuevo Usuario
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="GET" action="{{ route('usuarios.index') }}" class="row row-cols-lg-auto g-2 align-items-center mb-3">
                    <div class="col-12 col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ $busqueda }}" class="form-control" placeholder="Buscar por nombre o correo">
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <button type="submit" class="btn btn-outline-secondary">Buscar</button>
                    </div>
                    @if($busqueda)
                        <div class="col-12 col-lg-auto">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-link text-decoration-none">Limpiar</a>
                        </div>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Creado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $usuario->name }}</div>
                                        <div class="text-muted small">ID: {{ $usuario->id }}</div>
                                    </td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ optional($usuario->rol)->nombre ?? 'Sin rol asignado' }}</td>
                                    <td>{{ optional($usuario->created_at)->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('usuarios.mostrar', $usuario) }}" class="btn btn-info btn-sm me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('usuarios.editar', $usuario) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('usuarios.eliminar', $usuario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" @if(Auth::id() === $usuario->id) disabled title="No puedes eliminar tu propia cuenta" @endif onclick="return confirm('¿Deseas eliminar a {{ $usuario->name }}?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No se encontraron usuarios con los criterios ingresados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
