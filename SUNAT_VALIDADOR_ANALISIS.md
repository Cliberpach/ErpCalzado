# Análisis: Validador SUNAT en calzadoprov2

> Referencia para implementar "Consulta SUNAT" en módulo **Contabilidad** de ErpCalzado.
> Sistema fuente: `C:\laragon\www\calzadoprov2\` — módulo Ventas > Validador SUNAT

---

## 1. Arquitectura General

calzadoprov2 usa **Laravel Livewire** para toda la UI del Validador SUNAT.
ErpCalzado usa controladores tradicionales + Blade (sin Livewire visible en nav).
Esto es la diferencia de stack más importante a considerar al adaptar.

```
calzadoprov2/
├── routes/tenant.php                          # 3 rutas sunat
├── app/
│   ├── Livewire/Tenant/Ventas/Sunat/
│   │   ├── ValidadorDocumentos.php            # componente principal
│   │   ├── ComprobantePendienteIndex.php      # facturas rechazadas
│   │   └── ComprobantePorEnviarIndex.php      # boletas pendientes
│   └── Services/Tenant/
│       ├── ConsultaCpeService.php             # API REST SUNAT (OAuth2)
│       └── GreenterService.php                # SOAP SUNAT via greenter
└── resources/views/livewire/tenant/ventas/sunat/
    ├── validador-documentos.blade.php
    ├── comprobante-pendiente-index.blade.php
    └── comprobante-por-enviar-index.blade.php
```

---

## 2. Los 3 Submodulos de SUNAT

### 2.1 Validador SUNAT (el principal)
Ruta: `sunat.validador` → componente `ValidadorDocumentos`

**Qué hace:** El usuario selecciona tipo de comprobante, serie y rango de números.
El sistema consulta la API CPE de SUNAT para cada documento y compara el estado
en SUNAT vs el estado local en BD. Si difieren, el botón "Regularizar" sincroniza.

**Flujo:**
```
Usuario filtra (tipo/serie/rango)
    → ValidadorDocumentos::validar()
    → ConsultaCpeService::validar() [por cada doc, throttle 200ms]
    → API REST SUNAT devuelve estadoCp (0-4)
    → tabla muestra estado_sistema vs estado_sunat
    → botón "Regularizar" → ValidadorDocumentos::regularizar(id)
    → actualiza DocumentoVenta.estado_sunat en BD
```

### 2.2 Comprobantes Pendientes
Ruta: `sunat.pendientes` → componente `ComprobantePendienteIndex`

**Qué hace:** Lista facturas rechazadas o con error en SUNAT.
Permite reenviarlas via SOAP o consultar el CDR de un ticket async.

### 2.3 Comprobantes por Enviar
Ruta: `sunat.por-enviar` → componente `ComprobantePorEnviarIndex`

**Qué hace:** Lista boletas que nunca fueron enviadas a SUNAT.
Permite envío individual.

---

## 3. Servicio Core: ConsultaCpeService

Este es el corazón del Validador. Consulta la **API CPE Integrada de SUNAT**.

### Autenticación OAuth2 (client_credentials)
```
POST https://api-seguridad.sunat.gob.pe/v1/clientesextranet/{clientId}/oauth2/token/

Body:
  grant_type    = client_credentials
  scope         = https://api.sunat.gob.pe/v1/contribuyente/contribuyentes
  client_id     = {Empresa.cpe_client_id}
  client_secret = {Empresa.cpe_client_secret}
```
El token se cachea: clave `cpe_token_{MD5(clientId)}`, TTL = `expires_in - 60s`.

### Endpoint de Validación
```
GET https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/{ruc}/validarcomprobante

Query params:
  numRuc      = RUC emisor
  codComp     = 01 (Factura) | 03 (Boleta) | 07 (Nota Crédito)
  numeroSerie = ej: F001
  numero      = ej: 1234
  fechaEmision = dd/mm/yyyy
  monto       = importe total

Header: Authorization: Bearer {token}
```

### Respuesta — estadoCp
| Valor | Significado |
|-------|-------------|
| `0`   | No existe en SUNAT |
| `1`   | Aceptado |
| `2`   | Anulado |
| `3`   | Autorizado (guías) |
| `4`   | No autorizado |

---

## 4. Servicio Secundario: GreenterService (SOAP)

Usado para **reenviar** documentos rechazados o pendientes.
Wrapper del paquete PHP `greenter/greenter`.

| Método | Endpoint SUNAT | Uso |
|--------|----------------|-----|
| `enviarDocumento()` | FE_BETA / FE_PRODUCCION | Facturas y boletas |
| `enviarResumenDiario()` | FE_BETA / FE_PRODUCCION | Resúmenes de boletas |
| `consultarTicket()` | mismo | Polling async de resúmenes |
| `enviarGuia()` | GUIA_BETA / GUIA_PRODUCCION | Guías de remisión |

Credenciales: `Empresa.usuario_secundario_sunat` + `Empresa.clave_secundario_sunat` (SOL).

---

## 5. Campos BD — tabla `documentos_venta`

Migrations agregadas al proyecto:

| Columna | Tipo | Propósito |
|---------|------|-----------|
| `estado_sunat` | string | `pending/aceptado/rechazado/error/no_aplica` |
| `sunat_codigo` | string | Código de respuesta SUNAT |
| `sunat_mensaje` | text | Mensaje legible de SUNAT |
| `sunat_respuesta_completa` | json | Respuesta completa deserializada |
| `sunat_hash` | string | SHA256 del XML (despacho) |
| `sunat_ticket` | string | Ticket async para resúmenes |
| `en_resumen` | boolean | Si la boleta está en resumen diario |
| `resumen_id` | int FK | Relación a tabla `resumenes` |

Credenciales en `empresas`:
- `cpe_client_id` — ID de app OAuth2 SUNAT
- `cpe_client_secret` — Secret de app OAuth2 SUNAT
- `usuario_secundario_sunat` — SOL secundario
- `clave_secundario_sunat` — clave SOL secundario

---

## 6. UI del Validador (vista blade)

`validador-documentos.blade.php` tiene:
1. **Filtros:** select tipo comprobante + input serie + input rango número (desde/hasta)
2. **Botón "Validar":** dispara `wire:click="validar"` → batch query a SUNAT
3. **Tabla resultado:** columnas serie, número, fecha, monto, `estado_sistema` (badge), `estado_sunat` (badge), acción
4. **Botones por fila:** "Regularizar" si difieren los estados
5. **Botón global:** "Regularizar Todos"

Badges de estado usan clases Bootstrap: `badge-success/warning/danger/secondary`.

---

## 7. Navegación en calzadoprov2

En `resources/views/components/layouts/tenant.blade.php:71`:
```php
['label' => 'Validador SUNAT', 'ruta' => 'tenant.ventas.sunat.validador']
```
Colgado bajo el menú **Ventas** como ítem de segundo nivel.

Además hay un `SunatIndicador` livewire en el header que muestra badge
con conteo de documentos rechazados/pendientes (cache 60s).

---

## 8. Estructura actual de Contabilidad en ErpCalzado

En `nav.blade.php` líneas 383–393:
```blade
@can('restore', [Auth::user(), ['contabilidad.documentos.index']])
    <li class="@yield('contabilidad-active')">
        ...Contabilidad...
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'contabilidad.documentos.index')
                <li ...><a href="{{ route('consultas.contabilidad.index') }}">Documentos</a></li>
            @endcan
        </ul>
    </li>
@endcan
```

Solo tiene 1 submodulo: **Documentos**. El nuevo "Consulta SUNAT" iría aquí como segundo ítem.

---

## 9. Diferencias de Stack a Resolver

| Aspecto | calzadoprov2 | ErpCalzado |
|---------|-------------|------------|
| Framework UI | Livewire | Blade + controladores tradicionales |
| Multi-tenant | Sí (tenant.php routes) | No (single-tenant) |
| Permisos | `tenant.ventas.sunat.validador` | `@can('haveaccess', 'permiso.key')` |
| Rutas | `routes/tenant.php` | `routes/web.php` |

Para ErpCalzado, el `ConsultaCpeService` puede portarse **tal cual** (es puro PHP/HTTP).
La UI necesita hacerse en Blade tradicional con AJAX o form submit, sin Livewire.

---

## 10. Credenciales Necesarias (Configuración Empresa)

Para que funcione el Validador, la empresa necesita:
1. **App OAuth2 en SUNAT:** registrarse en `https://api.sunat.gob.pe` → obtener `client_id` + `client_secret`
2. **Usuario SOL secundario:** para envíos SOAP via Greenter

Estos datos deben guardarse en la tabla `empresas` (o `configuracion`).
En calzadoprov2 están en `empresas.cpe_client_id` / `cpe_client_secret`.

---

## 11. Resumen de Archivos a Crear en ErpCalzado

> Solo análisis — no programar aún.

| Archivo | Tipo | Propósito |
|---------|------|-----------|
| `app/Services/ConsultaCpeService.php` | Service | Portar de calzadoprov2 |
| `app/Http/Controllers/Contabilidad/ConsultaSunatController.php` | Controller | Reemplaza Livewire |
| `resources/views/contabilidad/sunat/index.blade.php` | View | Formulario + tabla resultado |
| `routes/web.php` (línea nueva) | Route | `contabilidad.sunat.index` + `contabilidad.sunat.validar` |
| Migration (opcional) | DB | Si se quiere guardar historial de consultas |
| `nav.blade.php` (editar) | Nav | Agregar ítem bajo Contabilidad |
| `PermissionSeeder` (editar) | Seed | Agregar `contabilidad.sunat.index` |
