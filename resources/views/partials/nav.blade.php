<li class="nav-header">
    <div class="dropdown profile-element">
        {{-- @if (auth()->user()->ruta_imagen)
            <img alt="image" alt="{{ auth()->user()->name }}" class="rounded-circle" height="48" width="48" src="{{ Storage::url(auth()->user()->ruta_imagen) }}" />
        @else
            <img alt="{{ auth()->user()->name }}" alt="{{ auth()->user()->name }}" class="rounded-circle" height="48" width="48" src="{{ asset('img/default.jpg') }}" />
        @endif --}}
        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            <span class="block m-t-xs font-bold">{{ auth()->user()->name }}</span>
            <span class="text-muted text-xs block">Administrador <b class="caret"></b></span>
        </a>
        <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <li><a class="dropdown-item" href="login.html">Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="logo-element">
        {{-- <img src="{{ asset('img/default.png') }}" height="30" width="45"> --}}
    </div>
</li>

<li>
    <a href="{{ route('home') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Panel de
            control</span></a>
</li>

@can('restore', [Auth::user(), ['caja.caja.index', 'caja.movimiento_caja.index', 'caja.egreso.index',
    'caja.recibos_caja.index']])
    <li class="@yield('caja-chica-active')">
        <a href="#"><i class="fas fa-cash-register"></i> <span class="nav-label">Caja Chica</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'caja.caja.index')
                <li class="@yield('caja-active')"><a href="{{ route('Caja.index') }}"><i class="fa fa-archive"></i>Cajas</a></li>
            @endcan
            @can('haveaccess', 'caja.movimiento_caja.index')
                <li class="@yield('caja-movimiento-active')"><a href="{{ route('Caja.Movimiento.index') }}"><i class="fa fa-registered"></i>
                        Apertura y Cierre Caja</a></li>
            @endcan
            @can('haveaccess', 'caja.egreso.index')
                <li class="@yield('egreso-active')"> <a href="{{ route('Egreso.index') }}"><i class="fa fa-arrow-right"></i>
                        Egreso</a></li>
            @endcan
            @can('haveaccess', 'caja.recibos_caja.index')
                <li class="@yield('recibos_caja-active')"> <a href="{{ route('recibos_caja.index') }}"><i class="fa fa-arrow-right"></i>
                        Recibos Caja</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['compra.proveedor.index', 'compra.orden.index', 'compra.documento_compra.index']])
    <li class="@yield('compras-active')">
        <a href="#"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Compras</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'compra.proveedor.index')
                <li class="@yield('proveedor-active')"><a href="{{ route('compras.proveedor.index') }}">Proveedores</a></li>
            @endcan
            @can('haveaccess', 'compra.orden.index')
                <li class="@yield('orden-compra-active')"><a href="{{ route('compras.orden.index') }}">Orden Compra</a></li>
            @endcan
            @can('haveaccess', 'compra.documento_compra.index')
                <li class="@yield('documento-active')"><a href="{{ route('compras.documento.index') }}">Doc. Compra</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['venta.tipo_cliente.index', 'venta.cliente.index', 'venta.cotizacion.index',
    'venta.documento_venta.index', 'venta.ventascaja.index', 'venta.guia.index', 'venta.resumenes.index',
    'venta.despachos.index', 'venta.reservas.index']])
    <li class="@yield('ventas-active')">
        <a href="#"><i class="fa fa-signal"></i> <span class="nav-label">Ventas</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'venta.tipo_cliente.index')
                <li class="@yield('tipo_cliente-active')"><a href="{{ route('ventas.tipo_cliente.index') }}">Tipos Clientes</a></li>
            @endcan
            @can('haveaccess', 'venta.cliente.index')
                <li class="@yield('clientes-active')"><a href="{{ route('ventas.cliente.index') }}">Clientes</a></li>
            @endcan
            @can('haveaccess', 'venta.cotizacion.index')
                <li class="@yield('cotizaciones-active')"><a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a></li>
            @endcan
            @can('haveaccess', 'venta.documento_venta.index')
                <li class="@yield('documento-active')"><a href="{{ route('ventas.documento.index') }}">Doc. Venta</a></li>
            @endcan
            @can('haveaccess', 'venta.ventascaja.index')
                <li class="@yield('ventas-caja-active')"><a href="{{ route('ventas.caja.index') }}">Caja</a></li>
            @endcan
            @can('haveaccess', 'venta.despachos.index')
                <li class="@yield('despachos-active')"><a href="{{ route('ventas.despachos.index') }}">Despacho</a></li>
            @endcan
            @can('haveaccess', 'venta.reservas.index')
                <li class="@yield('reservas-active')"><a href="{{ route('ventas.reservas.index') }}">Reserva</a></li>
            @endcan
            @can('haveaccess', 'venta.guia.index')
                <li class="@yield('guias-remision-active')"><a href="{{ route('ventas.guiasremision.index') }}">Guias de Remision</a></li>
            @endcan
            @can('haveaccess', 'venta.resumenes.index')
                <li class="@yield('resumenes-active')"><a href="{{ route('ventas.resumenes.index') }}">Resúmenes</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['pedido.pedido.index', 'pedido.pedidos_detalles.index']])
    <li class="@yield('pedidos-active')">
        <a href="#"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Pedidos</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'pedido.pedido.index')
                <li class="@yield('pedido-active')"><a href="{{ route('pedidos.pedido.index') }}">Pedidos</a></li>
            @endcan
            @can('haveaccess', 'pedido.pedidos_detalles.index')
                <li class="@yield('pedidos-detalles-active')"><a href="{{ route('pedidos.pedidos_detalles.index') }}">Detalles</a></li>
            @endcan
            @can('haveaccess', 'pedido.ordenes_produccion.index')
                <li class="@yield('ordenes-pedido-active')"><a href="{{ route('pedidos.ordenes_produccion.index') }}">Órdenes</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['almacen.almacen.index', 'almacen.categoria.index', 'almacen.marca.index',
    'almacen.modelo.index', 'almacen.color.index', 'almacen.talla.index', 'almacen.producto.index',
    'almacen.nota_ingreso.index', 'almacen.nota_salida.index', 'almacen.solicitudes_traslado.index',
    'almacen.traslados.index', 'almacen.vehiculos.index', 'almacen.conductores.index']])
    <li class="@yield('almacenes-active')">
        <a href="#">
            <i class="fas fa-warehouse"></i>
            <span class="nav-label">Almacén</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'almacen.almacen.index')
                <li class="@yield('almacen-active')"><a href="{{ route('almacenes.almacen.index') }}">Almacén</a></li>
            @endcan
            @can('haveaccess', 'almacen.categoria.index')
                <li class="@yield('categoria-active')"><a href="{{ route('almacenes.categorias.index') }}">Categoria</a></li>
            @endcan
            @can('haveaccess', 'almacen.marca.index')
                <li class="@yield('marca-active')"><a href="{{ route('almacenes.marcas.index') }}">Marca</a></li>
            @endcan

            @can('haveaccess', 'almacen.modelo.index')
                <li class="@yield('modelo-active')"><a href="{{ route('almacenes.modelos.index') }}">Modelo</a></li>
            @endcan
            @can('haveaccess', 'almacen.color.index')
                <li class="@yield('color-active')"><a href="{{ route('almacenes.colores.index') }}">Color</a></li>
            @endcan
            @can('haveaccess', 'almacen.talla.index')
                <li class="@yield('talla-active')"><a href="{{ route('almacenes.tallas.index') }}">Talla</a></li>
            @endcan

            @can('haveaccess', 'almacen.producto.index')
                <li class="@yield('producto-active')"><a href="{{ route('almacenes.producto.index') }}">Producto</a></li>
            @endcan

            @can('haveaccess', 'almacen.nota_ingreso.index')
                <li class="@yield('nota_ingreso-active')"><a href="{{ route('almacenes.nota_ingreso.index') }}">Nota Ingreso</a></li>
            @endcan
            @can('haveaccess', 'almacen.nota_salida.index')
                <li class="@yield('nota_salidad-active')"><a href="{{ route('almacenes.nota_salidad.index') }}">Nota Salida</a></li>
            @endcan
            @can('haveaccess', 'almacen.solicitudes_traslado.index')
                <li class="@yield('solicitudes_traslado-active')"><a href="{{ route('almacenes.solicitud_traslado.index') }}">Solicitudes
                        Traslado</a></li>
            @endcan
            @can('haveaccess', 'almacen.traslados.index')
                <li class="@yield('traslados-active')"><a href="{{ route('almacenes.traslados.index') }}">Traslados</a></li>
            @endcan
            @can('haveaccess', 'almacen.vehiculos.index')
                <li class="@yield('vehiculos-active')"><a href="{{ route('almacenes.vehiculos.index') }}">Vehículos</a></li>
            @endcan
            @can('haveaccess', 'almacen.conductores.index')
                <li class="@yield('conductores-active')"><a href="{{ route('almacenes.conductores.index') }}">Conductores</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['cuenta.cuenta_proveedor.index', 'cuenta.cuenta_cliente.index']])
    <li class="@yield('cuentas-active')">
        <a href="#">
            <i class="fas fa-wallet"></i>
            <span class="nav-label">Cuentas</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'cuenta.cuenta_proveedor.index')
                <li class="@yield('cuenta-proveedor-active')"><a href="{{ route('cuentaProveedor.index') }}"><span
                            class="nav-label">Proveedor</span></a></li>
            @endcan
            @can('haveaccess', 'cuenta.cuenta_cliente.index')
                <li class="@yield('cuenta-cliente-active')"><a href="{{ route('cuentaCliente.index') }}"><span
                            class="nav-label">Cliente</span></a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), [
    'consulta.consulta_documento.index',
    'consulta.consulta_venta_documento.index',
    'consulta.consulta_venta_cotizacion.index',
    'consulta.consulta_venta_documento_no.index',
    'consulta.consulta_compras_orden.index',
    'consulta.consulta_compras_documento.index',
    'consulta.consulta_cuenta_proveedor.index',
    'consulta.consulta_cuenta_cliente.index',
    'consulta.consulta_nota_salida.index',
    'consulta.consulta_nota_ingreso.index',
    'consulta.consulta_utilidad_bruta.index'
]])
    <li class="@yield('consulta-active')">

        <a href="#">
            <i class="fa fa-question-circle"></i>
            <span class="nav-label">Consulta</span>
            <span class="fa arrow"></span>
        </a>

        <ul class="nav nav-second-level collapse">

            @can('haveaccess', 'consulta.consulta_documento.index')
                <li class="@yield('consulta-comprobantes-active')">
                    <a href="{{ route('consultas.documento.index') }}">
                        Documentos
                    </a>
                </li>
            @endcan

            {{-- VENTAS --}}
            <li class="@yield('consulta-ventas-active')">

                <a href="#">
                    Ventas
                    <span class="fa arrow"></span>
                </a>

                <ul class="nav nav-third-level">

                    @can('haveaccess', 'consulta.consulta_venta_documento.index')
                        <li class="@yield('consulta-ventas-cotizacion-active')">
                            <a href="{{ route('consultas.ventas.cotizacion.index') }}">
                                Cotización
                            </a>
                        </li>
                    @endcan

                    @can('haveaccess', 'consulta.consulta_venta_cotizacion.index')
                        <li class="@yield('consulta-ventas-documento-active')">
                            <a href="{{ route('consultas.ventas.documento.index') }}">
                                Doc. Venta
                            </a>
                        </li>
                    @endcan

                    @can('haveaccess', 'consulta.consulta_venta_documento_no.index')
                        <li class="@yield('consulta-ventas-documento-no-active')">
                            <a href="{{ route('consultas.ventas.documento.no.index') }}">
                                No enviados
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

            {{-- ALERTAS --}}
            <li class="@yield('consulta-alertas-active')">

                <a href="#">
                    Alertas
                    <span class="fa arrow"></span>
                </a>

                <ul class="nav nav-third-level">

                    <li class="@yield('consulta-ventas-alertas-envio-active')">
                        <a href="{{ route('consultas.ventas.alerta.envio') }}">
                            Documentos
                        </a>
                    </li>

                    <li class="@yield('consulta-ventas-alertas-regularize-active')">
                        <a href="{{ route('consultas.ventas.alerta.regularize') }}">
                            CDR
                        </a>
                    </li>

                    <li class="@yield('consulta-ventas-alertas-notas-active')">
                        <a href="{{ route('consultas.ventas.alerta.notas') }}">
                            Notas
                        </a>
                    </li>

                    <li class="@yield('consulta-ventas-alertas-guias-active')">
                        <a href="{{ route('consultas.ventas.alerta.guias') }}">
                            Guias
                        </a>
                    </li>

                </ul>
            </li>

            {{-- COMPRAS --}}
            <li class="@yield('consulta-compras-active')">

                <a href="#">
                    Compras
                    <span class="fa arrow"></span>
                </a>

                <ul class="nav nav-third-level">

                    @can('haveaccess', 'consulta.consulta_compras_orden.index')
                        <li class="@yield('consulta-compras-orden-active')">
                            <a href="{{ route('consultas.compras.orden.index') }}">
                                Orden de Compra
                            </a>
                        </li>
                    @endcan

                    @can('haveaccess', 'consulta.consulta_compras_documento.index')
                        <li class="@yield('consulta-compras-documento-active')">
                            <a href="{{ route('consultas.compras.documento.index') }}">
                                Doc. Compras
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>

            @can('haveaccess', 'consulta.consulta_cuenta_proveedor.index')
                <li class="@yield('cuenta_proveedor-active')">
                    <a href="{{ route('consultas.cuentas.proveedor.index') }}">
                        Cuenta Proveedor
                    </a>
                </li>
            @endcan

            @can('haveaccess', 'consulta.consulta_cuenta_cliente.index')
                <li class="@yield('cuenta_cliente-active')">
                    <a href="{{ route('consultas.cuentas.cliente.index') }}">
                        Cuenta Cliente
                    </a>
                </li>
            @endcan

            @can('haveaccess', 'consulta.consulta_nota_salida.index')
                <li class="@yield('nota_salida_consulta-active')">
                    <a href="{{ route('consultas.notas.salidad.index') }}">
                        Nota Salida
                    </a>
                </li>
            @endcan

            @can('haveaccess', 'consulta.consulta_nota_ingreso.index')
                <li class="@yield('nota_ingreso_consulta-active')">
                    <a href="{{ route('consultas.notas.ingreso.index') }}">
                        Nota Ingreso
                    </a>
                </li>
            @endcan

            @can('haveaccess', 'consulta.consulta_pos_egreso.index')
                <li class="@yield('pos_egreso-active')">
                    <a href="{{ route('consultas.pos.egreso.index') }}">
                        Egreso
                    </a>
                </li>
            @endcan

            @can('haveaccess', 'consulta.consulta_utilidad_bruta.index')
                <li class="@yield('utilidad_bruta-active')">
                    <a href="{{ route('consultas.caja.utilidad.index') }}">
                        Utilidad Bruta
                    </a>
                </li>
            @endcan

        </ul>

    </li>
@endcan

@can('restore', [Auth::user(), ['contabilidad.documentos.index']])
    <li class="@yield('contabilidad-active')">
        <a href="#"><i class="fa fa-question-circle"></i> <span class="nav-label">Contabilidad </span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'contabilidad.documentos.index')
                <li class="@yield('contabilidad_documentos-active')"><a href="{{ route('consultas.contabilidad.index') }}">Documentos</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['reporte.reporte_cajadiaria.index', 'reporte.reporte_egreso.index',
    'reporte.reporte_venta.index', 'reporte.reporte_compra.index', 'reporte.reporte_nota_salida.index',
    'reporte.reporte_nota_ingreso.index', 'reporte.reporte_cuenta_cobrar.index', 'reporte.reporte_cuenta_pagar.index',
    'reporte.reporte_stock_valorizado.index']])
    <li class="@yield('reporte-active')">
        <a href="#">
            <i class="fas fa-chart-bar"></i>
            <span class="nav-label">Reportes</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'reporte_cajadiaria.index')
                <li class="@yield('caja_diaria-active')"><a href="{{ route('reporte.pos.cajadiaria') }}">Caja Diaria</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_egreso.index')
                <li class="@yield('reporte_pos_egreso-active')"><a href="{{ route('reporte.pos.egreso') }}">Egreso</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_venta.index')
                <li class="@yield('ventas_reporte-active')"><a href="{{ route('reporte.ventas.documento') }}">Ventas</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_compra.index')
                <li class="@yield('compras_reporte-active')"><a href="{{ route('reporte.compras.documento') }}">Compras</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_nota_salida.index')
                <li class="@yield('nota_salida_reporte-active')"><a href="{{ route('reporte.notas.salida') }}">Nota Salida</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_nota_ingreso.index')
                <li class="@yield('nota_ingreso_reporte-active')"><a href="{{ route('reporte.notas.ingreso') }}">Nota Ingreso</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_cuenta_cobrar.index')
                <li class="@yield('cuentas_x_cobrar_reporte-active')"><a href="{{ route('reporte.cuentas.cliente') }}">Cuentas por Cobrar</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_cuenta_pagar.index')
                <li class="@yield('cuentas_x_pagar_reporte-active')"><a href="{{ route('reporte.cuentas.proveedor') }}">Cuentas por Pagar</a></li>
            @endcan
            @can('haveaccess', 'reporte.reporte_stock_valorizado.index')
                <li class="@yield('stock_valorizado_reporte-active')"><a href="{{ route('reporte.producto.stockvalorizado.index') }}">Stock
                        Valorizado</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['kardex.kardex_proveedor.index', 'kardex.kardex_cliente.index',
    'kardex.kardex_producto.index', 'kardex.kardex_venta.index', 'kardex.kardex_cuenta.index']])
    <li class="@yield('kardex-active')">
        <a href="#"><i class="fa fa-exclamation"></i> <span class="nav-label">Kardex</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'kardex.kardex_proveedor.index')
                <li><a class="@yield('proveedor_kardex-active')" href="{{ route('consultas.kardex.proveedor.index') }}">Proveedor</a></li>
            @endcan
            @can('haveaccess', 'kardex.kardex_cliente.index')
                <li class="@yield('cliente_kardex-active')"><a href="{{ route('consultas.kardex.cliente.index') }}">Cliente</a></li>
            @endcan
            @can('haveaccess', 'kardex.kardex_producto.index')
                <li class="@yield('producto_kardex-active')"><a href="{{ route('consultas.kardex.producto.index') }}">Producto</a></li>
            @endcan
            @can('haveaccess', 'kardex.kardex_venta.index')
                <li class="@yield('venta_kardex-active')"><a href="{{ route('consultas.kardex.venta.index') }}">Venta</a></li>
            @endcan
            @can('haveaccess', 'kardex.kardex_cuenta.index')
                <li class="@yield('kardex_cuenta-active')"><a href="{{ route('consultas.kardex.cuenta.index') }}">Cuenta</a></li>
            @endcan
        </ul>
    </li>
@endcan


@can('restore', [Auth::user(), ['mantenimiento.colaborador.index', 'mantenimiento.empresa.index',
    'mantenimiento.sedes.index', 'mantenimiento.condicion.index', 'mantenimiento.tabla.index',
    'mantenimiento.configuracion.index', 'mantenimiento.metodo_entrega.index', 'mantenimiento.cuentas.index',
    'mantenimiento.tipo_pago.index']])
    <li class="@yield('mantenimiento-active')">
        <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Mantenimento</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'mantenimiento.colaborador.index')
                <li class="@yield('colaboradores-active')"><a href="{{ route('mantenimiento.colaborador.index') }}">Colaboradores</a>
                </li>
            @endcan
            @can('haveaccess', 'mantenimiento.empresa.index')
                <li class="@yield('empresas-active')"><a href="{{ route('mantenimiento.empresas.index') }}">Empresas</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.sedes.index')
                <li class="@yield('sedes-active')"><a href="{{ route('mantenimiento.sedes.index') }}">Sedes</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.condicion.index')
                <li class="@yield('condicion-active')"><a href="{{ route('mantenimiento.condiciones.index') }}">Condiciones de
                        Pago</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.tabla.index')
                <li class="@yield('tablas-active')"><a href="{{ route('mantenimiento.tabla.general.index') }}">Tablas
                        Generales</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.configuracion.index')
                <li class="@yield('configuracion-active')"><a href="{{ route('configuracion.index') }}">Configuración</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.metodo_entrega.index')
                <li class="@yield('metodo_entrega-active')"><a href="{{ route('mantenimiento.metodo_entrega.index') }}">Métodos
                        Entrega</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.tipo_pago.index')
                <li class="@yield('tipo_pago-active')"><a href="{{ route('mantenimiento.tipo_pago.index') }}">Tipo Pago</a></li>
            @endcan
            @can('haveaccess', 'mantenimiento.cuentas.index')
                <li class="@yield('cuentas_bancarias-active')"><a href="{{ route('mantenimiento.cuentas.index') }}">Cuentas</a></li>
            @endcan
        </ul>
    </li>
@endcan

@can('restore', [Auth::user(), ['seguridad.user.index', 'seguridad.role.index']])
    <li class="@yield('seguridad-active')">
        <a href="#"><i class="fa fa-key"></i> <span class="nav-label">Seguridad</span><span
                class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
            @can('haveaccess', 'seguridad.user.index')
                <li class="@yield('users-active')"><a href="{{ route('seguridad.user.index') }}">Usuarios</a></li>
            @endcan
            @can('haveaccess', 'seguridad.role.index')
                <li class="@yield('roles-active')"><a href="{{ route('seguridad.role.index') }}">Roles</a></li>
            @endcan
        </ul>
    </li>
@endcan
