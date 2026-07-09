# ErpCalzado — Documentación del Proyecto

> Documento generado para subir como contexto/knowledge en Claude (proyecto en claude.ai).
> Fecha de generación: 2026-07-07.

## 1. Resumen general

**ErpCalzado** es un ERP monolítico construido en **Laravel 7** para una empresa del rubro **calzado**. Cubre los procesos internos de:

- Almacén / inventario (Almacenes)
- Ventas, POS y pedidos (Ventas / Pos / Pedidos)
- Compras (Compras)
- Caja y movimientos de efectivo (Caja)
- Contabilidad y facturación electrónica SUNAT (Contabilidad + Greenter)
- Producción (InvDesarrollo / órdenes de producción)
- Administración y seguridad (Mantenimiento / Seguridad)

Además expone una **API pública separada** (`routes/api.php`) que sirve de backend a un storefront/e-commerce de cara al cliente final (catálogo, carrito, envíos, promociones), independiente del panel administrativo interno.

El frontend es **híbrido**: Blade + jQuery + Bootstrap 4 + DataTables como base, con **componentes Vue 2** puntuales para widgets interactivos (tablas, modales, flujos tipo carrito). No es una SPA.

## 2. Stack tecnológico

### Backend (`composer.json`)
- **Laravel** `^7.29` (locked en v7.30.6), PHP `^7.2.5|^8.0`
- `laravel/ui` 2.4 — auth scaffolding clásico (login/register Blade)
- `spatie/laravel-permission` ^5.1 — roles y permisos
- `spatie/laravel-activitylog` ^3.17 — auditoría
- `greenter/lite` ^5.0 + `greenter/consulta-cpe` ^1.1 — facturación electrónica SUNAT (Perú), junto con capa propia en `app/Greenter` y `app/Facturacion` (firma, certificados, SOAP/XML)
- `beyondcode/laravel-websockets` ^1.13 + `pusher/pusher-php-server` ~4.0 — broadcasting en tiempo real (hay config legacy `websockets-antiguo.php`, sugiere migración/reescritura en curso)
- `yajra/laravel-datatables` 1.5 — DataTables server-side
- `maatwebsite/excel` ^3.1 — import/export Excel (correlaciona con 33 clases en `app/Exports` y 14 en `app/Imports`)
- `barryvdh/laravel-dompdf`, `simplesoftwareio/simple-qrcode`, `bacon/bacon-qr-code`, `picqer/php-barcode-generator` — PDFs de comprobantes, QR y códigos de barra de producto
- `luecano/numero-a-letras` — montos en letras (comprobantes)
- `tightenco/ziggy` ^1.4 — rutas de Laravel disponibles en JS
- `fruitcake/laravel-cors`, `fideloper/proxy`, `phpzip/phpzip`

### Frontend (`package.json`)
- **Laravel Mix ^6** (Webpack), no Vite
- **Vue 2.7.16** (Options API) — componentes puntuales, no SPA completa
- **jQuery ^3.7**, **Bootstrap ^4.6** + `bootstrap-vue`
- `element-ui` ^2.15 — kit UI Vue secundario
- Familia DataTables (`datatables.net-bs4`, buttons, responsive) junto con `yajra/laravel-datatables`
- `laravel-echo` + `pusher-js` — cliente de websockets/broadcasting
- `highcharts` — gráficos/dashboard
- `filepond` + plugins — carga/preview de imágenes
- `select2`, `vue-select`, `tom-select` — selects buscables
- `pdfmake`, `moment`, `query-string`, `vue-toastr`/`vue-toast`

## 3. Estructura de `app/`

El proyecto **no** usa la estructura Laravel por defecto ni un paquete de módulos (no hay `nwidart/laravel-modules`). La separación por dominio se logra con **namespaces de primer nivel dentro de `app/`**:

- `app/Almacenes` (25 archivos) — Producto, Almacen, Kardex, Traslado, LoteProducto, Vehiculo, Conductor, CodigoBarra, etc.
- `app/Ventas` (25) — Cliente, Cotizacion, Documento, Guia, Nota, Pedido, Resumen, Retencion, CuentaCliente, etc.
- `app/Compras` (11)
- `app/Mantenimiento` (23)
- `app/Pos` (6), `app/Pedidos` (2), `app/Movimientos` (1), `app/InvDesarrollo` (5), `app/Configuracion` (1), `app/Permission` (3, extiende spatie)
- Modelos sueltos en raíz: `User.php`, `Parametro.php`, `PersonaTrabajador.php`, `UserPersona.php`, `DetallesMovimientoCaja.php`, `TestTareas.php`
- `app/Models` (solo 14 archivos, bajo `Almacenes/`, `Kardex/`, `Mantenimiento/`, `Ventas/`) — convención más nueva/parcial que convive con el estilo anterior de modelos en la raíz del dominio
- `app/Facturacion` + `app/Greenter` (15) — capa de integración SUNAT (WS/Signed, certificados, wrappers Greenter)
- `app/Exports` (33) / `app/Imports` (14) — clases Excel, refleja fuerte necesidad de reportería
- `app/Http` (275 archivos):
  - `Controllers/` organizados por módulo de dominio; namespace `Api/` aparte (`Almacenes`, `Cart`, `Company`, `Promotions`, `Shipping`) para el storefront
  - `Services/` — capa de servicios por dominio (`Almacen`, `Caja`, `Contabilidad/ConsultaSunat`, `Dashboard` con split Manager/Repository/Service, `Kardex/Cuenta`, `Mantenimiento`, `Pedidos`, `Produccion/Orden`, `Seguridad/User`, `Ventas`), muchas veces sub-namespaced por entidad
  - `Requests/`, `Middleware/`, `Helpers/` (incluye `Helpers.php` autoloaded con funciones globales)
- Estándar: `Console`, `Events` (10, incl. `NotifySunatEvent`), `Listeners` (10), `Jobs`, `Mail`, `Notifications` (5), `Policies`, `Providers`, `Exceptions`

## 4. Rutas (`routes/`)

`web.php` incluye (vía `require`) archivos de rutas por dominio:
`routes/ventas/web.php`, `routes/mantenimiento/web.php`, `routes/kardex/web.php`, `routes/cajas/web.php`, `routes/almacenes/web.php`, `routes/dashboard/web.php`, `routes/seguridad/web.php`, `routes/pedidos/web.php`, `routes/utils/web.php`.

Todo bajo middleware `auth`. Grupos principales de recursos por archivo:

| Archivo | Recursos |
|---|---|
| `web.php` (core) | configuracion, almacenes/{almacen,categorias,marcas,modelos,colores,tallas,productos,conductores,nota_ingreso,nota_salidad,solicitudes_traslado,traslados,vehiculos}, compras/{documentos,notas,ordenes,proveedores}, comprobantes/electronicos, consultas/* (varios), contabilidad/sunat, cuentaCliente, cuentaProveedor, importExcel, modeloExcel, notas/electronicos, reportes/*, RUC/DNI lookup, backup-restore |
| `almacenes/web.php` | almacen, almacenes, categorias, colores, conductores, marcas, modelos, nota_ingreso, nota_salidad, productos (+ sub-recurso color/imagen), solicitudes_traslado, tallas, traslados, vehiculos |
| `cajas/web.php` | cajas, egresos, movimientos, recibos_caja |
| `kardex/web.php` | cliente, cuenta, kardex, producto, proveedor, stock, venta |
| `mantenimiento/web.php` | colaboradores, condiciones, copias_seguridad, cuentas, empresas, mantenimiento, metodos_entrega, promociones, sedes, tablas/detalles, tablas/generales, tarifarios, tipo_pago, ubigeo, vendedores |
| `pedidos/web.php` | detalles, ordenes, pedidos |
| `seguridad/web.php` | roles, seguridad, users |
| `ventas/web.php` | caja, clientes, cotizaciones, despachos, documentos, guiasremision, reservas, resumenes, ventas |
| `dashboard/web.php` / `utils/web.php` | dashboard, utilidades |

`api.php` — API pública/storefront (controladores `Api\*` propios, no reutiliza los del panel admin): `categorias`, `productos` (listado, detalle, por color/talla, búsqueda, por tag, home-products), `colores`, `tallas`, `company` (locations), `shipping` (costo por provincia), `promotions`, `cart` (validate-stock).

## 5. Base de datos (`database/migrations`, 116 archivos)

Entidades por área:

- **Geo/referencia**: departamentos, provincias, distritos, ubigeo, tablas genéricas (tabla_detalles)
- **Personas/organización**: personas, persona_trabajador, empresas, empresa_sedes, colaboradores, users, vendedores, condicions, user_persona
- **Almacén/inventario**: almacenes, categorias, marcas, modelos, colores, tallas, productos, producto_colores, producto_color_tallas, producto_color_imagenes, codigos_barra, lote_productos(+detalle), nota_ingreso/nota_salidad(+detalles), movimiento_almacenes, movimiento_nota, traslados(+detalle), vehiculos, conductores, cambios_tallas, product_features, kardex
- **Ventas**: cotizaciones(+detalles), cotizacion_documento(+detalles), clientes, tipos_pago, pedidos(+detalles, atenciones), guias_remision(+detalles), nota_electronica(+detalle), retencions(+detalles), envios_ventas, empresas_envio/empresa_envio_sedes, cuenta_cliente(+detalle), error_venta/error_guia/error_nota (tracking de rechazos SUNAT)
- **Compras**: proveedores, compra_documentos(+detalles), nota_credito_compras(+detalle), cuenta_proveedor(+detalle)
- **Producción**: ordenes(+detalle), ordenes_produccion(+detalles), prototipos, registro_sanitario
- **Caja/finanzas**: caja, movimiento_caja, detalles_movimiento_caja, egresos(+detalle), recibos_caja(+detalle), cuentas, tipo_pago_cuentas, kardex_cuentas (+ funciones/procedimientos SQL: `fn_calcular_saldo`, `sp_kardex_cuentas`, `sp_updatenumeraciondocumento`)
- **Facturación/SUNAT**: empresa_facturaciones, empresa_numeracion_facturaciones, greenter_config, banco_empresas/bancos
- **Seguridad**: roles, role_user, permissions, permission_role, activity_log
- **Marketing/e-commerce**: promociones(+productos), productos_clientes, tipos_clientes
- **Infra**: failed_jobs, jobs, cache, password_resets, notifications, websockets_statistics_entries, copias_seguridad

## 6. Config notable (`config/`)

- Sin multi-tenant (app single-tenant) — a diferencia del proyecto hermano `calzadoprov2` (ver sección 8)
- `config/datatables.php`, `config/permission.php`, `config/activitylog.php`, `config/dompdf.php`
- `config/websockets.php` + legacy `config/websockets-antiguo.php` (reescritura en curso)
- `config/cors.php`

## 7. Frontend — `resources/`

- **Blade primero**, híbrido con Vue 2. `resources/views/` espeja la estructura de dominio: `almacenes`, `compras`, `configuracion`, `consultas`, `contabilidad`, `dashboard`, `kardex`, `mantenimiento`, `pedidos`, `pos`, `recibos_caja`, `reportes`, `seguridad`, `utils`, `ventas`, `Egreso` (+ `Egreso_old` obsoleto), más `auth`, `layout.blade.php`/`layouts`, `partials`, `components`, `email`, `errors`.
- `resources/js/` — `app.js`, `appNotify.js`, `appPages.js`, `bootstrap.js`, `helpers.js`, `utilidades.js`, `components/` (mix de `.vue` en raíz: `DataTableKardex.vue`, `ModalLotes.vue`, `Pagination.vue`, `Toast*.vue`, y subcarpetas `caja`, `layout`, `shared`, `ventas`), `views/` (`Ventas`, `kardex`), `interfaces/`, `libs/`, `utils/` (incl. `selects`).
- Convención documentada en `docs/skills/frontend_skill.md`: bloque de cabecera requerido en `index.blade.php` (breadcrumb/hero, sección sidebar-active, patrón de botón "agregar").

## 8. Documentos internos existentes

- `PLAN_CONSULTA_SUNAT.md` (raíz) — plan de implementación de "Consulta SUNAT" en el módulo Contabilidad: pasos para registrar app OAuth2 en el portal SUNAT (obtener `client_id`/`client_secret`), migración para agregar `cpe_client_id`/`cpe_client_secret` a `empresa_facturaciones`, nuevo `ConsultaSunatController` y `ConsultaSunatManager` (token OAuth2 + llamada REST "validar comprobante", con throttling y lote máx. 50).
- `SUNAT_VALIDADOR_ANALISIS.md` (raíz) — análisis comparativo contra el proyecto hermano `calzadoprov2` (Livewire, multi-tenant) que ya tiene un módulo "Validador SUNAT" con 3 sub-módulos (Validador de Documentos, Comprobantes Pendientes, Comprobantes por Enviar) y arquitectura `ConsultaCpeService`/`GreenterService`, usado como blueprint para portar la funcionalidad a ErpCalzado (Blade tradicional, sin Livewire).
- `docs/flows/show_table_stocks/README.md` — nota sobre el flujo de la tabla de stocks.
- `docs/skills/frontend_skill.md` — convención de cabecera Blade para vistas `index`.

Nota: `README.md` es el boilerplate estándar de Laravel, sin información específica del proyecto.

## 9. Variables de entorno (`.env.example`, grupos sin secretos)

- `APP_*`, `LOG_CHANNEL`
- `DB_*` — MySQL (ejemplo: `DB_DATABASE=erpsiscom`)
- `BROADCAST_DRIVER`, `CACHE_DRIVER`, `QUEUE_CONNECTION` (sync), `SESSION_*`
- `REDIS_*`
- `MAIL_*` (SMTP/Mailtrap de ejemplo)
- `AWS_*` (S3: access key, secret, region, bucket)
- `PUSHER_*` / `MIX_PUSHER_*` (broadcasting)

Las credenciales SUNAT/Greenter (`sol_user`, `sol_pass`, y los nuevos `cpe_client_id`/`cpe_client_secret`) **no** están en `.env`: se guardan por empresa en la tabla `empresa_facturaciones` (vía UI de Mantenimiento/Empresas).

## 10. Conclusión arquitectónica

ERP monolítico Laravel 7 para empresa de calzado, con separación de dominio manual (namespaces bajo `app/`, sin paquete de módulos) y capa de servicios (`app/Http/Services`). Rutas partidas por dominio e incluidas en `web.php`. Frontend Blade + jQuery/Bootstrap 4/DataTables como base, con componentes Vue 2 puntuales — no SPA. Incluye una API pública separada para un storefront de cliente final. Hay una migración de funcionalidad SUNAT en curso, portando el "Validador de Comprobantes" desde un proyecto hermano multi-tenant basado en Livewire (`calzadoprov2`).
