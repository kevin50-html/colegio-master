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
                    <h1 class="h3 mb-0">Editar usuario</h1>
                    <div class="btn-group" role="group">
                        <a href="{{ route('usuarios.mostrar', $usuario) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i>Ver perfil
                        </a>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('usuarios.actualizar', $usuario) }}" method="POST" class="card shadow-sm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="roles_id" class="form-label">Rol del sistema</label>
                                <select name="roles_id" id="roles_id" class="form-select" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" @selected(old('roles_id', $usuario->roles_id) == $rol->id)>{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Solo si cambias la contraseña">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">Restablecer</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar usuario
                        </button>
                    </div>
                </form>

                @if(Auth::id() === $usuario->id)
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-1"></i>Recuerda que no puedes eliminar tu propia cuenta desde este módulo.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
