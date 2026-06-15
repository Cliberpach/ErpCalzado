# Plan de Implementación — Consulta SUNAT (Módulo Contabilidad)

---

## Lo que TÚ debes hacer en el Portal SUNAT

### Paso 1 — Registrar aplicación OAuth2 en SUNAT

1. Ingresa a **https://api.sunat.gob.pe** con tu clave SOL
2. Ve a **"Mis Aplicaciones"** → **"Nueva Aplicación"**
3. Selecciona el servicio: **"Consulta de Comprobantes de Pago Electrónicos"**
4. Completa el formulario y guarda
5. SUNAT te entregará:
   - `client_id` (ej: `a0b1c2d3-e4f5-...`)
   - `client_secret` (ej: `ABC123xyz==`)
6. Guarda esos dos valores — los necesitarás para configurarlos en el sistema

> **Nota:** Cada empresa que use este validador necesita su propio `client_id` + `client_secret`.
> No son las mismas credenciales que el SOL secundario.

---

## Lo que YO haré en el sistema

### Archivos a crear/modificar

```
ErpCalzado/
├── database/migrations/
│   └── [fecha]_add_cpe_credentials_to_empresa_facturaciones.php  ← NUEVO
├── app/
│   ├── Http/
│   │   ├── Controllers/Contabilidad/
│   │   │   └── ConsultaSunatController.php                        ← NUEVO
│   │   └── Services/Contabilidad/ConsultaSunat/
│   │       └── ConsultaSunatManager.php                           ← NUEVO
├── resources/views/contabilidad/sunat/
│   └── index.blade.php                                            ← NUEVO
├── routes/web.php                                                 ← MODIFICAR (agregar rutas)
└── resources/views/partials/nav.blade.php                         ← MODIFICAR (agregar ítem)
```

---

## Detalle de cada archivo

### 1. Migration — `cpe_credentials`

**Tabla:** `empresa_facturaciones` (donde ya están `sol_user`, `sol_pass`)

```sql
ALTER TABLE empresa_facturaciones ADD COLUMN cpe_client_id VARCHAR(255) NULL;
ALTER TABLE empresa_facturaciones ADD COLUMN cpe_client_secret VARCHAR(255) NULL;
```

Sin valor por defecto — quedan NULL hasta que el admin los configure en Mantenimiento/Empresas.

---

### 2. Service — `ConsultaSunatManager`

**Ruta:** `app/Http/Services/Contabilidad/ConsultaSunat/ConsultaSunatManager.php`

Responsabilidades:
- `getToken(Facturacion $facturacion): string`
  - POST a `https://api-seguridad.sunat.gob.pe/v1/clientesextranet/{clientId}/oauth2/token/`
  - Body: `grant_type=client_credentials`, `scope`, `client_id`, `client_secret`
  - Cachea el token con `Cache::put("cpe_token_{facturacion->id}", $token, $ttl)`
  - Lanza excepción si `cpe_client_id` o `cpe_client_secret` son NULL
- `validarComprobante(array $datos, Facturacion $facturacion): array`
  - GET a `https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/{ruc}/validarcomprobante`
  - Params: `numRuc`, `codComp`, `numeroSerie`, `numero`, `fechaEmision` (dd/mm/yyyy), `monto`
  - Retorna `['estadoCp' => 1, 'descripcion' => 'Aceptado', ...]`
- `validarLote(array $comprobantes, Facturacion $facturacion): array`
  - Llama `validarComprobante()` por cada ítem
  - Throttle: `usleep(200000)` entre llamadas (200ms)
  - Máximo 50 comprobantes por lote

**Mapa estadoCp:**
```
0 → No existe en SUNAT
1 → Aceptado
2 → Anulado
3 → Autorizado
4 → No autorizado
```

---

### 3. Controller — `ConsultaSunatController`

**Ruta:** `app/Http/Controllers/Contabilidad/ConsultaSunatController.php`

```php
// index()   → vista con el formulario
// validar() → recibe POST, llama Manager, devuelve JSON
```

**`index()`:**
- `$this->authorize('haveaccess', 'contabilidad.sunat.index')`
- Carga `Facturacion` de la empresa/sede actual
- Retorna `view('contabilidad.sunat.index')`

**`validar(Request $request)`:**
- `$this->authorize('haveaccess', 'contabilidad.sunat.index')`
- Valida request: `tipo_comprobante`, `serie`, `numero_desde`, `numero_hasta`, `fecha`, `monto`
- Construye array de comprobantes del rango (desde..hasta)
- Llama `ConsultaSunatManager->validarLote(...)`
- Retorna `response()->json(['success' => true, 'data' => $resultados])`
- Catch: `response()->json(['success' => false, 'message' => $th->getMessage()])`

---

### 4. Vista — `index.blade.php`

**Ruta:** `resources/views/contabilidad/sunat/index.blade.php`

**Estructura (igual al patrón del proyecto):**
```
@extends('layout')
@section('contabilidad-active', 'active')
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <!-- PANEL FILTROS -->
        <div class="ibox">
            <div class="ibox-title">Consulta Validador SUNAT</div>
            <div class="ibox-content">
                <!-- FORM FILTROS -->
                select tipo_comprobante (01=Factura, 03=Boleta, 07=Nota Crédito)
                input  serie            (ej: F001)
                input  numero_desde     (ej: 1)
                input  numero_hasta     (ej: 10)
                input  fecha            (date picker dd/mm/yyyy)
                input  monto            (decimal)
                button "Consultar SUNAT"
            </div>
        </div>

        <!-- TABLA RESULTADOS (inicialmente oculta) -->
        <div class="ibox" id="div-resultados" style="display:none">
            <div class="ibox-title">Resultados</div>
            <div class="ibox-content">
                <table id="tbl-resultados">
                    <thead>
                        <tr>
                            <th>Serie</th>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado SUNAT</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-resultados"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- vanilla JS + axios -->
    // consultar() → axios.post(route('contabilidad.sunat.validar'), datos)
    // pintarResultados(data) → itera data, pinta badge por estadoCp
    // badges: verde=Aceptado, rojo=No existe/No autorizado, naranja=Anulado
@endpush
```

---

### 5. Rutas — `routes/web.php`

```php
// Bajo el grupo de contabilidad (buscar donde está 'consultas.contabilidad.index')
Route::get('/contabilidad/sunat', [ConsultaSunatController::class, 'index'])
    ->name('contabilidad.sunat.index');
Route::post('/contabilidad/sunat/validar', [ConsultaSunatController::class, 'validar'])
    ->name('contabilidad.sunat.validar');
```

---

### 6. Nav — `nav.blade.php`

**Modificación en líneas 383–393** — agregar ítem dentro del `@can` de contabilidad:

```blade
@can('restore', [Auth::user(), ['contabilidad.documentos.index', 'contabilidad.sunat.index']])
    ...
    @can('haveaccess', 'contabilidad.sunat.index')
        <li class="@yield('contabilidad-sunat-active')">
            <a href="{{ route('contabilidad.sunat.index') }}">Consulta SUNAT</a>
        </li>
    @endcan
    ...
@endcan
```

---

## Flujo completo en producción

```
Usuario en vista "Consulta SUNAT"
    ↓ llena: tipo=Factura, serie=F001, desde=1, hasta=5, fecha=15/06/2026, monto=150.00
    ↓ clic "Consultar SUNAT"
    ↓ axios.post → ConsultaSunatController::validar()
    ↓ ConsultaSunatManager::getToken()
        → POST api-seguridad.sunat.gob.pe → Bearer token (cacheado)
    ↓ ConsultaSunatManager::validarLote([F001-1, F001-2, F001-3, F001-4, F001-5])
        → por cada uno: GET api.sunat.gob.pe/validarcomprobante → estadoCp
        → throttle 200ms entre llamadas
    ↓ JSON con array resultados
    ↓ JS pinta tabla con badges de color por estado
```

---

## Consideraciones adicionales

| Punto | Detalle |
|-------|---------|
| Timeout HTTP | 15 segundos por llamada a SUNAT |
| Lote máximo | 50 comprobantes — si desde..hasta > 50, mostrar error en UI |
| Cache token | Duración = `expires_in - 60s` para evitar expiración en mitad de lote |
| Credenciales nulas | Si `cpe_client_id` es NULL → mensaje claro: "Configure las credenciales CPE en Mantenimiento > Empresas" |
| Facturacion | Se lee de `empresa_facturaciones` por `sede_id` del usuario logueado |
| No hay migración en `documentos_venta` | Esta consulta es solo de consulta, no guarda estado en BD |

---

## Orden de implementación

1. Migration `cpe_client_id` + `cpe_client_secret` en `empresa_facturaciones`
2. `ConsultaSunatManager` (service)
3. `ConsultaSunatController`
4. Vista `contabilidad/sunat/index.blade.php`
5. Rutas en `web.php`
6. Nav en `nav.blade.php`
7. Agregar permiso `contabilidad.sunat.index` al seeder de permisos
