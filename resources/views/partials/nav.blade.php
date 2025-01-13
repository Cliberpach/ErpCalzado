<li class="nav-header">
    <div class="dropdown profile-element">
        @if (auth()->user()->ruta_imagen)
            <img alt="image" alt="{{ auth()->user()->name }}" class="rounded-circle" height="48" width="48"
                src="{{ Storage::url(auth()->user()->ruta_imagen) }}" />
        @else
            <img alt="{{ auth()->user()->name }}" alt="{{ auth()->user()->name }}" class="rounded-circle" height="48"
                width="48" src="{{ asset('img/default.jpg') }}" />

        @endif
        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            <span class="block m-t-xs font-bold">{{ auth()->user()->name }}</span>
            <span class="text-muted text-xs block">Administrador <b class="caret"></b></span>
        </a>
        <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <li><a class="dropdown-item" href="login.html">Cerrar Sesión</a></li>
        </ul>
    </div>
    <div class="logo-element">
        <img src="{{ asset('img/default.png') }}" height="30" width="45">
    </div>
</li>

<li>
    <a href="{{ route('home') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Panel de
            control</span></a>
</li>
@can('haveaccess','utilidad_mensual.index')
<li class="@yield('utilidad-active')">
    <a href="{{ route('consultas.utilidad.index') }}"><i class="fa fa-money"></i> <span class="nav-label">Utilidad</span></a>
</li>
@endcan

@can('restore', [Auth::user(),['caja.index','movimiento_caja.index','egreso.index','recibos_caja.index']])
<li class="@yield('caja-chica-active')">
    <a href="#"><i class="fas fa-cash-register"></i> <span class="nav-label">Caja Chica</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'caja.index')
        <li class="@yield('caja-active')"><a href="{{ route('Caja.index') }}"><i class="fa fa-archive"></i>Cajas</a></li>
        @endcan
        @can('haveaccess', 'movimiento_caja.index')
        <li class="@yield('caja-movimiento-active')"><a href="{{ route('Caja.Movimiento.index') }}"><i class="fa fa-registered"></i> Apertura y Cierre Caja</a></li>
        @endcan
        @can('haveaccess', 'egreso.index')
        <li class="@yield('egreso-active')"> <a href="{{ route('Egreso.index') }}"><i class="fa fa-arrow-right"></i> Egreso</a></li>
        @endcan
        @can('haveaccess', 'recibos_caja.index')
        <li class="@yield('recibos_caja-active')"> <a href="{{ route('recibos_caja.index') }}"><i class="fa fa-arrow-right"></i> Recibos Caja</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['proveedor.index','orden.index','documento_compra.index']])
<li class="@yield('compras-active')">
    <a href="#"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Compras</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'proveedor.index')
        <li class="@yield('proveedor-active')"><a href="{{ route('compras.proveedor.index') }}">Proveedores</a></li>
        @endcan
        @can('haveaccess', 'orden.index')
        <li class="@yield('orden-compra-active')"><a href="{{ route('compras.orden.index') }}">Orden Compra</a></li>
        @endcan
        @can('haveaccess', 'documento_compra.index')
        <li class="@yield('documento-active')"><a href="{{ route('compras.documento.index') }}">Doc. Compra</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['cliente.index','cotizacion.index','documento_venta.index','ventascaja.index','guia.index','resumenes.index','pedidos.index','despachos.index']])
<li class="@yield('ventas-active')">
    <a href="#"><i class="fa fa-signal"></i> <span class="nav-label">Ventas</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'cliente.index')
        <li class="@yield('clientes-active')"><a href="{{ route('ventas.cliente.index') }}">Clientes</a></li>
        @endcan
        @can('haveaccess', 'cotizacion.index')
        <li class="@yield('cotizaciones-active')"><a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a></li>
        @endcan
        @can('haveaccess', 'documento_venta.index')
        <li class="@yield('documento-active')"><a href="{{ route('ventas.documento.index') }}">Doc. Venta</a></li>
        @endcan
        @can('haveaccess', 'ventascaja.index')
        <li class="@yield('ventas-caja-active')"><a href="{{ route('ventas.caja.index') }}">Caja</a></li>
        @endcan
        @can('haveaccess', 'despachos.index')
        <li class="@yield('despachos-active')"><a href="{{ route('ventas.despachos.index') }}">Despacho</a></li>
        @endcan
        @can('haveaccess', 'guia.index')
        <li class="@yield('guias-remision-active')"><a href="{{ route('ventas.guiasremision.index') }}">Guias de Remision</a></li>
        @endcan
        @can('haveaccess', 'resumenes.index')
        <li class="@yield('resumenes-active')"><a href="{{ route('ventas.resumenes.index') }}">Resúmenes</a></li>
        @endcan
        {{-- @can('haveaccess', 'pedidos.index')
        <li class="@yield('pedidos-active')"><a href="{{ route('ventas.pedidos.index') }}">Pedidos</a></li>
        @endcan
        @can('haveaccess', 'pedidos_detalles.index')
        <li class="@yield('pedidos-active')"><a href="{{ route('ventas.pedidos_detalles.index') }}">Pedidos Detalles</a></li>
        @endcan --}}
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['pedido.index','pedidos_detalles.index']])
<li class="@yield('pedidos-active')">
    <a href="#"><i class="fas fa-file-invoice-dollar"></i> <span class="nav-label">Pedidos</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'pedido.index')
        <li class="@yield('pedido-active')"><a href="{{ route('pedidos.pedido.index') }}">Pedidos</a></li>
        @endcan
        @can('haveaccess', 'pedidos_detalles.index')
        <li class="@yield('pedidos-detalles-active')"><a href="{{route('pedidos.pedidos_detalles.index')}}">Detalles</a></li>
        @endcan
        @can('haveaccess', 'ordenes_produccion.index')
        <li class="@yield('ordenes-pedido-active')"><a href="{{route('pedidos.ordenes_produccion.index')}}">Órdenes</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['almacen.index','categoria.index','marca.index','producto.index','nota_ingreso.index','nota_salida.index','traslados.index','vehiculos.index','conductores.index']])
<li class="@yield('almacenes-active')">
    <a href="#"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Almacén</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'almacen.index')
        <li class="@yield('almacen-active')"><a href="{{ route('almacenes.almacen.index') }}">Almacén</a></li>
        @endcan
        @can('haveaccess', 'categoria.index')
        <li class="@yield('categoria-active')"><a href="{{ route('almacenes.categorias.index') }}">Categoria</a></li>
        @endcan
        @can('haveaccess', 'marca.index')
            <li class="@yield('marca-active')"><a href="{{ route('almacenes.marcas.index') }}">Marca</a></li>
        @endcan

        @can('haveaccess', 'modelo.index')
            <li class="@yield('modelo-active')"><a href="{{ route('almacenes.modelos.index') }}">Modelo</a></li>
        @endcan
        @can('haveaccess', 'color.index')
            <li class="@yield('color-active')"><a href="{{ route('almacenes.colores.index') }}">Color</a></li>
        @endcan
        @can('haveaccess', 'talla.index')
            <li class="@yield('talla-active')"><a href="{{ route('almacenes.tallas.index') }}">Talla</a></li>
        @endcan

        @can('haveaccess', 'producto.index')
            <li class="@yield('producto-active')"><a href="{{ route('almacenes.producto.index') }}">Producto</a></li>
        @endcan
        @can('haveaccess', 'nota_ingreso.index')
            <li class="@yield('nota_ingreso-active')"><a href="{{ route('almacenes.nota_ingreso.index') }}">Nota Ingreso</a></li>
        @endcan
        @can('haveaccess', 'nota_salida.index')
            <li class="@yield('nota_salidad-active')"><a href="{{ route('almacenes.nota_salidad.index') }}">Nota Salida</a></li>
        @endcan
        @can('haveaccess', 'traslados.index')
            <li class="@yield('traslados-active')"><a href="{{ route('almacenes.traslados.index') }}">Traslados</a></li>
        @endcan
        @can('haveaccess', 'vehiculos.index')
            <li class="@yield('vehiculos-active')"><a href="{{ route('almacenes.vehiculos.index') }}">Vehículos</a></li>
        @endcan
        @can('haveaccess', 'conductores.index')
            <li class="@yield('conductores-active')"><a href="{{ route('almacenes.conductores.index') }}">Conductores</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['cuenta_proveedor.index','cuenta_cliente.index']])
<li class="@yield('cuentas-active')">
    <a href="#"><i class="fa fa-money"></i> <span class="nav-label">Cuentas </span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'cuenta_proveedor.index')
        <li class="@yield('cuenta-proveedor-active')"><a href="{{ route('cuentaProveedor.index') }}"><span class="nav-label">Proveedor</span></a></li>
        @endcan
        @can('haveaccess', 'cuenta_cliente.index')
        <li class="@yield('cuenta-cliente-active')"><a href="{{ route('cuentaCliente.index') }}"><span class="nav-label">Cliente</span></a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['consulta_documento.index','consulta_venta_documento.index','consulta_venta_cotizacion.index','consulta_venta_documento_no.index','consulta_compras_orden.index','consulta_compras_documento.index','consulta_cuenta_proveedor.index','consulta_cuenta_cliente.index','consulta_nota_salida.index','consulta_nota_ingreso.index','consulta_utilidad_bruta.index']])
<li class="@yield('consulta-active')">
    <a href="#"><i class="fa fa-question-circle"></i> <span class="nav-label">Consulta </span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess','consulta_documento.index')
        <li class="@yield('consulta-comprobantes-active')"><a href="{{ route('consultas.documento.index') }}">Documentos</a></li>
        @endcan

        @can('restore', [Auth::user(),['consulta_venta_documento.index','consulta_venta_cotizacion.index','consulta_venta_documento_no.index']])
        <li class="@yield('consulta-ventas-active')">
            <a href="#">Ventas <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level">
                @can('haveaccess','consulta_venta_documento.index')
                <li class="@yield('consulta-ventas-cotizacion-active')"><a href="{{ route('consultas.ventas.cotizacion.index') }}">Cotización</a></li>
                @endcan
                @can('haveaccess','consulta_venta_cotizacion.index')
                <li class="@yield('consulta-ventas-documento-active')"><a href="{{ route('consultas.ventas.documento.index') }}">Doc. Venta</a></li>
                @endcan
                @can('haveaccess','consulta_venta_documento_no.index')
                <li class="@yield('consulta-ventas-documento-no-active')"><a href="{{ route('consultas.ventas.documento.no.index') }}">No enviados</a></li>
                @endcan
            </ul>
        </li>
        @endcan

        <li class="@yield('consulta-alertas-active')">
            <a href="#">Alertas <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level">
                <li class="@yield('consulta-ventas-alertas-envio-active')"><a href="{{ route('consultas.ventas.alerta.envio') }}">Documentos</a></li>
                <li class="@yield('consulta-ventas-alertas-regularize-active')"><a href="{{ route('consultas.ventas.alerta.regularize') }}">CDR</a></li>
                <li class="@yield('consulta-ventas-alertas-notas-active')"><a href="{{ route('consultas.ventas.alerta.notas') }}">Notas</a></li>
                <li class="@yield('consulta-ventas-alertas-guias-active')"><a href="{{ route('consultas.ventas.alerta.guias') }}">Guias</a></li>
            </ul>
        </li>

        @can('restore', [Auth::user(),['consulta_compras_orden.index','consulta_compras_documento.index']])
        <li class="@yield('consulta-compras-active')">
            <a href="#">Compras <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level">
                @can('haveaccess','consulta_compras_orden.index')
                <li class="@yield('consulta-compras-orden-active')"><a href="{{ route('consultas.compras.orden.index') }}">Orden de Compra</a></li>
                @endcan
                @can('haveaccess','consulta_compras_documento.index')
                <li class="@yield('consulta-compras-documento-active')"><a href="{{ route('consultas.compras.documento.index') }}">Doc. Compras</a></li>
                @endcan
            </ul>
        </li>
        @endcan
        @can('haveaccess','consulta_cuenta_proveedor.index')
        <li class="@yield('cuenta_proveedor-active')"><a href="{{ route('consultas.cuentas.proveedor.index') }}">Cuenta Proveedor</a></li>
        @endcan
        @can('haveaccess','consulta_cuenta_cliente.index')
        <li class="@yield('cuenta_cliente-active')"><a href="{{ route('consultas.cuentas.cliente.index') }}">Cuenta Cliente</a></li>
        @endcan
        @can('haveaccess','consulta_nota_salida.index')
        <li class="@yield('nota_salida_consulta-active')"><a href="{{ route('consultas.notas.salidad.index') }}">Nota Salida</a></li>
        @endcan
        @can('haveaccess','consulta_nota_ingreso.index')
        <li class="@yield('nota_ingreso_consulta-active')"><a href="{{ route('consultas.notas.ingreso.index') }}">Nota Ingreso</a></li>
        @endcan
        @can('haveaccess','consulta_pos_egreso.index')
        <li class="@yield('pos_egreso-active')"><a href="{{ route('consultas.pos.egreso.index') }}">Egreso</a></li>
        @endcan
        @can('haveaccess','consulta_utilidad_bruta.index')
        <li class="@yield('utilidad_bruta-active')"><a href="{{ route('consultas.caja.utilidad.index') }}">Utilidad Bruta</a></li>
        @endcan
      
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['contabilidad.index']])
<li class="@yield('contabilidad-active')">
    <a href="#"><i class="fa fa-question-circle"></i> <span class="nav-label">Contabilidad </span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess','contabilidad.index')
         <li class="@yield('contabilidad_documentos-active')"><a href="{{ route('consultas.contabilidad.index') }}">Documentos</a></li>
        @endcan
    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['reporte_cajadiaria.index','reporte_venta.index','reporte_compra.index','reporte_nota_salida.index','reporte_nota_ingreso.index','reporte_cuenta_cobrar.index','reporte_cuenta_pagar.index','reporte_stock_valorizado.index']])
<li class="@yield('reporte-active')">
    <a href="#"><i class="fa fa-money"></i> <span class="nav-label">Reportes </span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess','reporte_cajadiaria.index')
        <li class="@yield('caja_diaria-active')"><a href="{{ route('reporte.pos.cajadiaria') }}">Caja Diaria</a></li>
        @endcan
        @can('haveaccess','reporte_egreso.index')
        <li class="@yield('reporte_pos_egreso-active')"><a href="{{ route('reporte.pos.egreso') }}">Egreso</a></li>
        @endcan
        @can('haveaccess','reporte_venta.index')
        <li class="@yield('ventas_reporte-active')"><a href="{{ route('reporte.ventas.documento') }}">Ventas</a></li>
        @endcan
        @can('haveaccess','reporte_compra.index')
        <li class="@yield('compras_reporte-active')"><a href="{{ route('reporte.compras.documento') }}">Compras</a></li>
        @endcan
        @can('haveaccess','reporte_nota_salida.index')
        <li class="@yield('nota_salida_reporte-active')"><a href="{{ route('reporte.notas.salida') }}">Nota Salida</a></li>
        @endcan
        @can('haveaccess','reporte_nota_ingreso.index')
        <li class="@yield('nota_ingreso_reporte-active')"><a href="{{ route('reporte.notas.ingreso') }}">Nota Ingreso</a></li>
        @endcan
        @can('haveaccess','reporte_cuenta_cobrar.index')
        <li class="@yield('cuentas_x_cobrar_reporte-active')"><a href="{{ route('reporte.cuentas.cliente') }}">Cuentas por Cobrar</a></li>
        @endcan
        @can('haveaccess','reporte_cuenta_pagar.index')
        <li class="@yield('cuentas_x_pagar_reporte-active')"><a href="{{ route('reporte.cuentas.proveedor') }}">Cuentas por Pagar</a></li>
        @endcan
        @can('haveaccess','reporte_stock_valorizado.index')
        <li class="@yield('stock_valorizado_reporte-active')"><a href="{{ route('reporte.producto.stockvalorizado.index') }}">Stock Valorizado</a></li>
        @endcan
    </ul>
</li>
@endcan



@can('restore', [Auth::user(),['kardex_proveedor.index','kardex_cliente.index','kardex_producto.index','kardex_salida.index','kardex_venta.index']])
<li class="@yield('kardex-active')">
    <a href="#"><i class="fa fa-exclamation"></i> <span class="nav-label">Kardex</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess','kardex_proveedor.index')
        <li ><a class="@yield('proveedor_kardex-active')" href="{{route('consultas.kardex.proveedor.index')}}">Proveedor</a></li>
        @endcan
        @can('haveaccess','kardex_cliente.index')
        <li class="@yield('cliente_kardex-active')"><a href="{{ route('consultas.kardex.cliente.index') }}">Cliente</a></li>
        @endcan
        @can('haveaccess','kardex_producto.index')
        <li class="@yield('producto_kardex-active')"><a href="{{ route('consultas.kardex.producto.index') }}">Producto</a></li>
        @endcan

        {{-- @can('restore', [Auth::user(),['kardex_salida.index','kardex_venta.index']])
        <li class="@yield('salida-kardex-active')">
            <a href="#">Salidas <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level">
                @can('haveaccess','kardex_venta.index')
                <li class="@yield('salida-ventas-active')"><a href="{{ route('consultas.kardex.ventas.index') }}">Ventas</a></li>
                @endcan
                @can('haveaccess','kardex_salida.index')
                <li class="@yield('salida-notas-active')"><a href="{{ route('consultas.kardex.notas.index') }}">Salidas</a></li>
                @endcan
            </ul>
        </li>
        @endcan --}}
    </ul>
</li>
@endcan


@can('restore', [Auth::user(),['colaborador.index','vendedor.index','empresa.index','sedes.index','condicion.index','tabla.index','configuracion.index','metodo_entrega.index']])
<li class="@yield('mantenimiento-active')">
    <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Mantenimento</span><span
            class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'colaborador.index')
        <li class="@yield('colaboradores-active')"><a href="{{ route('mantenimiento.colaborador.index') }}">Colaboradores</a></li>
        @endcan
        @can('haveaccess', 'vendedor.index')
        <li class="@yield('vendedores-active') d-none"><a href="{{ route('mantenimiento.vendedor.index') }}">Vendedores</a></li>
        @endcan
        @can('haveaccess', 'empresa.index')
        <li class="@yield('empresas-active')"><a href="{{ route('mantenimiento.empresas.index') }}">Empresas</a></li>
        @endcan
        @can('haveaccess', 'sedes.index')
        <li class="@yield('sedes-active')"><a href="{{ route('mantenimiento.sedes.index') }}">Sedes</a></li>
        @endcan
        @can('haveaccess','condicion.index')
            <li class="@yield('condicion-active')"><a href="{{ route('mantenimiento.condiciones.index') }}">Condiciones de Pago</a></li>
        @endcan
        @can('haveaccess', 'tabla.index')
        <li class="@yield('tablas-active')"><a href="{{ route('mantenimiento.tabla.general.index') }}">Tablas Generales</a></li>
        @endcan
        @can('haveaccess', 'configuracion.index')
        <li class="@yield('configuracion-active')"><a href="{{ route('configuracion.index') }}">Configuración</a></li>
        @endcan
        @can('haveaccess', 'metodo_entrega.index')
        <li class="@yield('metodo_entrega-active')"><a href="{{ route('mantenimiento.metodo_entrega.index') }}">Métodos Entrega</a></li>
        @endcan

    </ul>
</li>
@endcan

@can('restore', [Auth::user(),['user.index','role.index']])
<li class="@yield('seguridad-active')">
    <a href="#"><i class="fa fa-key"></i> <span class="nav-label">Seguridad</span><span
        class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        @can('haveaccess', 'user.index')
        <li class="@yield('users-active')"><a href="{{ route('user.index') }}">Usuarios</a></li>
        @endcan
        @can('haveaccess', 'role.index')
        <li class="@yield('roles-active')"><a href="{{ route('role.index') }}">Roles</a></li>
        @endcan
    </ul>
</li>

@endcan
