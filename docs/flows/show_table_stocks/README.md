# Flow: Mostrar tabla de tallas con stock (sin ruido visual)

## Problema

Las tablas de detalle que muestran cantidades por talla (ej. detalle de Nota de Ingreso) recorrían **todas** las tallas activas del sistema (`Talla::where('estado','ACTIVO')->get()`), sin importar si esa talla tenía o no stock cargado. Esto generaba:

- Columnas de tallas sin ningún stock en ningún color.
- Celdas con `0` en tallas sin cantidad, generando ruido visual.

## Regla aplicada

1. **Columnas (tallas) a mostrar**: una talla se muestra si **al menos un color** del detalle tiene `cantidad > 0` en esa talla. Basta que un solo color tenga stock en esa talla para que la columna completa aparezca (aplica para todos los colores de la fila).
2. **Celdas en 0**: no se imprime `0`. Si la cantidad de una talla para ese producto-color es 0, la celda se deja en blanco.

## Dónde vive (ejemplo: Nota de Ingreso — vista `edit`)

Archivo: `resources/views/almacenes/nota_ingresos/edit.blade.php`

### 1. Filtrar tallas en el servidor (Blade)

```blade
@php
    $detalleData = json_decode($detalle, true) ?: [];
    $tallasConStockIds = collect($detalleData)
        ->filter(fn ($item) => (float) ($item['cantidad'] ?? 0) > 0)
        ->pluck('talla_id')
        ->unique();
    $tallasConStock = $tallas->whereIn('id', $tallasConStockIds)->values();
@endphp
```

`$tallas` sigue siendo el listado completo (viene del controlador). `$tallasConStock` es el subconjunto real a pintar.

### 2. Pasar el subconjunto al partial de la tabla (encabezado)

```blade
@include('almacenes.nota_ingresos.tabla-productos',[
    "carrito" => "carrito",
    "tallas" => $tallasConStock
])
```

El partial `tabla-productos.blade.php` ya recorre `$tallas` para pintar las columnas (`@foreach ($tallas as $talla) <th>...</th> @endforeach`); al inyectar `$tallasConStock` como `tallas`, el `@foreach` local usa el subconjunto sin tocar el partial.

### 3. Pasar el mismo subconjunto al JS (cuerpo de la tabla)

```js
const tallas = @json($tallasConStock);
```

El JS pinta el cuerpo (`pintarDetalleNotaIngreso`) iterando este mismo array `tallas`, así encabezado y filas siempre coinciden en cantidad y orden de columnas.

### 4. No imprimir ceros en las celdas

```js
cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad=0;
htmlTallas += `<td>${Number(cantidad)>0?cantidad:''}</td>`;
```

## Segundo caso aplicado: Nota de Salida — vista `show`

Archivo: `resources/views/almacenes/nota_salidad/show.blade.php`

Aquí `$detalle` **no** viene como JSON string (a diferencia de Nota de Ingreso), sino como array de objetos `stdClass` directo de `DB::select(...)`. El filtro se hace directo sobre el objeto:

```blade
@php
    $tallasConStockIds = collect($detalle)
        ->filter(fn ($item) => (float) ($item->cantidad ?? 0) > 0)
        ->pluck('talla_id')
        ->unique();
    $tallasConStock = $tallas->whereIn('id', $tallasConStockIds)->values();
@endphp
```

El partial `tables/tbl_ns_detalle.blade.php` se reutiliza en otras vistas (`form_ns_create`, `solicitudes_traslado`, `traslados`) que **no** pasan `tallas` filtradas — solo se sobreescribe el include dentro de `show.blade.php`, así el resto de vistas sigue mostrando el set completo de tallas (correcto para formularios de creación).

## Tercer caso aplicado: Traslados — vista `show`

Archivo: `resources/views/almacenes/traslados/show.blade.php`

`$detalle` aquí es una Collection de Eloquent (`TrasladoDetalle::where('traslado_id', $id)->get()`), mismo filtro por `->cantidad`:

```blade
@php
    $tallasConStockIds = collect($detalle)
        ->filter(fn ($item) => (float) ($item->cantidad ?? 0) > 0)
        ->pluck('talla_id')
        ->unique();
    $tallasConStock = $tallas->whereIn('id', $tallasConStockIds)->values();
@endphp
```

El partial `tables/tbl_traslado_show.blade.php` también se reutiliza en `solicitudes_traslado/confirmar.blade.php`, que no pasa `tallas` — el override solo aplica en `show.blade.php`.

El JS ya tenía `cantidad = ''` cuando la fila no existía; se agregó también el chequeo `Number(cantidad) > 0` para blanquear el caso en que la fila existe pero `cantidad` es `0`.

## Cuarto caso aplicado: Solicitudes de Traslado — vista `show`

Archivo: `resources/views/almacenes/solicitudes_traslado/show.blade.php`

Mismo patrón que Traslados (`$detalle` es Collection de `TrasladoDetalle`). El partial `tables/tbl_traslado_detalle.blade.php` también se reutiliza en `_confirmar_show.blade.php`, que no pasa `tallas` — override solo en `show.blade.php`.

## Replicar este flow en otra vista

1. Ubicar de dónde sale el detalle/carrito (array con `talla_id` y `cantidad` por color/producto).
2. Calcular el set de `talla_id` con `cantidad > 0` (server-side con `@php` + `collect()`, o client-side si el detalle ya está en JS).
3. Filtrar la colección de tallas completa contra ese set antes de pasarla al header de la tabla y al JS que pinta el cuerpo — deben usar la **misma** colección filtrada.
4. Al pintar cada celda, si `cantidad == 0`, imprimir cadena vacía en vez de `0`.
