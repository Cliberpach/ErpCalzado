gi# Cómo levantar ErpCalzado

Este proyecto usa **PHP 7.4.33** (distinto al de otros proyectos en esta PC), así que hay que
usar la ruta completa al ejecutable en vez del comando `php` genérico.

## 0. Arrancar MySQL en Laragon

`php artisan serve` NO depende de Apache/Nginx, así que **no hace falta darle a "Start All"**
en Laragon. Lo único que necesitás corriendo es MySQL:

1. Abrí Laragon
2. Clic derecho → **MySQL** → **Start**

(Si le das "Start All" tampoco pasa nada malo, solo que arranca cosas que no usás para este
proyecto.)

## 1. Levantar el servidor

Abre una terminal (PowerShell o CMD) en la carpeta del proyecto y corre:

```powershell
C:\laragon\bin\php\php-7.4.33-Win32-vc15-x64\php.exe artisan serve --port=8001
```

Deja la terminal abierta. La app queda disponible en:

```
http://localhost:8001
```

## 2. Si tocaste algo de CSS/JS (opcional)

En otra terminal, en la misma carpeta:

```powershell
npm run dev       # compila una vez
npm run watch     # recompila automáticamente al guardar cambios
```

## Notas

- Requiere que **MySQL esté corriendo en Laragon** (base de datos: `erpsiscom`).
- Los procesos (`artisan serve`, `npm run watch`) se cierran solos al apagar la PC o cerrar
  la terminal — no quedan corriendo en segundo plano de forma permanente.
- Para correr esto **al mismo tiempo** que `ecommerceMerris` (que usa PHP 8.3.30), simplemente
  abrí una terminal para cada uno con su propio comando — no hay conflicto entre ambos.
