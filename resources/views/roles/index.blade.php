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
                <h1 class="mb-4">Lista de Roles</h1>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <a href="{{ route('roles.crear') }}" class="btn btn-primary mb-3">Crear nuevo rol</a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Usuarios asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                            <tr>
                                <td>{{ $rol->nombre }}</td>
                                <td>{{ $rol->descripcion }}</td>
                                <td>{{ $rol->usuarios_count }}</td>
                                <td>
                                    <a href="{{ route('roles.mostrar', $rol->id) }}" class="btn btn-info btn-sm">Ver</a>
                                    <a href="{{ route('roles.editar', $rol->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                    <form action="{{ route('roles.eliminar', $rol->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este rol?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
