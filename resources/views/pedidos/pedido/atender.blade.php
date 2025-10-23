@extends('layout')
@section('content')
    @include('ventas.documentos.editar.modals.mdl_envio')
@section('pedidos-active', 'active')
@section('pedido-active', 'active')
<style>
    .colorReadOnly {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        opacity: 0.9;
    }

    .overlay_pedido {
        position: fixed;
        /* Fija el overlay para que cubra todo el viewport */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        /* Color oscuro con opacidad */
        z-index: 9999;
        /* Asegura que el overlay esté sobre todo */
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 24px;
        visibility: hidden;
        /* Inicialmente oculto */
        opacity: 0;
        /* Transparente por defecto */
        transition: opacity 0.3s ease-in-out;
        /* Transición suave de visibilidad */
    }

    /*========== LOADER SPINNER =======*/
    .loader_pedido {
        position: relative;
        width: 75px;
        height: 100px;
        background-repeat: no-repeat;
        background-image: linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0),
            linear-gradient(#DDD 50px, transparent 0);
        background-size: 8px 100%;
        background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px;
        animation: pillerPushUp 4s linear infinite;
    }

    .loader_pedido:after {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 10px;
        height: 10px;
        background: #de3500;
        border-radius: 50%;
        animation: ballStepUp 4s linear infinite;
    }

    @keyframes pillerPushUp {

        0%,
        40%,
        100% {
            background-position: 0px 90px, 15px 78px, 30px 66px, 45px 58px, 60px 50px
        }

        50%,
        90% {
            background-position: 0px 50px, 15px 58px, 30px 66px, 45px 78px, 60px 90px
        }
    }

    @keyframes ballStepUp {
        0% {
            transform: translate(0, 0)
        }

        5% {
            transform: translate(8px, -14px)
        }

        10% {
            transform: translate(15px, -10px)
        }

        17% {
            transform: translate(23px, -24px)
        }

        20% {
            transform: translate(30px, -20px)
        }

        27% {
            transform: translate(38px, -34px)
        }

        30% {
            transform: translate(45px, -30px)
        }

        37% {
            transform: translate(53px, -44px)
        }

        40% {
            transform: translate(60px, -40px)
        }

        50% {
            transform: translate(60px, 0)
        }

        57% {
            transform: translate(53px, -14px)
        }

        60% {
            transform: translate(45px, -10px)
        }

        67% {
            transform: translate(37px, -24px)
        }

        70% {
            transform: translate(30px, -20px)
        }

        77% {
            transform: translate(22px, -34px)
        }

        80% {
            transform: translate(15px, -30px)
        }

        87% {
            transform: translate(7px, -44px)
        }

        90% {
            transform: translate(0, -40px)
        }

        100% {
            transform: translate(0, 0);
        }
    }
</style>

<div class="overlay_pedido">
    <span class="loader_pedido"></span>
</div>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Atender Pedido</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-12">
            @if (Session::has('pedido_facturado_atender'))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {!! Session::get('pedido_facturado_atender') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('components.overlay_esfera_1')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">

                            @include('pedidos.pedido.forms.form_pedido_atender')

                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-6 col-md-6">
                                            <h4><b>Detalle del Pedido</b></h4>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 mr-3"
                                            style="background-color: #ffffff;border-radius:5px;">
                                            <div class="row align-items-center" style="height: 100%;">
                                                <div class="col-6 d-flex justify-content-center p-0">
                                                    <div class="mr-2"
                                                        style="background-color: rgb(7, 7, 183);padding:5px;"></div>
                                                    <p class="m-0" style="color: black;">CANT SOLICITADA</p>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center p-0">
                                                    <div class="mr-2" style="background-color: black;padding:5px;">
                                                    </div>
                                                    <p class="m-0" style="color:black;">STOCK</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="col-12">
                                        @include('pedidos.pedido.table-detalles-atender', [
                                            'carrito' => 'carrito',
                                        ])
                                    </div>

                                </div>
                                <div class="panel-footer panel-primary">
                                    <div class="table-responsive">
                                        @include('pedidos.pedido.tables.table_montos_atender')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-6 col-md-6">
                                            <h4><b>Historial Atención</b></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            @include('pedidos.pedido.tables-historial.table-pedido-detalles')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" id="btn_regresar_atend" class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </button>

                                    <button type="submit" id="btn_grabar" form="form-pedido-doc-venta"
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
@push('styles')
<style>
    .search-length-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .buttons-container {
        display: flex;
        justify-content: end;
    }

    .custom-button {
        background-color: #ffffff !important;
        color: #000000 !important;
        border: 1px solid #dcdcdc !important;
        border-radius: 5px;
        padding: 8px 16px;
        font-size: 14px;
        margin: 8px 0px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color 3s, color 3s;
    }

    .custom-button:hover {
        background-color: #d7e9fb !important;
        color: #000000 !important;
        border-color: #d7e9fb !important;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    const tfootSubtotal = document.querySelector('.subtotal');
    const tfootEmbalaje = document.querySelector('.embalaje');
    const tfootEnvio = document.querySelector('.envio');
    const tfootTotal = document.querySelector('.total');
    const tfootIgv = document.querySelector('.igv');
    const tfootTotalPagar = document.querySelector('.total-pagar');
    const tfootDescuento = document.querySelector('.descuento');

    const inputSubTotal = document.querySelector('#monto_sub_total');
    const inputEmbalaje = document.querySelector('#monto_embalaje');
    const inputEnvio = document.querySelector('#monto_envio');
    const inputTotal = document.querySelector('#monto_total');
    const inputIgv = document.querySelector('#monto_total_igv');
    const inputTotalPagar = document.querySelector('#monto_total_pagar');
    const inputMontoDescuento = document.querySelector('#monto_descuento');

    let carrito = [];
    const data_send = [];
    let secureClosure = 1; //======= 1:DEVUELVE STOCKS_LOGICOS  2: NO DEVUELVE STOCKS_LOGICOS =======

    let dataTableHistorialPedido = null;
    let dataTableDetallePedido = null;

    document.addEventListener('DOMContentLoaded', async () => {
        mostrarAnimacionPedido();
        cargarProductosPrevios();
        pintarHistorialAtencionPedido();

        loadSelect2();

        events();
        iniciarSelectsMdlEnvio();
        eventsModalEnvio();
        setDefaultMdlEnvio();

        ocultarAnimacionPedido();
    })


    function events() {

        document.querySelector('#btn_regresar_atend').addEventListener('click', (e) => {
            event.target.disabled = true;

            event.target.innerHTML = '<i class="fa fa-arrow-left"></i> Regresando...';

            window.location.href = "{{ route('pedidos.pedido.index') }}";
        })

        //======== EDITAR INPUT CANTIDAD ATENDIDA =======
        document.addEventListener('input', (e) => {

            if (e.target.classList.contains('inputCantidadAtender')) {
                //====== QUITAR EL FOCUS DEL INPUT ======
                e.target.blur();
                //======== ELIMINAR TODOS LOS CARACTERES QUE NO SEAN NÚMEROS ========
                e.target.value = e.target.value.replace(/\D/g, '');
                //======= OBTENER PRODUCTO ID - COLOR ID - TALLA ID ==============
                const producto_id = e.target.getAttribute('data-producto-id');
                const color_id = e.target.getAttribute('data-color-id');
                const talla_id = e.target.getAttribute('data-talla-id');

                //======= VALIDAR CANTIDAD ======
                const cantidad_atender_nueva = e.target.value == '' ? 0 : parseInt(e.target.value);
                validarCantidadAtendida(producto_id, color_id, talla_id, cantidad_atender_nueva, e.target);
            }

            if (e.target.classList.contains('embalaje') || e.target.classList.contains('envio')) {
                // Eliminar ceros a la izquierda, excepto si es el único carácter en el campo o si es seguido por un punto decimal y al menos un dígito
                e.target.value = e.target.value.replace(
                    /^0+(?=\d)|(?<=\D)0+(?=\d)|(?<=\d)0+(?=\.)|^0+(?=[1-9])/g, '');

                // Evitar que el primer carácter sea un punto
                e.target.value = e.target.value.replace(/^(\.)/, '');

                // Reemplazar todo excepto los dígitos y el punto decimal
                e.target.value = e.target.value.replace(/[^\d.]/g, '');

                // Reemplazar múltiples puntos decimales con uno solo
                e.target.value = e.target.value.replace(/(\..*)\./g, '$1');

                calcularMontos();
            }
        })

        //========= GENERAR DOC DE VENTA ========
        document.querySelector('#form-pedido-doc-venta').addEventListener('submit', async (e) => {
            e.preventDefault();

            const message = comprobarFacturacion();

            Swal.fire({
                title: message,
                text: "Operación no reversible!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí!"
            }).then(async (result) => {
                if (result.isConfirmed) {

                    //===== VALIDACIONES ======
                    const validar = await validarForm();

                    if (!validar) {
                        return;
                    }

                    //====== CARGAR PRODUCTOS ========
                    cargarData();

                    //======== ENVIAR FORM =======
                    generarDocumentoVenta(e.target);

                }
            });

        })

        //============ CIERRE DE LA VENTANA ======
        window.addEventListener('beforeunload', async function(event) {

            if (secureClosure == 1) {
                // var mensaje = '¿Estás seguro de que quieres salir de esta página?';
                // event.returnValue = mensaje;

                try {
                    const response = await axios.post(route('pedidos.pedido.devolverStockLogico'), {
                        carrito: JSON.stringify(carrito)
                    });
                    console.log('Stock devuelto correctamente:', response.data);
                } catch (error) {
                    console.error('Error al devolver el stock:', error);
                }
            }

        });


        //=========== MODAL DESPACHO =========
        document.querySelector('.btn-envio').addEventListener('click', () => {
            //======= COLCANDO EN MODAL ENVIO EL NOMBRE DEL CLIENTE =======
            const cliente_nombre = $("#cliente").find('option:selected').text();
            //console.log(cliente_nombre);
            const nroDocumento = cliente_nombre.split(':')[1].split('-')[0].trim();
            const cliente_nombre_recortado = cliente_nombre.split('-')[1].trim()
            const tipo_documento = cliente_nombre.split(':')[0];

            // console.log(cliente_nombre);
            // console.log(cliente_nombre_recortado);
            // console.log(nroDocumento);

            if (tipo_documento === "DNI" || tipo_documento === "CARNET EXT.") {
                //====== COLOCAR TEXTO DEL SPAN =====
                document.querySelector('.span-tipo-doc-dest').textContent = tipo_documento;
                //====== SELECCIONAR LA OPCIÓN RESPECTIVA EN SELECT TIPO DOC DEST ======
                if (tipo_documento === "DNI") {
                    $('#tipo_doc_destinatario').val(0).trigger('change');
                    if (nroDocumento.trim() != "99999999") {
                        document.querySelector('#nro_doc_destinatario').value = nroDocumento;
                        document.querySelector('#nro_doc_destinatario').value = nroDocumento;
                        document.querySelector('#nombres_destinatario').value = cliente_nombre_recortado;
                    }
                }
                if (tipo_documento === "CARNET EXT.") {
                    $('#tipo_doc_destinatario').val(1).trigger('change');
                    document.querySelector('#nro_doc_destinatario').value = nroDocumento;
                    document.querySelector('#nombres_destinatario').value = cliente_nombre_recortado;
                }
            }

            //========= ABRIR MODAL ENVÍO =======
            $("#modal_envio").modal("show");
        })
    }

    //====== SELECT2 =======
    function loadSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //======== VERIFICAR SI EL PEDIDO FUE FACTURADO Y MOSTRAR MENSAJE PERSONALIZADO =======
    function comprobarFacturacion() {
        const pedido = @json($pedido);
        const montoTotal = parseFloat(inputTotalPagar.value);

        let message = "";
        if (pedido.facturado === "SI") {
            const saldo_facturado = parseFloat(pedido.saldo_facturado);
            //======== EL SALDO CUBRE LA ATENCIÓN AÚN ========
            if (saldo_facturado >= montoTotal) {
                message = `El saldo de S/.${saldo_facturado} del pedido facturado   cubre la atención de S/.${montoTotal}.
                Se generará el comprobante como pagado con anticipo.¿DESEA CONTINUAR?`;
            } else {
                message =
                    `El saldo de S/.${saldo_facturado} del pedido facturado  NO CUBRE la atención de S/.${montoTotal}.
                Se generará el comprobante como pagado con anticipo parcial y un recibo de caja con el excedente.¿DESEA CONTINUAR?`;
            }
        }

        if (!pedido.facturado) {
            message = "¿DESEA GENERAR EL DOCUMENTO DE VENTA?";
        }

        return message;
    }

    //======= LIMPIAR TABLA ======
    function clearHistorialAtencionPedido() {
        const tbody = document.querySelector('#table-pedido-detalles tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

    //======== MOSTRAR HISTORIAL ATENCIÓN PEDIDO =======
    function pintarHistorialAtencionPedido() {
        const pedido_detalles = @json($pedido_detalle);
        const tbody = document.querySelector('#table-pedido-detalles tbody');

        clearHistorialAtencionPedido();

        if (dataTableHistorialPedido) {
            dataTableHistorialPedido.destroy();
        }

        let filas = ``;
        pedido_detalles.forEach((pd) => {
            filas += `<tr>
                            <th scope="row">${pd.producto_nombre}</th>
                            <td>${pd.color_nombre}</td>
                            <td>${pd.talla_nombre}</td>
                            <td>${pd.cantidad}</td>
                            <td>${pd.cantidad_atendida}</td>
                            <td>${pd.cantidad_pendiente}</td>
                            <td>${pd.cantidad_enviada}</td>
                            <td>${pd.cantidad_devuelta}</td>
                            <td>${pd.cantidad_fabricacion}</td>
                        </tr>`;
        })

        tbody.innerHTML = filas;

        loadDataTableHistorialPedido();
    }


    function cargarData() {
        //===== CARGANDO PRODUCTOS =====
        const inputProductos = document.querySelector('#productos_tabla');
        inputProductos.value = JSON.stringify(data_send);

        //======= CARGANDO CLIENTE ======
        const cliente_id = document.querySelector('#cliente').value;
        document.querySelector('#cliente_id').value = cliente_id;

    }

    async function generarDocumentoVenta(form) {
        try {

            //======== OVERLAY DE CARGA =======
            Swal.fire({
                title: 'Generando documento de venta...',
                text: 'Por favor, espera',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });


            //======== VERIFICAR SI EXISTEN CAJAS ABIERTAS ==========
            const {
                data
            } = await this.axios.get(route('Caja.movimiento.verificarestado'));
            //======= MANEJO DE LA RESPUESTA ===========
            const {
                success
            } = data;

            if (success) {
                const pedido = @json($pedido);
                const formData = new FormData(form);
                formData.append('pedido_id', pedido.id);

                const res = await axios.post(route('pedidos.pedido.generarDocumentoVenta'), formData)
                const success = res.data.success;

                if (success) {
                    secureClosure = 2;
                    const documento_id = res.data.documento_id;

                    toastr.success('¡Documento de venta creado!', 'Exito');

                    let url_open_pdf = '{{ route('ventas.documento.comprobante', [':id1', ':size']) }}'
                        .replace(':id1', documento_id)
                        .replace(':size', 80);

                    window.open(url_open_pdf, 'Comprobante SISCOM',
                        'location=1, status=1, scrollbars=1,width=900, height=600');
                    window.location.href = '{{ route('ventas.documento.index') }}';
                    //===> asegurar cierre ===

                } else {
                    secureClosure = 1;
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    Swal.close();
                }

            } else {
                toastr.error('NO HAY CAJAS APERTURADAS', 'ERROR');
                secureClosure = 1;
                Swal.close();
            }

        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN GENERAR DOC VENTA');
            Swal.close();
        } finally {
            document.querySelector('#btn_grabar').disabled = false;
        }
    }

    //=========== VALIDAR FORM ========
    async function validarForm() {
        //====== VALIDAR FECHAS ======
        const fechaAtencion = document.querySelector('#fecha_atencion');
        const fechaVencimiento = document.querySelector('#fecha_vencimiento');

        if (fechaAtencion.value == '') {
            toastr.error('ESTABLEZCA LA FECHA DE ATENCIÓN', 'ERROR');
            fechaAtencion.focus();
            return false;
        }
        if (fechaVencimiento.value == '') {
            toastr.error('ESTABLEZCA LA FECHA DE VENCIMIENTO', 'ERROR');
            fechaVencimiento.focus();
            return false;
        }

        //======= VALIDAR TIPO VENTA ======
        const tipo_venta = document.querySelector('#tipo_venta');
        if (tipo_venta.value == '') {
            tipo_venta.focus();
            toastr.error('SELECCIONE UN TIPO DE VENTA', 'ERROR');
            return false;
        }

        //===== VALIDAR CONDICION ======
        const condicion = document.querySelector('#condicion_id');
        if (condicion.value == '') {
            condicion.focus();
            toastr.error('SELECCIONE UNA CONDICIÓN PARA LA VENTA', 'ERROR');
            return false;
        }

        //========= VALIDAR CONTENIDO DEL CARRITO =======
        if (carrito.length == 0) {
            toastr.error('EL DETALLE DEL PEDIDO ESTÁ VACÍO', 'ERROR');
            return false;
        }

        //====== VALIDAR CLIENTE =====
        const cliente = document.querySelector('#cliente');
        if (cliente.value == '') {
            toastr.error('DEBE SELECCIONAR UN CLIENTE', 'ERROR');
            cliente.focus();
        }

        //====== FORMATEAR CARRITO ======
        data_send.length = 0;
        formatearDetalle(carrito);
        console.log(data_send);

        if (data_send.length == 0) {
            toastr.error('LAS CANTIDADES DEL DETALLE SON 0', 'ERROR');
            return false;
        }

        //====== VALIDAR TIPO COMPROBANTE ACTIVO ======
        const validar = validarTipoComprobante(tipo_venta.value);
        return validar;

    }

    //========= VALIDAR CANTIDAD ATENDIDA ========
    async function validarCantidadAtendida(producto_id, color_id, talla_id, cantidad_atender_nueva, input) {

        //======== OBTENER EL PRODUCTO DEL CARRITO =======
        let producto = carrito.filter((c) => {
            return c.producto_id == producto_id && c.color_id == color_id;
        })[0].tallas.filter((t) => {
            return t.talla_id == talla_id;
        })

        if (producto.length > 0) {
            //======= VALIDAR QUE LA CANTIDAD ATENDIDA SEA MENOR IGUAL A LA SOLICITADA =====
            const cantidad_pendiente = parseInt(producto[0].cantidad_pendiente);
            const cantidad_atender_anterior = parseInt(producto[0].cantidad_atender);

            if (cantidad_atender_nueva > cantidad_pendiente) {
                toastr.error('LA CANTIDAD ATENDER DEBE SER MENOR O IGUAL A LA PENDIENTE', 'ERROR');
                input.value = parseInt(cantidad_atender_anterior);
                input.focus();
                return false;
            }

            //======= VALIDAR QUE LA CANTIDAD ATENDER SEA MENOR IGUAL AL STOCK_LOGICO EN VIVO DEL PRODUCTO =======
            try {
                const overlay = document.getElementById('overlay_esfera_1');
                overlay.style.display = 'flex';
                const res = await axios.post(route('pedidos.pedido.validarCantidadAtender'), {
                    cantidad_atender_anterior,
                    cantidad_atender_nueva,
                    almacen_id: @json($pedido->almacen_id),
                    producto_id,
                    color_id,
                    talla_id
                })

                console.log(res);
                const type = res.data.type;
                const message = res.data.message;
                const data = res.data.data;

                if (type == 'success') {
                    //======== ACTUALIZANDO EL CARRITO ========
                    producto[0].cantidad_atender = cantidad_atender_nueva;
                    calcularSubTotal();
                    calcularDescuento(producto_id, color_id);
                    toastr.success(message, 'CANTIDAD ACTUALIZADA');
                }

                if (type == 'error') {
                    input.value = cantidad_atender_anterior;
                    input.focus();
                    toastr.error(message, 'ERROR');
                }
            } catch (error) {

            } finally {
                const overlay = document.getElementById('overlay_esfera_1');
                overlay.style.display = 'none';
                input.focus();
            }
        }
    }


    //========= PINTAR DETALLE PEDIDO =======
    function pintarDetallePedido(carrito) {

        let fila = ``;
        let htmlTallas = ``;
        const bodyDetalleTable = document.querySelector('#table-detalle-atender tbody');
        const tallas = @json($tallas);

        carrito.forEach((c) => {
            htmlTallas = ``;
            fila += `<tr>
                            <th>
                                <div style="min-width:150px;">${c.producto_nombre} - ${c.color_nombre}</div>
                            </th>`;

            //tallas
            tallas.forEach((t) => {
                let talla_data = c.tallas.filter((ct) => {
                    return t.id == ct.talla_id;
                });

                if (talla_data.length === 0) {
                    htmlTallas += `<td></td>
                                        <td></td>`;
                } else {
                    htmlTallas += `<td>
                                            <div class="d-flex flex-column align-items-center">
                                                <p  style="margin:0px;color:rgb(7, 7, 183);font-weight:bold;">${talla_data[0].cantidad_pendiente}</p>
                                                <p  style="margin:0px;color:black;font-weight:bold;">${talla_data[0].stock_logico}</p>
                                            </div>
                                        </td>`;

                    htmlTallas += ` <td>
                                            <input ${talla_data[0].cantidad_atender == 0?'readonly':''}
                                            class="form-control inputCantidadAtender" data-producto-id="${c.producto_id}"
                                            data-color-id="${c.color_id}" data-talla-id="${t.id}"
                                            value="${talla_data[0].cantidad_atender}"></input>
                                        </td>`;
                }
            })


            htmlTallas += `   <td style="text-align: right;">
                                    <span class="precio_venta_${c.producto_id}_${c.color_id}">
                                        ${c.porcentaje_descuento === 0? c.precio_unitario:c.precio_unitario_nuevo}
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

            fila += htmlTallas;
        })

        bodyDetalleTable.innerHTML = fila;

    }


    //=========== CARGAR PRODUCTOS PREVIOS =======
    const cargarProductosPrevios = () => {

        limpiarTableDetallePedido();
        destruirDataTableDetallePedido();

        const productosPrevios = @json($atencion_detalle);
        //====== CARGANDO CARRITO ======
        const producto_color_procesados = [];

        productosPrevios.forEach((productoPrevio) => {
            const id = `${productoPrevio.producto_id}-${productoPrevio.color_id}`;

            if (!producto_color_procesados.includes(id)) {
                const producto = {
                    almacen_id: productoPrevio.almacen_id,
                    producto_id: productoPrevio.producto_id,
                    producto_nombre: productoPrevio.producto_nombre,
                    producto_codigo: productoPrevio.producto_codigo,
                    modelo_nombre: productoPrevio.modelo_nombre,
                    color_id: productoPrevio.color_id,
                    color_nombre: productoPrevio.color_nombre,
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
                            talla_nombre: t.talla_nombre,
                            cantidad_solicitada: parseInt(t.cantidad_solicitada),
                            cantidad_atendida: parseInt(t.cantidad_atendida),
                            cantidad_pendiente: parseInt(t.cantidad_pendiente),
                            stock_logico: parseInt(t.stock_logico),
                            stock_logico_actualizado: parseInt(t.stock_logico_actualizado),
                            cantidad_atender: parseInt(t.cantidad),
                            existe: t.existe
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
        pintarDetallePedido(carrito);
        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c) => {
            calcularDescuento(c.producto_id, c.color_id, c.porcentaje_descuento);
        })

        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();

        loadDataTableDetallePedido();
    }

    function limpiarTableDetallePedido() {
        const table = document.querySelector('#table-detalle-atender tbody');

        while (table.firstChild) {
            table.removeChild(table.firstChild);
        }
    }

    function destruirDataTableDetallePedido() {
        if (dataTableDetallePedido) {
            dataTableDetallePedido.destroy();
        }
    }

    //======== CALCULAR SUBTOTAL POR PRODUCTO COLOR EN EL DETALLE ======
    const calcularSubTotal = () => {
        let subtotal = 0;

        carrito.forEach((p) => {
            p.tallas.forEach((t) => {
                subtotal += parseFloat(p.precio_venta) * parseFloat(t.cantidad_atender);
            })

            p.subtotal = subtotal;
            subtotal = 0;
        })
    }

    //======= CARGAR EMBALAJE ENVIO PREVIOS =======
    function cargarEmbalajeEnvioPrevios() {
        const precioEmbalaje = inputEmbalaje.value;
        const precioEnvio = inputEnvio.value;

        tfootEmbalaje.value = precioEmbalaje;
        tfootEnvio.value = precioEnvio;
    }

    //======== CALCULAR DESCUENTO ========
    const calcularDescuento = (producto_id, color_id) => {
        const indiceExiste = carrito.findIndex((c) => {
            return c.producto_id == producto_id && c.color_id == color_id;
        })

        if (indiceExiste !== -1) {
            const producto_color_editar = carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            const porcentaje_descuento = producto_color_editar.porcentaje_descuento;
            producto_color_editar.monto_descuento = porcentaje_descuento === 0 ? 0 : producto_color_editar
                .subtotal * (porcentaje_descuento / 100);
            producto_color_editar.precio_venta_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar
                .precio_venta * (1 - porcentaje_descuento / 100)).toFixed(2);
            producto_color_editar.subtotal_nuevo = porcentaje_descuento === 0 ? 0 : (producto_color_editar
                .subtotal * (1 - porcentaje_descuento / 100)).toFixed(2);

            carrito[indiceExiste] = producto_color_editar;

            //==== RECALCULANDO MONTOS ====
            calcularMontos();

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

        inputTotalPagar.value = total_pagar.toFixed(2);
        inputIgv.value = igv.toFixed(2);
        inputTotal.value = total.toFixed(2);
        inputEmbalaje.value = embalaje.toFixed(2);
        inputEnvio.value = envio.toFixed(2);
        inputSubTotal.value = subtotal.toFixed(2);
        inputMontoDescuento.value = descuento.toFixed(2);
    }


    //======== LIMPIAR TABLA PRODUCTOS ========
    function clearTabla(bodyTable) {
        while (bodyTable.firstChild) {
            bodyTable.removeChild(bodyTable.firstChild);
        }
    }

    //==== CAMBIAR CONDICION ====
    function cambiarCondicion(condicion_id) {
        //===== CONDICION CREDITO ======
        if (condicion_id == "2") {
            const select_fecha_venc = document.querySelector('#fecha_vencimiento');
            const select_fecha_aten = document.querySelector('#fecha_atencion');

            const fechaAtencion = new Date(select_fecha_aten.value);
            fechaAtencion.setDate(fechaAtencion.getDate() + 10);
            const fechaVencimientoMinima = fechaAtencion.toISOString().split('T')[0];

            select_fecha_venc.readOnly = false;
            select_fecha_venc.min = fechaVencimientoMinima;
            select_fecha_venc.value = fechaVencimientoMinima;
        }
        if (condicion_id == "1") {
            const select_fecha_venc = document.querySelector('#fecha_vencimiento');
            const select_fecha_aten = document.querySelector('#fecha_atencion');

            const fechaActual = new Date().toISOString().split('T')[0];
            select_fecha_venc.value = fechaActual;
            select_fecha_venc.readOnly = true;
        }
    }

    //========= VALIDAR TIPO COMPROBANTE ==========
    async function validarTipoComprobante(comprobante_id) {
        toastr.clear();
        if (comprobante_id != '') {
            //===== VALIDAR TIPO VENTA ACTIVO ======
            try {
                const url = route('pedidos.pedido.validarTipoVenta', {
                    'comprobante_id': comprobante_id
                });
                const res = await axios.get(url);
                console.log(res);
                const type = res.data.type;
                const message = res.data.message;
                if (type == 'success') {
                    const estado = res.data.estado;
                    const message = res.data.message;
                    if (estado) {
                        toastr.success(message, 'VALIDACIÓN COMPLETADA');
                        return true;
                    } else {
                        document.querySelector('#tipo_venta').focus();
                        toastr.error(message, 'VALIDACIÓN COMPLETADA');
                        return false;
                    }
                }

                if (type == 'error') {
                    const message = res.data.message;
                    const exception = res.data.exception;
                    document.querySelector('#tipo_venta').focus();
                    toastr.error(exception, message);
                    return false;
                }

            } catch (error) {
                return false;
            }
        } else {
            return false;
        }
    }

    function formatearDetalle(detalles) {
        /*if(detalles.length > 0){
            detalles.forEach((d)=>{
                d.tallas.forEach((t)=>{
                    if(t.cantidad_atender != 0){
                        const producto ={};
                        producto.producto_id            =   d.producto_id;
                        producto.color_id               =   d.color_id;
                        producto.talla_id               =   t.talla_id;
                        producto.cantidad               =   t.cantidad_atender;
                        producto.precio_unitario        =   d.precio_venta;
                        producto.porcentaje_descuento   =   d.porcentaje_descuento;
                        producto.precio_unitario_nuevo  =   d.precio_venta_nuevo;
                        data_send.push(producto);
                    }
                })
            })
        }*/
        if (detalles.length > 0) {

            let producto_valido = {};

            carrito.forEach((producto) => {

                producto_valido = producto;

                const tallas_validas = producto.tallas.filter((t) => {
                    return t.cantidad_atender != 0;
                })

                tallas_validas.forEach((tv) => {
                    tv.cantidad = tv.cantidad_atender;
                })

                producto_valido.tallas = tallas_validas;
                if (producto_valido.tallas.length > 0) {
                    data_send.push(producto_valido);
                }

            })
        }

    }

    function mostrarAnimacionPedido() {
        document.querySelector('.overlay_pedido').style.visibility = 'visible';
    }

    function ocultarAnimacionPedido() {
        document.querySelector('.overlay_pedido').style.visibility = 'hidden';
    }

    //======= INICIAR DATATABLE ======
    function loadDataTableHistorialPedido() {
        dataTableHistorialPedido = new DataTable('#table-pedido-detalles', {
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

    function loadDataTableDetallePedido() {
        dataTableDetallePedido = new DataTable('#table-detalle-atender', {
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

    function cambiarModo(modo) {
        const select_id = 'tipo_venta';

        $(`#${select_id}`).off('select2:opening');
        $(`#${select_id}`).next('.select2-container')
            .css('pointer-events', 'auto')
            .find('.select2-selection')
            .removeClass('colorReadOnly');

        if (modo === 'RESERVA') {
            $(`#${select_id}`).val(129).trigger('change');

            $(`#${select_id}`).next('.select2-container')
                .css('pointer-events', 'none')
                .find('.select2-selection')
                .addClass('colorReadOnly');
        }
    }

    async function setDefaultMdlEnvio() {
        const cliente = @json($cliente);
        desactivarEventosSelectsMdlEnvio();
        window.departamentoSelect.setValue(parseInt(cliente.departamento_id));
        const provincias = await getProvincias(cliente.departamento_id);
        pintarProvincias(provincias, cliente.provincia_id);
        const distritos = await getDistritos(cliente.provincia_id);
        pintarDistritos(distritos, parseInt(cliente.distrito_id));
        setZona(getZona(parseInt(cliente.departamento_id)));
        window.tipoEnvioSelect.setValue(187, false);
        await getEmpresasEnvio();
        activarEventosSelectsMdlEnvio();
    }
</script>
@endpush
