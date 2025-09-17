@extends('layout')
@section('content')

@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>Generar Pedido #{{ $cotizacion->id }}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Generar Pedido</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            @include('ventas.cotizaciones.forms.form_convert_to_pedido')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('ventas.cotizacion.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>
                                    <button type="submit" id="btn_grabar" form="form-convert-to-pedido"
                                        class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
    const tallas = @json($tallas);
    const productosPrevios = @json($detalles);

    let dtCotizacionDetalle = null;
    let carrito = [];
    const amountsCotizacion = {
        subtotal: 0,
        embalaje: 0,
        envio: 0,
        total: 0,
        igv: 0,
        totalPagar: 0,
        monto_descuento: 0
    }

    const tfootSubtotal = document.querySelector('.subtotal');
    const tfootEmbalaje = document.querySelector('.embalaje');
    const tfootEnvio = document.querySelector('.envio');
    const tfootTotal = document.querySelector('.total');
    const tfootIgv = document.querySelector('.igv');
    const tfootTotalPagar = document.querySelector('.total-pagar');
    const tfootDescuento = document.querySelector('.descuento');

    document.addEventListener('DOMContentLoaded', async () => {
        cargarProductosPrevios();
        events();
    })

    function events() {

        document.querySelector('#form-convert-to-pedido').addEventListener('submit', (e) => {
            e.preventDefault();
            generarPedidoDeCotizacion(e.target);
        })
    }

    function generarPedidoDeCotizacion(formCotPedido) {
        const cotizacion = @json($cotizacion);
        const textHtml = `
                            <div class="container-fluid text-left">
                                <div class="row small">

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-user-shield text-primary"></i>
                                        <b class="text-muted"> REGISTRADOR:</b><br>
                                        <span>${cotizacion.registrador_nombre}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-calendar-alt text-success"></i>
                                        <b class="text-muted"> FECHA REGISTRO:</b><br>
                                        <span>{{ date('Y-m-d') }}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-warehouse text-warning"></i>
                                        <b class="text-muted"> ALMACÉN:</b><br>
                                        <span>${cotizacion.almacen_nombre}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-file-contract text-info"></i>
                                        <b class="text-muted"> CONDICIÓN:</b><br>
                                        <span>{{ $condicion->descripcion }}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-user-tie text-danger"></i>
                                        <b class="text-muted"> CLIENTE:</b><br>
                                        <span>${cotizacion.cliente_nombre}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-phone text-success"></i>
                                        <b class="text-muted"> TELÉFONO:</b><br>
                                        <span>${cotizacion.telefono ?? '-'}</span>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <i class="fas fa-calendar-day text-primary"></i>
                                        <b class="text-muted"> FECHA PROPUESTA DE ENTREGA:</b><br>
                                        <span>${cotizacion.fecha_propuesta ?? '-'}</span>
                                    </div>

                                    <div class="col-12 mb-2">
                                        <i class="fas fa-sticky-note text-secondary"></i>
                                        <b class="text-muted"> OBSERVACIÓN:</b><br>
                                        <span>${cotizacion.observacion ?? '-'}</span>
                                    </div>

                                </div>
                            </div>
                        `;

        Swal.fire({
            title: `Convertir Cotización N° ${cotizacion.id} a Pedido`,
            html: textHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Sí',
            cancelButtonText: "No, Cancelar",
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Generando Pedido...',
                    text: 'Por favor, espere.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    const formData = new FormData(formCotPedido);
                    formData.append('cotizacion_id', @json($cotizacion->id))
                    const res = await axios.post(route('ventas.cotizacion.pedido'), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = "{{ route('pedidos.pedido.index') }}";

                    }
                } catch (error) {
                    toastr.error(res.data.message, 'ERROR AL GENERAR EL PEDIDO');
                } finally {
                    Swal.close();
                }

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    }

    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal = () => {
        let subtotal = 0;

        carrito.forEach((p) => {
            p.tallas.forEach((t) => {
                subtotal += parseFloat(p.precio_venta) * parseFloat(t.cantidad);
            })

            p.subtotal = subtotal;
            subtotal = 0;
        })
    }

    //=========== CALCULAR MONTOS =======
    const calcularMontos = () => {
        let subtotal = 0;
        let embalaje = tfootEmbalaje.value ? parseFloat(tfootEmbalaje.value) : 0;
        let envio = tfootEnvio.value ? parseFloat(tfootEnvio.value) : 0;
        let total = 0;
        let igv = 0;
        let total_pagar = 0;
        let descuento = 0;

        //====== subtotal es la suma de todos los productos ======
        carrito.forEach((c) => {
            if (c.porcentaje_descuento === 0) {
                subtotal += parseFloat(c.subtotal);
            } else {
                subtotal += parseFloat(c.subtotal_nuevo);
            }
            descuento += parseFloat(c.monto_descuento);
        })

        total_pagar = subtotal + embalaje + envio;
        total = total_pagar / 1.18;
        igv = total_pagar - total;

        tfootTotalPagar.textContent = 'S/. ' + total_pagar.toFixed(2);
        tfootIgv.textContent = 'S/. ' + igv.toFixed(2);
        tfootTotal.textContent = 'S/. ' + total.toFixed(2);
        tfootSubtotal.textContent = 'S/. ' + subtotal.toFixed(2);
        tfootDescuento.textContent = 'S/. ' + descuento.toFixed(2);

        amountsCotizacion.totalPagar = total_pagar.toFixed(2);
        amountsCotizacion.igv = igv.toFixed(2);
        amountsCotizacion.total = total.toFixed(2);
        amountsCotizacion.embalaje = embalaje.toFixed(2);
        amountsCotizacion.envio = envio.toFixed(2);
        amountsCotizacion.subtotal = subtotal.toFixed(2);
        amountsCotizacion.monto_descuento = descuento.toFixed(2);
    }

    function cargarEmbalajeEnvioPrevios() {
        const precioEmbalaje = parseFloat(@json($cotizacion->monto_embalaje));
        const precioEnvio = parseFloat(@json($cotizacion->monto_envio));

        amountsCotizacion.embalaje = precioEmbalaje;
        amountsCotizacion.envio = precioEnvio;
    }

    //====== PINTAR DETALLE COTIZACIÓN ======
    function pintarDetalleCotizacion(carrito) {
        let filas = ``;
        let htmlTallas = ``;
        const tblDetalleBody = document.querySelector('#table_detalle tbody');

        carrito.forEach((c) => {
            htmlTallas = ``;
            filas += `<tr>
                            <th><div style="width:200px;">${c.producto_nombre}</div></th>
                            <th>${c.color_nombre}</th>`;

            //tallas
            tallas.forEach((t) => {
                let cantidad = c.tallas.filter((ct) => {
                    return t.id == ct.talla_id;
                });
                cantidad.length != 0 ? cantidad = cantidad[0].cantidad : cantidad = '';
                htmlTallas += `<td><p style="margin:0;font-weight:bold;">${cantidad}</p></td>`;
            })


            htmlTallas += `   <td style="text-align: right;">
                                    <span class="precio_venta_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.precio_venta:c.precio_venta_nuevo}
                                    </span>
                                </td>
                                <td class="td-subtotal" style="text-align: right;">
                                    <span class="subtotal_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.subtotal:c.subtotal_nuevo}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <input readonly data-producto-id="${c.producto_id}" data-color-id="${c.color_id}"
                                    style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                                    class="form-control detailDescuento"></input>
                                </td>
                            </tr>`;

            filas += htmlTallas;
        })

        tblDetalleBody.innerHTML = filas;

    }

    //======= CALCULAR DESCUENTO ========
    const calcularDescuento = (producto_id, color_id, porcentaje_descuento) => {
        const indiceExiste = carrito.findIndex((c) => {
            return c.producto_id == producto_id && c.color_id == color_id;
        })

        if (indiceExiste !== -1) {
            const producto_color_editar = carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            producto_color_editar.porcentaje_descuento = porcentaje_descuento;
            producto_color_editar.monto_descuento = porcentaje_descuento === 0 ? 0 : producto_color_editar
                .subtotal * (porcentaje_descuento / 100);
            producto_color_editar.precio_venta_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar
                .precio_venta * (1 - porcentaje_descuento / 100)).toFixed(2);
            producto_color_editar.subtotal_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar
                .subtotal * (1 - porcentaje_descuento / 100)).toFixed(2);

            carrito[indiceExiste] = producto_color_editar;


            //==== ACTUALIZANDO PRECIO VENTA Y SUBTOTAL EN EL HTML ====
            const detailPrecioVenta = document.querySelector(
                `.precio_venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`);
            const detailSubtotal = document.querySelector(
                `.subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`);


            if (porcentaje_descuento !== 0) {
                detailPrecioVenta.textContent = producto_color_editar.precio_venta_nuevo;
                detailSubtotal.textContent = producto_color_editar.subtotal_nuevo;
            } else {
                detailPrecioVenta.textContent = producto_color_editar.precio_venta;
                detailSubtotal.textContent = producto_color_editar.subtotal;
            }

        }
    }

    function loadDataTableDetallesCotizacion() {
        dtCotizacionDetalle = new DataTable('#table_detalle', {
            language: {
                "sEmptyTable": "No hay datos disponibles en la tabla",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
                "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Mostrar _MENU_ entradas",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    const cargarProductosPrevios = () => {
        //====== CARGANDO CARRITO ======
        const producto_color_procesados = [];

        productosPrevios.forEach((productoPrevio) => {
            const id = `${productoPrevio.producto_id}-${productoPrevio.color_id}`;

            if (!producto_color_procesados.includes(id)) {
                const producto = {
                    producto_id: productoPrevio.producto_id,
                    producto_nombre: productoPrevio.producto.nombre,
                    color_id: productoPrevio.color.id,
                    color_nombre: productoPrevio.color.descripcion,
                    precio_venta: productoPrevio.precio_unitario,
                    subtotal: 0,
                    subtotal_nuevo: 0,
                    porcentaje_descuento: parseFloat(productoPrevio.porcentaje_descuento),
                    monto_descuento: 0,
                    precio_venta_nuevo: 0,
                    tallas: []
                }

                //==== BUSCANDO SUS TALLAS ====
                const tallas = productosPrevios.filter((t) => {
                    return t.producto_id == productoPrevio.producto_id && t.color_id ==
                        productoPrevio.color_id;
                })

                if (tallas.length > 0) {
                    const producto_color_tallas = [];
                    tallas.forEach((t) => {
                        const talla = {
                            talla_id: t.talla_id,
                            talla_nombre: t.talla.descripcion,
                            cantidad: parseInt(t.cantidad),
                        }
                        producto_color_tallas.push(talla);
                    })
                    producto.tallas = producto_color_tallas;
                }
                producto_color_procesados.push(id);
                carrito.push(producto);
            }
        })


        //===== CALCULAR SUBTOTAL POR FILA DEL DETALLE ======
        calcularSubTotal();
        //===== CARGANDO EMBALAJE Y ENVÍO PREVIO ========
        cargarEmbalajeEnvioPrevios();

        //===== PINTANDO DETALLE ======
        pintarDetalleCotizacion(carrito);
        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c) => {
            calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
        })

        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();

        //====== APLICAMOS DATATABLE A LA TABLA DETALLES COTIZACIÓN =======
        loadDataTableDetallesCotizacion();
    }
</script>
@endpush
