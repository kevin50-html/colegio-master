@php
    $usuarioActual = Auth::user();
    $rolActual = isset($rolActual) ? $rolActual : ($usuarioActual ? App\Models\RolesModel::find($usuarioActual->roles_id) : null);
    $menuActivo = $menuActivo ?? '';
@endphp
<div class="sidebar">
    <div class="p-3">
        <h6 class="text-white-50 text-uppercase">Menú Principal</h6>
    </div>
    <nav class="nav flex-column px-3">
        <a class="nav-link {{ $menuActivo === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
        @if($rolActual && $rolActual->nombre === 'Acudiente')
            <a class="nav-link {{ $menuActivo === 'matriculas' ? 'active' : '' }}" href="{{ route('matriculas.index') }}">
                <i class="fas fa-folder-open me-2"></i>Mis Matrículas
            </a>
        @endif
        @if($rolActual)
            @if($rolActual->tienePermiso('gestionar_usuarios') || $rolActual->tienePermiso('acceso_total'))
                <a class="nav-link {{ $menuActivo === 'usuarios' ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                    <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_estudiantes') || $rolActual->tienePermiso('ver_estudiantes') || $rolActual->tienePermiso('acceso_total'))
                <a class="nav-link {{ $menuActivo === 'estudiantes' ? 'active' : '' }}" href="{{ route('estudiantes.index') }}">
                    <i class="fas fa-user-graduate me-2"></i>Estudiantes
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_estudiantes') || $rolActual->tienePermiso('matricular_estudiantes') || $rolActual->tienePermiso('acceso_total'))
                <a class="nav-link {{ $menuActivo === 'matriculas' && $rolActual->nombre !== 'Acudiente' ? 'active' : '' }}" href="{{ route('matriculas.index') }}">
                    <i class="fas fa-user-check me-2"></i>Matrículas
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_docentes') || $rolActual->tienePermiso('ver_docentes') || $rolActual->tienePermiso('acceso_total'))
                <a class="nav-link {{ $menuActivo === 'docentes' ? 'active' : '' }}" href="{{ route('docentes.index') }}">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Docentes
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_roles'))
                <a class="nav-link {{ $menuActivo === 'roles' ? 'active' : '' }}" href="{{ route('roles.index') }}">
                    <i class="fas fa-user-shield me-2"></i>Roles y Permisos
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_cursos') || $rolActual->tienePermiso('gestionar_materias') || $rolActual->tienePermiso('gestionar_periodos') || $rolActual->tienePermiso('gestionar_horarios') || $rolActual->tienePermiso('crear_actividades') || $rolActual->tienePermiso('registrar_notas') || $rolActual->tienePermiso('ver_cursos') || $rolActual->tienePermiso('ver_materias'))
                <a class="nav-link {{ $menuActivo === 'academico' ? 'active' : '' }}" href="{{ route('academico.cursos.index') }}">
                    <i class="fas fa-school me-2"></i>Gestión Académica
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_disciplina'))
                <a class="nav-link" href="#">
                    <i class="fas fa-gavel me-2"></i>Disciplina
                </a>
            @endif
            @if($rolActual->tienePermiso('ver_reportes_generales'))
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar me-2"></i>Reportes
                </a>
            @endif
            @if($rolActual->tienePermiso('gestionar_pagos'))
                <a class="nav-link" href="#">
                    <i class="fas fa-money-bill-wave me-2"></i>Pagos
                </a>
            @endif
            @if($rolActual->tienePermiso('configurar_sistema'))
                <a class="nav-link" href="#">
                    <i class="fas fa-cog me-2"></i>Configuración
                </a>
            @endif
            @if($rolActual && $rolActual->nombre === 'Acudiente')
                <a class="nav-link {{ $menuActivo === 'matriculas-subir' ? 'active' : '' }}" href="{{ route('matriculas.crear') }}">
                    <i class="fas fa-file-upload me-2"></i>Matrícula (Cargar Documentos)
                </a>
            @endif
        @endif
    </nav>
</div>
