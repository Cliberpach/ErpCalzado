@extends('layout')
@section('content')
    @include('ventas.cotizaciones.modal-cliente')
@section('ventas-active', 'active')
@section('cotizaciones-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>MODIFICAR COTIZACIÓN # {{ $cotizacion->id }}</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('ventas.cotizacion.index') }}">Cotizaciones</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Modificar</strong>
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
                            @include('ventas.cotizaciones.forms.form_edit_cotizacion')
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Detalle de la Cotización</b></h4>
                                </div>
                                <div class="panel-body">

                                    @include('ventas.cotizaciones.table-stocks', [
                                        'carrito' => 'carrito',
                                    ])

                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="table-responsive">
                                            @include('ventas.cotizaciones.table_montos')
                                        </div>
                                    </div>

                                </div>
                            </div>
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
                                    <button type="submit" id="btn_grabar" form="form-cotizacion"
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
    const selectModelo = document.querySelector('#modelo');
    const tableStocksBody = document.querySelector('#table-stocks tbody');
    const tableDetalleBody = document.querySelector('#table-detalle tbody');
    const tokenValue = document.querySelector('input[name="_token"]').value;
    const btnAgregarDetalle = document.querySelector('#btn_agregar');
    const formCotizacion = document.querySelector('#form-cotizacion');

    const tfootSubtotal = document.querySelector('.subtotal');
    const tfootEmbalaje = document.querySelector('.embalaje');
    const tfootEnvio = document.querySelector('.envio');
    const tfootTotal = document.querySelector('.total');
    const tfootIgv = document.querySelector('.igv');
    const tfootTotalPagar = document.querySelector('.total-pagar');
    const tfootDescuento = document.querySelector('.descuento');

    const amountsCotizacion = {
        subtotal: 0,
        embalaje: 0,
        envio: 0,
        total: 0,
        igv: 0,
        totalPagar: 0,
        monto_descuento: 0
    }

    const inputProductos = document.querySelector('#productos_tabla');
    const tallas = @json($tallas);
    const productosPrevios = @json($detalles);

    let modelo_id = null;
    let carrito = [];
    let carritoFormateado = [];
    let dataTableStocksCotizacion = null;
    let dataTableDetallesCotizacion = null;

    document.addEventListener('DOMContentLoaded', async () => {
        loadSelect2();
        events();
        cargarProductosPrevios();
    })

    function events() {

        eventsCliente();

        document.querySelector('.embalaje').addEventListener('input', (e) => {
            let value = e.target.value;

            // 1. Reemplazar todo lo que no sea dígito o punto
            value = value.replace(/[^\d.]/g, '');

            // 2. Evitar que el primer carácter sea un punto
            value = value.replace(/^\./, '');

            // 3. Permitir solo un punto decimal
            value = value.replace(/(\..*)\./g, '$1');

            // 4. Eliminar ceros a la izquierda del entero, pero dejando al menos un dígito
            value = value.replace(/^0+(\d)/, '$1');

            e.target.value = value;

            calcularMontos();
        })

        document.querySelector('.envio').addEventListener('input', (e) => {
            let value = e.target.value;

            // 1. Reemplazar todo lo que no sea dígito o punto
            value = value.replace(/[^\d.]/g, '');

            // 2. Evitar que el primer carácter sea un punto
            value = value.replace(/^\./, '');

            // 3. Permitir solo un punto decimal
            value = value.replace(/(\..*)\./g, '$1');

            // 4. Eliminar ceros a la izquierda del entero, pero dejando al menos un dígito
            value = value.replace(/^0+(\d)/, '$1');

            e.target.value = value;

            calcularMontos();
        })

        document.addEventListener('input', (e) => {

            if (e.target.classList.contains('inputCantidad')) {
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }

            if (e.target.classList.contains('detailDescuento')) {
                //==== CONTROLANDO DE QUE EL VALOR SEA UN NÚMERO ====
                const valor = event.target.value;
                const producto_id = e.target.getAttribute('data-producto-id');
                const color_id = e.target.getAttribute('data-color-id');

                //==== SI EL INPUT ESTA VACÍO ====
                if (valor.trim().length === 0) {
                    //===== CALCULAR DESCUENTO Y PINTARLO ======
                    calcularDescuento(producto_id, color_id, 0);
                    //===== CALCULAR Y PINTAR MONTOS =======
                    calcularMontos();
                    return;
                }

                //===== EXPRESION REGULAR PARA EVITAR CARACTERES NO NUMÉRICOS EN LA CADENA ====
                const regex = /^[0-9]+(\.[0-9]{0,2})?$/;
                //==== BORRAR CARACTER NO NUMÉRICO ====
                if (!regex.test(valor)) {
                    event.target.value = valor.slice(0, -1);
                    return;
                }

                //==== EN CASO SEA NUMÉRICO ====
                let porcentaje_desc = parseFloat(event.target.value);

                //==== EL MÁXIMO DESCUENTO ES 100% ====
                if (porcentaje_desc > 100) {
                    event.target.value = 100;
                    porcentaje_desc = event.target.value;
                }

                //==== CALCULAR DESCUENTO Y PINTARLO ====
                calcularDescuento(producto_id, color_id, porcentaje_desc)
                //===== CALCULAR Y PINTAR MONTOS =======
                calcularMontos();
            }

            //======== INPUT BARCODE ======
            if (e.target.classList.contains('inputBarCode')) {
                if (e.target.value.trim().length === 8) {
                    getProductoBarCode(e.target.value);
                }
            }

        })

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-product')) {
                mostrarAnimacion();
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');
                eliminarProducto(productoId, colorId);
                destruirDataTableDetalleCotizacion();
                clearDetalleCotizacion();
                pintarDetalleCotizacion(carrito);
                calcularMontos();
                loadDataTableDetallesCotizacion();
                clearInputsCantidad();
                loadCarrito();
                ocultarAnimacion();
            }
        })

        formCotizacion.addEventListener('submit', (e) => {

            e.preventDefault();

            toastr.clear();
            if (carrito.length === 0) {
                toastr.error('EL DETALLE DE LA COTIZACIÓN ESTÁ VACÍO!!');
                return;
            }

            formatearDetalle();
            const formData = new FormData(e.target);
            formData.append('lstCotizacion', JSON.stringify(carritoFormateado));
            formData.append('sede_id', @json($sede_id));
            formData.append('registrador_id', @json($registrador->id));
            formData.append('montos_cotizacion', JSON.stringify(amountsCotizacion));
            formData.append('porcentaje_igv', @json($porcentaje_igv));
            actualizarCotizacion(formData);

        })

        //===== AGREGAR DETALLE ======
        btnAgregarDetalle.addEventListener('click', () => {

            mostrarAnimacion();
            // if(!$('#modelo').val()){
            //     toastr.error('DEBE SELECCIONAR UN MODELO','OPERACIÓN INCORRECTA');
            //     return;
            // }
            if (!$('#producto').val()) {
                toastr.error('DEBE SELECCIONAR UN PRODUCTO', 'OPERACIÓN INCORRECTA');
                return;
            }
            if (!$('#precio_venta').val()) {
                toastr.error('DEBE SELECCIONAR UN PRECIO DE VENTA', 'OPERACIÓN INCORRECTA');
                return;
            }

            agregarProductoCotizacion();
            reordenarCarrito();
            calcularSubTotal();
            clearDetalleCotizacion();
            destruirDataTableDetalleCotizacion();
            pintarDetalleCotizacion(carrito);
            //===== RECALCULANDO DESCUENTOS Y MONTOS =====
            carrito.forEach((c) => {
                calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
            })
            calcularMontos();
            loadDataTableDetallesCotizacion();
            ocultarAnimacion();

        })
    }

    //============ LOAD SELECT2 ========
    function loadSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });

        $(".select2_modal_cliente").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%'
        });

        $('#cliente').select2({
            width: '100%',
            placeholder: "Buscar Cliente...",
            allowClear: true,
            language: {
                inputTooShort: function(args) {
                    var min = args.minimum;
                    return "Por favor, ingrese " + min + " o más caracteres";
                },
                searching: function() {
                    return "BUSCANDO...";
                },
                noResults: function() {
                    return "No se encontraron clientes";
                }
            },
            ajax: {
                url: '{{ route('utilidades.getClientes') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    if (data.success) {
                        params.page = params.page || 1;
                        const clientes = data.clientes;
                        return {
                            results: clientes.map(item => ({
                                id: item.id,
                                text: item.descripcion
                            })),
                            pagination: {
                                more: data.more
                            }
                        };
                    } else {
                        toastr.error(data.message, 'ERROR EN EL SERVIDOR');
                        return {
                            results: []
                        }
                    }

                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(data) {
                if (data.loading) {
                    return $(
                        '<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>'
                    );
                }
                return data.text;
            },
        });

        $('#producto').select2({
            width: '100%',
            placeholder: "Buscar producto...",
            allowClear: true,
            language: {
                inputTooShort: function(args) {
                    var min = args.minimum;
                    return "Por favor, ingrese " + min + " o más caracteres";
                },
                searching: function() {
                    return "BUSCANDO...";
                },
                noResults: function() {
                    return "No se encontraron productos";
                }
            },
            ajax: {
                url: '{{ route('pedidos.pedido.getProductos') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        almacen_id: $('#almacen').val(),
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    if (data.success) {
                        params.page = params.page || 1;
                        const productos = data.productos;
                        return {
                            results: productos.map(item => ({
                                id: item.producto_id,
                                text: item.producto_completo
                            })),
                            pagination: {
                                more: data.more
                            }
                        };
                    } else {
                        toastr.error(data.message, 'ERROR EN EL SERVIDOR');
                        return {
                            results: []
                        }
                    }

                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(data) {
                if (data.loading) {
                    return $(
                        '<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>'
                    );
                }
                return data.text;
            },
        });
    }

    function agregarProductoCotizacion() {
        const inputsCantidad = document.querySelectorAll('.inputCantidad');

        for (const ic of inputsCantidad) {

            const cantidad = ic.value ? ic.value : null;
            if (cantidad) {
                const producto = formarProducto(ic);
                const indiceExiste = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id ==
                    producto.color_id);

                //===== PRODUCTO NUEVO =====
                if (indiceExiste == -1) {
                    const objProduct = {
                        producto_id: producto.producto_id,
                        color_id: producto.color_id,
                        producto_nombre: producto.producto_nombre,
                        color_nombre: producto.color_nombre,
                        precio_venta: producto.precio_venta,
                        monto_descuento: 0,
                        porcentaje_descuento: 0,
                        precio_venta_nuevo: 0,
                        subtotal_nuevo: 0,
                        tallas: [{
                            talla_id: producto.talla_id,
                            talla_nombre: producto.talla_nombre,
                            cantidad: producto.cantidad
                        }]
                    };

                    carrito.push(objProduct);
                } else {
                    const productoModificar = carrito[indiceExiste];
                    productoModificar.precio_venta = producto.precio_venta;

                    const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

                    if (indexTalla !== -1) {
                        const cantidadAnterior = productoModificar.tallas[indexTalla].cantidad;
                        productoModificar.tallas[indexTalla].cantidad = producto.cantidad;
                        carrito[indiceExiste] = productoModificar;
                    } else {
                        const objTallaProduct = {
                            talla_id: producto.talla_id,
                            talla_nombre: producto.talla_nombre,
                            cantidad: producto.cantidad
                        };
                        carrito[indiceExiste].tallas.push(objTallaProduct);
                    }
                }
            } else {
                const producto = formarProducto(ic);
                const indiceProductoColor = carrito.findIndex(p => p.producto_id == producto.producto_id && p
                    .color_id == producto.color_id);

                if (indiceProductoColor !== -1) {
                    const indiceTalla = carrito[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto
                        .talla_id);

                    if (indiceTalla !== -1) {
                        const cantidadAnterior = carrito[indiceProductoColor].tallas[indiceTalla].cantidad;
                        carrito[indiceProductoColor].tallas.splice(indiceTalla, 1);

                        const cantidadTallas = carrito[indiceProductoColor].tallas.length;

                        if (cantidadTallas == 0) {
                            carrito.splice(indiceProductoColor, 1);
                        }
                    }
                }
            }
        }
    }

    //======= VALIDACIONES PARA EL FORMULARIO ============
    const validaciones = () => {

        let enviar = true;

        //======= validar fechas =============
        if ($('#fecha_documento').val() == '') {
            toastr.error('Ingrese Fecha de Documento.', 'Error');
            $("#fecha_documento").focus();
            enviar = false;

        }

        if ($('#fecha_atencion').val() == '') {
            toastr.error('Ingrese Fecha de Atención.', 'Error');
            $("#fecha_atencion").focus();
            enviar = false;
        }

        //============= validar carrito =============
        if (carrito.length == 0) {
            toastr.error('Ingrese al menos 1 Producto.', 'Error');
            enviar = false;
        }

        return enviar
    }

    //====== FORMATEAR EL CARRITO A FORMATO DE BD ======
    function formatearDetalle() {
        carritoFormateado.length = 0;
        carrito.forEach((d) => {
            d.tallas.forEach((t) => {
                const producto = {};
                producto.producto_id = d.producto_id;
                producto.color_id = d.color_id;
                producto.talla_id = t.talla_id;
                producto.cantidad = t.cantidad;
                producto.precio_venta = d.precio_venta;
                producto.porcentaje_descuento = d.porcentaje_descuento;
                producto.precio_venta_nuevo = d.precio_venta_nuevo;
                carritoFormateado.push(producto);
            })
        })
    }

    //====== guardar el carrito en el form ===========
    const saveCarritoJSON = () => {
        inputProductos.value = JSON.stringify(carritoFormateado);
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
                    precio_venta_nuevo: parseFloat(productoPrevio.precio_unitario_nuevo),
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

        console.log(carrito)

        //===== CALCULAR SUBTOTAL POR FILA DEL DETALLE ======
        calcularSubTotal();
        //===== CARGANDO EMBALAJE Y ENVÍO PREVIO ========
        cargarEmbalajeEnvioPrevios();

        //===== PINTANDO DETALLE ======
        pintarDetalleCotizacion(carrito);

        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        // carrito.forEach((c) => {
        //     calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
        // })

        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();

        //====== APLICAMOS DATATABLE A LA TABLA DETALLES COTIZACIÓN =======
        loadDataTableDetallesCotizacion();

        //========== SET EMBALAJE Y ENVIO ===========
        const cotizacion = @json($cotizacion);
        const embalajeInput = document.querySelector('.embalaje');
        const envioInput = document.querySelector('.envio');

        embalajeInput.value = cotizacion.monto_embalaje ? formatoMoneda(cotizacion.monto_embalaje) : 0;
        embalajeInput.dispatchEvent(new Event('input', {
            bubbles: true
        }));

        envioInput.value = cotizacion.monto_envio ? formatoMoneda(cotizacion.monto_envio) : 0;
        envioInput.dispatchEvent(new Event('input', {
            bubbles: true
        }));

    }

    //======= CALCULAR DESCUENTO ========
    const calcularDescuento = (producto_id, color_id, porcentaje_descuento) => {
        const indiceExiste = carrito.findIndex((c) => {
            return c.producto_id == producto_id && c.color_id == color_id;
        })

        if (indiceExiste !== -1) {
            const producto_color_editar = carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            producto_color_editar.porcentaje_descuento = parseFloat(porcentaje_descuento);
            producto_color_editar.monto_descuento = porcentaje_descuento === 0 ? 0 : producto_color_editar
                .subtotal * (porcentaje_descuento / 100);
            producto_color_editar.precio_venta_nuevo = porcentaje_descuento === 0 ? producto_color_editar
                .precio_venta : (producto_color_editar
                    .precio_venta * (1 - porcentaje_descuento / 100));
            producto_color_editar.subtotal_nuevo = porcentaje_descuento === 0 ? producto_color_editar.subtotal : (
                producto_color_editar
                .subtotal * (1 - porcentaje_descuento / 100));

            carrito[indiceExiste] = producto_color_editar;

            //==== ACTUALIZANDO PRECIO VENTA Y SUBTOTAL EN EL HTML ====
            const tdPrecioVenta = document.querySelector(
                `.td-precio-venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`
            );

            if (tdPrecioVenta) {
                tdPrecioVenta.innerHTML = `
                    <div style="width:100px; text-align:right;">
                        ${
                            parseFloat(producto_color_editar.precio_venta) !== parseFloat(producto_color_editar.precio_venta_nuevo)
                            ? `
                                <del style="color: gray;">
                                    ${parseFloat(producto_color_editar.precio_venta).toFixed(2)}
                                </del><br>
                                <strong class="precio_venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}">
                                    ${parseFloat(producto_color_editar.precio_venta_nuevo).toFixed(2)}
                                </strong>
                            `
                            : `
                                <strong class="precio_venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}">
                                    ${parseFloat(producto_color_editar.precio_venta_nuevo).toFixed(2)}
                                </strong>
                            `
                        }
                    </div>
                `;
            }

            //==== REPINTANDO LA COLUMNA SUBTOTAL ====
            const tdSubtotal = document.querySelector(
                `.td-subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`
            );

            if (tdSubtotal) {
                tdSubtotal.innerHTML = `
                    <div style="width:100px; text-align:right;">
                        ${
                            parseFloat(producto_color_editar.subtotal) !== parseFloat(producto_color_editar.subtotal_nuevo)
                            ? `
                                <del style="color: gray;">
                                    ${parseFloat(producto_color_editar.subtotal).toFixed(2)}
                                </del><br>
                                <strong class="subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}">
                                    ${parseFloat(producto_color_editar.subtotal_nuevo).toFixed(2)}
                                </strong>
                            `
                            : `
                                <strong class="subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}">
                                    ${parseFloat(producto_color_editar.subtotal_nuevo).toFixed(2)}
                                </strong>
                            `
                        }
                    </div>
                `;
            }

        }
    }

    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal = () => {
        let subtotal = 0;
        let subtotal_nuevo = 0;

        carrito.forEach((p) => {
            p.tallas.forEach((t) => {
                subtotal += parseFloat(p.precio_venta) * parseFloat(t.cantidad);
                subtotal_nuevo += parseFloat(p.precio_venta_nuevo) * parseFloat(t.cantidad);
            })

            p.subtotal = subtotal;
            p.subtotal_nuevo = subtotal_nuevo;
            subtotal = 0;
            subtotal_nuevo = 0;
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

        amountsCotizacion.totalPagar = total_pagar;
        amountsCotizacion.igv = igv;
        amountsCotizacion.total = total;
        amountsCotizacion.embalaje = embalaje;
        amountsCotizacion.envio = envio;
        amountsCotizacion.subtotal = subtotal;
        amountsCotizacion.monto_descuento = descuento;
    }

    const eliminarProducto = (productoId, colorId) => {
        carrito = carrito.filter((p) => {
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    const reordenarCarrito = () => {
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    const formarProducto = (ic) => {
        const producto_id = ic.getAttribute('data-producto-id');
        const producto_nombre = ic.getAttribute('data-producto-nombre');
        const color_id = ic.getAttribute('data-color-id');
        const color_nombre = ic.getAttribute('data-color-nombre');
        const talla_id = ic.getAttribute('data-talla-id');
        const talla_nombre = ic.getAttribute('data-talla-nombre');
        const precio_venta = parseFloat($('#precio_venta').find('option:selected').text());
        const cantidad = ic.value ? parseInt(ic.value) : 0;
        const subtotal = 0;
        const subtotal_nuevo = 0;
        const porcentaje_descuento = 0;
        const monto_descuento = 0;
        const precio_venta_nuevo = parseFloat($('#precio_venta').find('option:selected').text());

        const producto = {
            producto_id,
            producto_nombre,
            color_id,
            color_nombre,
            talla_id,
            talla_nombre,
            cantidad,
            precio_venta,
            subtotal,
            subtotal_nuevo,
            porcentaje_descuento,
            monto_descuento,
            precio_venta_nuevo
        };
        return producto;
    }


    function clearDetalleCotizacion() {
        while (tableDetalleBody.firstChild) {
            tableDetalleBody.removeChild(tableDetalleBody.firstChild);
        }
    }

    function pintarDetalleCotizacion(carrito) {
        let fila = ``;
        let htmlTallas = ``;
        const bodyDetalleTable = document.querySelector('#table-detalle tbody');
        const tallas = @json($tallas);

        carrito.forEach((c) => {
            htmlTallas = ``;
            fila += `<tr>
                        <td>
                            <i class="fas fa-trash-alt btn btn-danger delete-product"
                            data-producto="${c.producto_id}" data-color="${c.color_id}">
                            </i>
                        </td>
                        <th>
                            <div style="width:120px;">${c.producto_nombre}</div>
                        </th>
                        <th>${c.color_nombre}</th>`;

            // tallas
            tallas.forEach((t) => {
                let cantidad = c.tallas.filter((ct) => t.id == ct.talla_id);
                cantidad = cantidad.length ? cantidad[0].cantidad : '';
                htmlTallas += `<td>${cantidad}</td>`;
            });

            // precio venta con comparación
            htmlTallas += `<td style="text-align: right;" class="td-precio-venta_${c.producto_id}_${c.color_id}">
            <div style="width:100px;">`;
            if (parseFloat(c.precio_venta) !== parseFloat(c.precio_venta_nuevo)) {
                htmlTallas += `
                <del style="color: gray;">${parseFloat(c.precio_venta).toFixed(2)}</del><br>
                <strong class="precio_venta_${c.producto_id}_${c.color_id}">${parseFloat(c.precio_venta_nuevo).toFixed(2)}</strong>
            `;
            } else {
                htmlTallas += `
                    <strong class="precio_venta_${c.producto_id}_${c.color_id}">
                        ${parseFloat(c.precio_venta_nuevo).toFixed(2)}
                    </strong>
                `;
            }
            htmlTallas += `</div></td>`;

            // subtotal con comparación
            htmlTallas += `<td class="td-subtotal_${c.producto_id}_${c.color_id}" style="text-align: right;">`;
            if (parseFloat(c.subtotal) !== parseFloat(c.subtotal_nuevo)) {
                htmlTallas += `
                <del style="color: gray;">${parseFloat(c.subtotal).toFixed(2)}</del><br>
                <strong class="subtotal_${c.producto_id}_${c.color_id}">${parseFloat(c.subtotal_nuevo).toFixed(2)}</strong>
            `;
            } else {
                htmlTallas += `
                    <strong class="subtotal_${c.producto_id}_${c.color_id}">
                        ${parseFloat(c.subtotal_nuevo).toFixed(2)}
                    </strong>
                `;
            }
            htmlTallas += `</td>`;

            // porcentaje descuento
            htmlTallas += `
            <td style="text-align: center;">
                <input data-producto-id="${c.producto_id}" data-color-id="${c.color_id}"
                style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                class="form-control detailDescuento"></input>
            </td>
            `;

            fila += htmlTallas + `</tr>`;
            bodyDetalleTable.innerHTML = fila;
        });
    }

    function destruirDataTableDetalleCotizacion() {
        if (dataTableDetallesCotizacion) {
            dataTableDetallesCotizacion.destroy();
        }
    }

    //======== OBTENER PRODUCTOS POR MODELO ========
    async function getProductos() {
        toastr.clear();
        mostrarAnimacion();
        limpiarTableStocks();

        modelo_id = document.querySelector('#modelo').value;
        marca_id = document.querySelector('#marca').value;
        categoria_id = document.querySelector('#categoria').value;

        btnAgregarDetalle.disabled = true;

        if (modelo_id || marca_id || categoria_id) {
            try {
                const res = await axios.get(route('ventas.cotizacion.getProductos'), {
                    params: {
                        modelo_id: modelo_id,
                        marca_id: marca_id,
                        categoria_id: categoria_id
                    }
                });

                if (res.data.success) {
                    pintarSelectProductos(res.data.productos);
                    toastr.info('PRODUCTOS CARGADOS', 'OPERACIÓN COMPLETADA');
                } else {
                    ocultarAnimacion();
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                ocultarAnimacion();
                toastr.error(error, 'ERROR EN LA PETICIÓN DE OBTENER PRODUCTOS');
            }

        } else {
            ocultarAnimacion();
        }
    }

    //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
    async function getColoresTallas() {
        mostrarAnimacion();
        toastr.clear();

        const producto_id = $('#producto').val();
        const almacen_id = document.querySelector('#almacen').value;

        if (!almacen_id) {
            $('#almacen').select2('open');

            document.querySelector('#producto').onchange = null;
            $('#producto').val(null).trigger('change');
            document.querySelector('#producto').onchange = function() {
                getColoresTallas();
            };

            toastr.error('DEBE SELECCIONAR UN ALMACÉN!!!');
            ocultarAnimacion();
            return;
        }

        if (producto_id) {
            try {
                const res = await axios.get(route('ventas.cotizacion.getColoresTallas', {
                    almacen_id,
                    producto_id
                }));
                if (res.data.success) {
                    pintarTableStocks(res.data.producto_color_tallas);
                    pintarPreciosVenta(res.data.precios_venta);
                    loadCarrito();
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            } finally {
                ocultarAnimacion();
            }
        } else {
            limpiarTableStocks();
            limpiarSelectPreciosVenta();
            ocultarAnimacion();
        }
    }

    function limpiarSelectPreciosVenta() {
        $('#precio_venta').empty();
        $('#precio_venta').trigger('change');
    }

    //======= PINTAR PRECIOS VENTA =======
    function pintarPreciosVenta(precios_venta) {
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#precio_venta').empty();

        //====== LLENAR =======

        if (precios_venta) {
            if (precios_venta.precio_venta_1 != null) {
                const option_1 = new Option(precios_venta.precio_venta_1, 'precio_venta_1', false, false);
                $('#precio_venta').append(option_1);
            }

            if (precios_venta.precio_venta_2 != null) {
                const option_2 = new Option(precios_venta.precio_venta_2, 'precio_venta_2', false, false);
                $('#precio_venta').append(option_2);
            }

            if (precios_venta.precio_venta_3 != null) {
                const option_3 = new Option(precios_venta.precio_venta_3, 'precio_venta_3', false, false);
                $('#precio_venta').append(option_3);
            }
        }

        // Refrescar Select2
        $('#precio_venta').trigger('change');
    }

    //======== PINTAR SELECT PRODUCTOS =======
    function pintarSelectProductos(productos) {
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#producto').empty();

        if (productos.length === 0) {
            ocultarAnimacion();
        }

        //====== LLENAR =======
        productos.forEach((producto) => {
            const option = new Option(producto.nombre, producto.id, false, false);
            $('#producto').append(option);
        });

        // Refrescar Select2
        //$('#producto').val(null);
        $('#producto').trigger('change');
    }

    const pintarTableStocks = (producto) => {
        let filas = ``;

        if (dataTableStocksCotizacion) {
            dataTableStocksCotizacion.destroy();
        }

        if (producto) {
            producto.colores.forEach((color) => {
                filas += `  <tr>
                                <th scope="row" data-producto=${producto.id} data-color=${color.id} >
                                    <div style="width:200px;">${producto.nombre}</div>
                                </th>
                                <th scope="row">${color.nombre}</th>
                            `;

                color.tallas.forEach((talla) => {
                    filas += `<td style="background-color: rgb(210, 242, 242);">
                                            <p style="margin:0;width:20px;text-align:center;${talla.stock_logico != 0?'font-weight:bold':''};">${talla.stock_logico}</p>
                                </td>
                                <td width="8%">
                                    <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                    id="inputCantidad_${producto.id}_${color.id}_${talla.id}"
                                    data-producto-id="${producto.id}"
                                    data-producto-nombre="${producto.nombre}"
                                    data-color-nombre="${color.nombre}"
                                    data-talla-nombre="${talla.nombre}"
                                    data-color-id="${color.id}" data-talla-id="${talla.id}"></input>
                                </td>`;
                })

                filas += `</tr>`;

            })
        }

        tableStocksBody.innerHTML = filas;
        loadDataTableStocksCotizacion();
        btnAgregarDetalle.disabled = false;

    }


    function loadDataTableStocksCotizacion() {
        dataTableStocksCotizacion = new DataTable('#table-stocks', {
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

    function loadDataTableDetallesCotizacion() {
        dataTableDetallesCotizacion = new DataTable('#table-detalle', {
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

    //======= LLENAR INPUTS CON CANTIDADES EXISTENTES EN EL CARRITO =========
    function loadCarrito() {

        carrito.forEach((c) => {

            c.tallas.forEach((talla) => {
                let llave = `#inputCantidad_${c.producto_id}_${c.color_id}_${talla.talla_id}`;
                const inputLoad = document.querySelector(llave);

                if (inputLoad) {
                    inputLoad.value = talla.cantidad;
                }
            })

        })
    }

    //====== CARGAR EL PRECIO DE VENTA ELEGIDO PARA EL PRODUCTO EN EL CARRITO ======
    function loadPrecioVentaProductoCarrito(producto_id) {
        const producto_elegido_id = producto_id;
        //===== LO BUSCAMOS EN EL CARRITO ======
        const indiceProducto = carrito.findIndex((p) => {
            return p.producto_id == producto_elegido_id;
        })

        if (indiceProducto !== -1) {
            const itemProducto = carrito[indiceProducto];
            let targetValue;
            //==== UBICANDO PRECIO VENTA SELECCIONADO ======
            $('#precio_venta option').each(function() {
                if ($(this).text() == itemProducto.precio_venta) {
                    targetValue = $(this).val();
                    return false;
                }
            });

            if (targetValue) {
                $('#precio_venta').val(targetValue).trigger('change');
                console.log('precio venta fijado');
            }
        } else {
            toastr.info('NO SE PUDO FIJAR EL PRECIO DE VENTA PREVIO PARA EL PRODUCTO',
                'ESTO NO AFECTA A LA COTIZACIÓN');
        }

    }

    function cargarEmbalajeEnvioPrevios() {
        const precioEmbalaje = parseFloat(@json($cotizacion->monto_embalaje));
        const precioEnvio = parseFloat(@json($cotizacion->monto_envio));

        amountsCotizacion.embalaje = precioEmbalaje;
        amountsCotizacion.envio = precioEnvio;
    }

    //============= ABRIR MODAL CLIENTE =============
    function openModalCliente() {
        $("#modal_cliente").modal("show");
    }

    function mostrarAnimacion() {
        document.querySelector('.overlay_cotizacion_edit').style.visibility = 'visible';
    }

    function ocultarAnimacion() {
        document.querySelector('.overlay_cotizacion_edit').style.visibility = 'hidden';
    }

    function limpiarTableStocks() {
        if (dataTableStocksCotizacion) {
            dataTableStocksCotizacion.destroy();
            dataTableStocksCotizacion = null;
        }
        while (tableStocksBody.firstChild) {
            tableStocksBody.removeChild(tableStocksBody.firstChild);
        }
    }

    function actualizarCotizacion(formData) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Deseas actualizar la cotización?",
            text: "Se modificará la cotización!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Actualizando cotización...",
                    text: "Por favor, espere",
                    icon: "info",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const cotizacion_id = @json($cotizacion->id);

                    /*const delay     =   new Promise(resolve => setTimeout(resolve, 10000));
                    const request   =   axios.post(route("ventas.cotizacion.update", cotizacion_id), formData, {
                        headers: {
                            "X-HTTP-Method-Override": "PUT"
                        }
                    });

                    const [res]     =   await Promise.all([request, delay]);*/


                    const res = await axios.post(route("ventas.cotizacion.update", cotizacion_id),
                        formData, {
                            headers: {
                                "X-HTTP-Method-Override": "PUT"
                            }
                        });

                    if (res.data.success) {
                        window.location = route('ventas.cotizacion.index');
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }
                } catch (error) {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            Swal.close();
                            toastr.error("ERRORES DE VALIDACIÓN!!!");
                        } else {
                            Swal.close();
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        Swal.close();
                        toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.',
                            'ERROR DE CONEXIÓN');
                    } else {
                        Swal.close();
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }
                } finally {

                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "Operación cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                });
            }
        });
    }


    function cambiarAlmacen(almacen_id) {

        toastr.clear();

        mostrarAnimacion();
        //====== QUITAR EVENTOS ======
        document.querySelector('#categoria').onchange = null;
        document.querySelector('#marca').onchange = null;
        document.querySelector('#modelo').onchange = null;
        document.querySelector('#producto').onchange = null;


        //======== LIMPIAR SELECTS ======
        $('#categoria').val(null).trigger('change');
        $('#marca').val(null).trigger('change');
        $('#modelo').val(null).trigger('change');
        $('#producto').val(null).trigger('change');
        $('#precio_venta').val(null).trigger('change');

        //======= LIMPIAR TABLERO STOCKS ======
        destruirDataTable(dataTableStocksCotizacion);
        limpiarTabla('table-stocks');
        loadDataTableStocksCotizacion();

        //========= AGREGAR EVENTOS NUEVAMENTE =======
        document.querySelector('#categoria').onchange = function() {
            getProductos();
        };
        document.querySelector('#marca').onchange = function() {
            getProductos();
        };
        document.querySelector('#modelo').onchange = function() {
            getProductos();
        };
        document.querySelector('#producto').onchange = function() {
            getColoresTallas();
        };


        //========== LIMPIAR DETALLE DE LA COTIZACIÓN ========
        carrito.length = 0;
        destruirDataTableDetalleCotizacion();
        clearDetalleCotizacion();
        pintarDetalleCotizacion(carrito);
        loadDataTableDetallesCotizacion();

        carrito.forEach((c) => {
            calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
        })
        calcularMontos();

        ocultarAnimacion();
        toastr.info('SE HA LIMPIADO EL FORMULARIO');

    }

    async function getProductoBarCode(barcode) {
        try {
            toastr.clear();
            mostrarAnimacion();
            const res = await axios.get(route('ventas.cotizacion.getProductoBarCode', {
                barcode
            }));

            if (res.data.success) {

                addProductoBarCode(res.data.producto);

                toastr.info('AGREGADO AL DETALLE!!!', `${res.data.producto.nombre} - ${res.data.producto.color_nombre} - ${res.data.producto.talla_nombre}
                            PRECIO: ${res.data.producto.precio_venta_1}`, {
                    timeOut: 0
                });
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER PRODUCTO POR CÓDIGO DE BARRA');
        } finally {
            ocultarAnimacion();
        }
    }

    function addProductoBarCode(_producto) {

        const producto_id = _producto.id;
        const producto_nombre = _producto.nombre;
        const color_id = _producto.color_id;
        const color_nombre = _producto.color_nombre;
        const talla_id = _producto.talla_id;
        const talla_nombre = _producto.talla_nombre;
        const precio_venta = _producto.precio_venta_1;
        const cantidad = 1;
        const subtotal = 0;
        const subtotal_nuevo = 0;
        const porcentaje_descuento = 0;
        const monto_descuento = 0;
        const precio_venta_nuevo = 0;

        const producto = {
            producto_id,
            producto_nombre,
            color_id,
            color_nombre,
            talla_id,
            talla_nombre,
            cantidad,
            precio_venta,
            subtotal,
            subtotal_nuevo,
            porcentaje_descuento,
            monto_descuento,
            precio_venta_nuevo
        };

        const indiceExiste = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto
            .color_id);

        //===== PRODUCTO NUEVO =====
        if (indiceExiste == -1) {
            const objProduct = {
                producto_id: producto.producto_id,
                color_id: producto.color_id,
                producto_nombre: producto.producto_nombre,
                color_nombre: producto.color_nombre,
                precio_venta: producto.precio_venta,
                monto_descuento: 0,
                porcentaje_descuento: 0,
                precio_venta_nuevo: 0,
                subtotal_nuevo: 0,
                tallas: [{
                    talla_id: producto.talla_id,
                    talla_nombre: producto.talla_nombre,
                    cantidad: producto.cantidad
                }]
            };

            carrito.push(objProduct);
        } else {

            const productoModificar = carrito[indiceExiste];
            productoModificar.precio_venta = producto.precio_venta;

            const indexTalla = productoModificar.tallas.findIndex(t => t.talla_id == producto.talla_id);

            if (indexTalla !== -1) {
                const cantidadAnterior = productoModificar.tallas[indexTalla].cantidad;
                productoModificar.tallas[indexTalla].cantidad++;
                carrito[indiceExiste] = productoModificar;
            } else {
                const objTallaProduct = {
                    talla_id: producto.talla_id,
                    talla_nombre: producto.talla_nombre,
                    cantidad: producto.cantidad
                };
                carrito[indiceExiste].tallas.push(objTallaProduct);
            }
        }

        reordenarCarrito();
        calcularSubTotal();
        clearDetalleCotizacion();
        destruirDataTableDetalleCotizacion();
        pintarDetalleCotizacion(carrito);
        //===== RECALCULANDO DESCUENTOS Y MONTOS =====
        carrito.forEach((c) => {
            calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
        })
        calcularMontos();
        loadDataTableDetallesCotizacion();
    }

    function clearInputsCantidad() {
        const inputsCantidad = document.querySelectorAll('.inputCantidad');
        inputsCantidad.forEach((inputCantidad) => {
            inputCantidad.value = '';
        })
    }
</script>
@endpush
