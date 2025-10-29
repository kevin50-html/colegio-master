@php
    $usuarioActual = Auth::user();
    $rolActual = isset($rolActual) ? $rolActual : ($usuarioActual ? App\Models\RolesModel::find($usuarioActual->roles_id) : null);
    $menuActivo = $menuActivo ?? '';
    $rutaActual = request()->route()?->getName();
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
                <a class="nav-link {{ $menuActivo === 'academico' && $rutaActual === 'academico.index' ? 'active' : '' }}" href="{{ route('academico.index') }}">
                    <i class="fas fa-school me-2"></i>Gestión Académica
                </a>
                <div class="nav flex-column ms-4 ps-2 border-start border-secondary-subtle">
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.cursos.*') ? 'active' : 'text-white-50' }}" href="{{ route('academico.cursos.index') }}">
                        <i class="fas fa-layer-group me-2"></i>Cursos
                    </a>
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.modulos.materias') || request()->routeIs('academico.materias.*') || request()->routeIs('academico.cursos.materias.*') || request()->routeIs('academico.curso-materias.*') ? 'active' : 'text-white-50' }}" href="{{ route('academico.modulos.materias') }}">
                        <i class="fas fa-book me-2"></i>Materias
                    </a>
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.modulos.periodos') || request()->routeIs('academico.curso-materias.periodos.*') || request()->routeIs('academico.periodos.*') ? 'active' : 'text-white-50' }}" href="{{ route('academico.modulos.periodos') }}">
                        <i class="fas fa-calendar-alt me-2"></i>Periodos
                    </a>
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.modulos.horarios') || request()->routeIs('academico.curso-materias.horarios.*') ? 'active' : 'text-white-50' }}" href="{{ route('academico.modulos.horarios') }}">
                        <i class="fas fa-clock me-2"></i>Horarios
                    </a>
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.modulos.actividades') || request()->routeIs('academico.periodos.actividades.*') || request()->routeIs('academico.actividades.notas.*') ? 'active' : 'text-white-50' }}" href="{{ route('academico.modulos.actividades') }}">
                        <i class="fas fa-tasks me-2"></i>Actividades
                    </a>
                    <a class="nav-link ps-2 small {{ request()->routeIs('academico.modulos.cursos-materias') ? 'active' : 'text-white-50' }}" href="{{ route('academico.modulos.cursos-materias') }}">
                        <i class="fas fa-project-diagram me-2"></i>Cursos por materias
                    </a>
                </div>
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
