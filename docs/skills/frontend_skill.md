# Frontend Skill — Encabezado de Vistas (Blade)

Toda vista `index.blade.php` (u otra vista principal de módulo) debe iniciar con este bloque para que el encabezado (breadcrumb + hero + botón agregar) se renderice bien:

```blade
@extends('layout')

@section('almacenes-active', 'active')
@section('modelo-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Modelos')
@section('hero-title', 'Lista de Modelos')
@section('hero-subtitle', 'Modelos')
@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlCreateModelo()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection
```

## Reglas por sección

- `@extends('layout')`: siempre layout base.
- `@section('{modulo}-active', 'active')`: activa ítem del menú lateral (ej. `almacenes-active`).
- `@section('{submodulo}-active', 'active')`: activa subítem del menú (ej. `modelo-active`).
- `@section('bread-module', ...)`: nombre módulo padre en breadcrumb (ej. `Almacén`).
- `@section('bread-submodule', ...)`: nombre submódulo en breadcrumb (ej. `Modelos`).
- `@section('hero-title', ...)`: título grande de la página.
- `@section('hero-subtitle', ...)`: subtítulo de la página.
- `@section('btn-add')`: botón de acción principal (crear/nuevo). Usa clase `main-btn-add`, ícono `fas fa-plus-circle`, y `onclick` que abre el modal de creación correspondiente (ej. `openMdlCreateModelo()`).

## Notas

- Cambia nombres de secciones activas (`{modulo}-active`, `{submodulo}-active`) según el módulo real de la vista.
- Cambia textos de breadcrumb/hero según el módulo/submódulo real.
- Cambia función `onclick` del botón según el modal de creación real de esa vista.
