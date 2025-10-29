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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Perfil de usuario</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('usuarios.editar', $usuario) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user me-2"></i>Información básica
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Nombre:</strong> {{ $usuario->name }}</p>
                                <p class="mb-2"><strong>Correo:</strong> {{ $usuario->email }}</p>
                                <p class="mb-2"><strong>Rol asignado:</strong> {{ optional($usuario->rol)->nombre ?? 'Sin rol' }}</p>
                                <p class="mb-2"><strong>Fecha de creación:</strong> {{ optional($usuario->created_at)->format('d/m/Y H:i') }}</p>
                                <p class="mb-0"><strong>Última actualización:</strong> {{ optional($usuario->updated_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <i class="fas fa-info-circle me-2"></i>Acciones rápidas
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Utiliza los botones para gestionar esta cuenta.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('usuarios.editar', $usuario) }}" class="btn btn-primary">
                                        <i class="fas fa-user-edit me-1"></i>Actualizar datos
                                    </a>
                                    <form action="{{ route('usuarios.eliminar', $usuario) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta cuenta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" @if(Auth::id() === $usuario->id) disabled title="No puedes eliminar tu propia cuenta" @endif>
                                            <i class="fas fa-user-times me-1"></i>Eliminar usuario
                                        </button>
                                    </form>
                                </div>
                                @if(Auth::id() === $usuario->id)
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-info-circle me-1"></i>Estás viendo tu propio perfil. Algunas acciones están restringidas para evitar bloqueos.
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
