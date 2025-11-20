# Auditoría y documentación del sistema "colegio-master"

## 1. Resumen ejecutivo
- La aplicación es un panel escolar construido con Laravel 11, Blade y Vite. Cubre autenticación, registro público, gestión académica (cursos, materias, periodos, horarios, actividades y calificaciones) y trámites de matrícula para acudientes.
- El backend se apoya en Eloquent para modelar relaciones entre usuarios, roles, estudiantes, docentes, cursos y matrículas. Las rutas están concentradas en `routes/web.php` y todas las secciones de negocio se exponen tras el middleware `auth`. 【F:routes/web.php†L4-L125】
- El frontend está compuesto por vistas Blade organizadas por dominio funcional, un layout principal (`resources/views/layouts/app.blade.php`) y un sidebar compartido (`resources/views/partials/sidebar.blade.php`). Tailwind se usa como hoja de estilos base mediante Vite. 【F:resources/views/layouts/app.blade.php†L1-L40】【F:resources/css/app.css†L1-L12】
- Se eliminó código muerto: la vista `welcome.blade.php` (sin rutas asociadas) y el comando `inspire` de consola. No había referencias activas a dichos artefactos. 【F:routes/console.php†L1-L5】

## 2. Metodología de auditoría
1. Recorrido de carpetas de aplicación, configuración, recursos y pruebas.
2. Revisión de cada controlador y modelo para documentar responsabilidades, dependencias y validaciones.
3. Inventario de vistas, assets y rutas para relacionarlos con los módulos de negocio.

## 3. Inventario de carpetas clave
| Carpeta | Rol dentro del sistema |
| --- | --- |
| `app/Http/Controllers` | Controladores HTTP para autenticación, usuarios, roles, académicos y matrículas. |
| `app/Models` | Modelos Eloquent que representan entidades como cursos, materias, periodos, horarios, actividades, notas, estudiantes, docentes y roles. |
| `bootstrap`, `config`, `artisan` | Infraestructura estándar de Laravel (arranque y configuración). |
| `database/migrations` | Migraciones que crean tablas de usuarios, roles, matrículas, cursos, materias, periodos, actividades y notas. |
| `public` | Punto de entrada web (incluye `index.php` y assets compilados). |
| `resources/views` | Vistas Blade segmentadas por dominio (`usuarios`, `roles`, `matriculas`, etc.). |
| `resources/css` y `resources/js` | Activos gestionados por Vite (Tailwind y bootstrap.js para Axios). |
| `routes/web.php` | Rutas autenticadas y recursos REST. |
| `tests` | Pruebas de características básicas (`ExampleTest` verifica el flujo `/` → `/login`). |

## 4. Backend Laravel
### 4.1 Rutas web
`routes/web.php` redirige `/` a `/login`, define el flujo de autenticación y encapsula todas las pantallas funcionales tras `Route::middleware(['auth'])`. Se organizan prefijos para `usuarios`, `estudiantes`, `docentes`, `roles`, `matriculas`, `horarios`, `notas` y `consulta-notas`. También se registran recursos REST para cursos, materias y la tabla pivote curso-materias. 【F:routes/web.php†L18-L122】

### 4.2 Controladores principales
| Controlador | Funciones clave |
| --- | --- |
| `AuthController` | Login/logout, dashboard contextual con resumen para acudientes (contador de matrículas pendientes). 【F:app/Http/Controllers/AuthController.php†L13-L53】 |
| `CrearUsuario` | Registro público básico que crea usuarios y los autentica inmediatamente. 【F:app/Http/Controllers/CrearUsuario.php†L9-L33】 |
| `UsuarioController` | CRUD completo de usuarios con validaciones, asignación de roles y protección mediante `puedeGestionarUsuarios`. 【F:app/Http/Controllers/UsuarioController.php†L13-L106】 |
| `RolController` | Administración de roles y permisos, AJAX para asignar/remover roles y catálogo de permisos agrupados. Incluye restricción para roles del sistema (no eliminables). 【F:app/Http/Controllers/RolController.php†L17-L208】 |
| `EstudianteController` | CRUD de estudiantes con comprobación de que las migraciones estén aplicadas (`asegurarModuloDisponible`) y filtros por estado. 【F:app/Http/Controllers/EstudianteController.php†L15-L140】 |
| `DocenteController` | CRUD de docentes con filtros por estado/curso y sincronización de cursos asignados. Valida permisos antes de cada acción. 【F:app/Http/Controllers/DocenteController.php†L15-L138】 |
| `CursoController` | CRUD de cursos, cargas de relaciones y conteos para resúmenes. Bloquea eliminación cuando hay relaciones activas. 【F:app/Http/Controllers/CursoController.php†L11-L78】 |
| `MateriaController` | CRUD de materias con filtros por nombre/código y validaciones de unicidad. Evita eliminar materias asignadas a cursos. 【F:app/Http/Controllers/MateriaController.php†L11-L69】 |
| `CursoMateriaController` | Gestiona la relación curso↔materia (selección de curso, listado de asignaciones, validación única por curso, alias). 【F:app/Http/Controllers/CursoMateriaController.php†L13-L96】 |
| `PeriodoController` | Registro y actualización de periodos académicos por materia, con validación de fechas y orden. 【F:app/Http/Controllers/PeriodoController.php†L11-L73】 |
| `ActividadController` | Alta/baja de actividades por periodo, búsquedas por materia y conteos para vistas. 【F:app/Http/Controllers/ActividadController.php†L13-L78】 |
| `HorarioController` | Generación y consulta de horarios por curso, validando días, horas y periodos asociados; agrupa bloques por día para las vistas. 【F:app/Http/Controllers/HorarioController.php†L13-L164】 |
| `MatriculaAcudienteController` | Flujo de matrículas para acudientes: subir documentos (FTP configurable), listar según permisos, aprobar/rechazar y convertir matrículas en registros de estudiantes. Incluye descarga segura de archivos. 【F:app/Http/Controllers/MatriculaAcudienteController.php†L13-L167】【F:app/Http/Controllers/MatriculaAcudienteController.php†L200-L263】 |
| `NotaController` | Registro de calificaciones por curso/materia, validación de pertenencia y borrado de notas al enviar valor `null`. 【F:app/Http/Controllers/NotaController.php†L13-L96】 |
| `NotaConsultaController` | Consulta de calificaciones para usuarios con permisos de lectura, cálculo de promedios por periodo y totales aprobados/reprobados. 【F:app/Http/Controllers/NotaConsultaController.php†L13-L108】 |

### 4.3 Modelos y relaciones Eloquent
| Modelo | Propósito y relaciones |
| --- | --- |
| `User` | Autenticación y relación `rol`; helpers `hasPermission`/`hasAnyPermission` para validar permisos desde los controladores. 【F:app/Models/User.php†L13-L67】 |
| `RolesModel` | Define permisos como arreglo, expone `usuarios()` y helper `tienePermiso`. También ofrece catálogo de roles del sistema. 【F:app/Models/RolesModel.php†L8-L35】 |
| `Curso` | Relaciona docentes, estudiantes, matrículas y materias (vía pivot) y expone colección `cursoMaterias`. 【F:app/Models/Curso.php†L11-L41】 |
| `Materia` | Define atributos académicos y relaciones `cursos`, `cursoMaterias`, `periodos`. 【F:app/Models/Materia.php†L11-L33】 |
| `CursoMateria` | Pivot enriquecido entre curso y materia con alias y accesos a `periodos` y `horarios`. 【F:app/Models/CursoMateria.php†L11-L35】 |
| `Periodo` | Rango temporal por materia; expone actividades y horarios ordenados. 【F:app/Models/Periodo.php†L11-L33】 |
| `Actividad` | Trabajo evaluable ligado a un periodo (opcionalmente a un horario) y relación `notas`. 【F:app/Models/Actividad.php†L11-L34】 |
| `Nota` | Calificación de estudiante por actividad, casteo decimal. 【F:app/Models/Nota.php†L11-L31】 |
| `Horario` | Bloques de horario por curso-materia y periodo; detecta dinámicamente si la columna del día se llama `dia` o `dia_semana`. 【F:app/Models/Horario.php†L11-L53】 |
| `Estudiante` | Información académica del alumno, relación con `User`, `Curso` y `Nota`, más accesor `nombre_completo`. 【F:app/Models/Estudiante.php†L11-L39】 |
| `Docente` | Datos del profesor, relación con cursos y accesor `nombre_completo`. 【F:app/Models/Docente.php†L11-L39】 |
| `MatriculaAcudiente` | Solicitudes de matrícula, documentos almacenados como JSON y enlaces a acudiente, curso y registro aprobado. 【F:app/Models/MatriculaAcudiente.php†L11-L39】 |

## 5. Vistas y capa de presentación
- `resources/views/layouts/app.blade.php`: Layout base con `<head>` dinámico, carga de Vite y slots `@yield('title')` + `@yield('content')`. 【F:resources/views/layouts/app.blade.php†L1-L40】
- `resources/views/partials/sidebar.blade.php`: Menú lateral que habilita accesos según rol (dashboard, matrículas, académicos, etc.). 【F:resources/views/partials/sidebar.blade.php†L1-L120】
- Directorios especializados: `usuarios/`, `roles/`, `estudiantes/`, `docentes/`, `matriculas/`, `cursos/`, `materias/`, `curso_materias/`, `periodos/`, `horarios/`, `actividades/`, `notas/` y `notas_consulta/`. Cada carpeta contiene listados, formularios `crear/editar` y vistas detalle compatibles con los controladores descritos.
- Autenticación y registro: `auth/login.blade.php` maneja el formulario protegido por CSRF, mientras que `registro/registro.blade.php` publica la interfaz de autogestión de usuarios. 【F:resources/views/auth/login.blade.php†L1-L85】【F:resources/views/registro/registro.blade.php†L1-L128】
- Dashboard (`resources/views/dashboard.blade.php`) integra widgets para finanzas, estadísticas académicas y accesos rápidos condicionados por el rol. 【F:resources/views/dashboard.blade.php†L1-L120】

## 6. Recursos front-end y compilación
- Tailwind se declara en `resources/css/app.css` mediante `@import 'tailwindcss'` y se especifica la fuente por defecto. El archivo incluye directivas `@source` para que el motor JIT escanee las vistas y scripts. 【F:resources/css/app.css†L1-L12】
- `resources/js/app.js` carga `bootstrap.js`, el cual configura Axios con cabeceras AJAX por defecto. 【F:resources/js/app.js†L1-L1】【F:resources/js/bootstrap.js†L1-L5】
- `vite.config.js` registra los assets CSS/JS y activa el plugin oficial de Laravel junto a Tailwind. 【F:vite.config.js†L1-L12】

## 7. Base de datos y migraciones
- El directorio `database/migrations` contiene la evolución del esquema: tablas para usuarios/roles, matrículas, cursos, pivotes, estudiantes, docentes, materias, periodos, horarios, actividades y notas. 【F:database/migrations/2025_10_09_012251_create_roles_table.php†L1-L20】
- Las migraciones recientes incluyen ajustes como `update_horarios_table_structure` y columnas extras (`estudiante_registro_id` en matrículas) necesarios para los flujos de aprobación automática. 【F:database/migrations/2025_11_19_000001_update_horarios_table_structure.php†L1-L20】

