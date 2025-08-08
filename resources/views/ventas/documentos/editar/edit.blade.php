@extends('layout')
@section('content')

@include('ventas.cotizaciones.modal-cliente')
@include('ventas.documentos.modal-envio')


@section('ventas-active', 'active')
@section('documento-active', 'active')

<style>
    .inputCantidadValido{
        border-color:rgb(59, 63, 255) !important;
    }
    .inputCantidadIncorrecto{
        border-color: red !important;
    }
    .inputCantidadColor{
        border-color: rgb(48, 48, 88);
    }
    .colorStockLogico{
        background-color: rgb(243, 248, 255);
    }
</style>


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>EDITAR DOCUMENTO DE VENTA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('consultas.ventas.documento.no.index') }}">Documentos de venta no enviados</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Editar</strong>
            </li>
        </ol>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <input type="hidden" id='asegurarCierre'>

                    @include('ventas.documentos.editar.forms.form_edit')

                    <hr>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Seleccione productos</b></h4>
                                </div>
                                <div class="panel-body ibox-content">

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                                        <label class="required" style="font-weight: bold;">CATEGORÍA - MARCA - MODELO - PRODUCTO</label>
                                        <select
                                            id="producto"
                                            class=""
                                            onchange="getColoresTallas()" >
                                            <option value=""></option>
                                        </select>
                                    </div>

                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
                                        <label class="required" style="font-weight: bold;">PRECIO VENTA</label>
                                        <select id="precio_venta" class="select2_form form-control">
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <div class="table-responsive">
                                            @include('ventas.documentos.editar.tables.tbl_stock')
                                        </div>
                                    </div>


                                    <div class="col-lg-2 col-xs-12">
                                        <div class="form-group">
                                            <label class="col-form-label" for="amount">&nbsp;</label>
                                            <button type=button class="btn btn-block btn-warning" style='color:white;'
                                                id="btn_agregar_detalle"> <i class="fa fa-plus"></i>
                                                AGREGAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary" id="panel_detalle">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle del Documento de Venta</b></h4>
                                </div>
                                <div class="panel-body ibox-content">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            @include('ventas.documentos.editar.tables.tbl_detalle')
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            @include('ventas.documentos.editar.tables.tbl_montos')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="hr-line-dashed"></div>
                    <div class="form-group row">

                        <div class="col-md-6 text-left" style="color:#fcbc6c">
                            <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                (<label class="required"></label>) son obligatorios.</small>
                        </div>

                        <div class="col-md-6 text-right">


                            <a onclick="regresarClick(event)" href="javascript:void(0)" id="btn_cancelar" class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" form="formActualizarVenta"  class="btn btn-w-m btn-primary">
                                <i class="fa fa-save"></i> Grabar
                            </button>
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
    .my-swal {
        z-index: 3000 !important;
    }

</style>

@endpush

@push('scripts')

<script>

    const tableDetalleBody      =   document.querySelector('#table-detalle tbody');
    const tableStocksBody       =   document.querySelector('#table-stocks tbody');
    const detalles              =   @json($detalles);
    const tallasBD              =   @json($tallas);
    const documento             =   @json($documento);
    const btnAgregarDetalle     =   document.querySelector('#btn_agregar_detalle');

    const tfootSubtotal         =   document.querySelector('.subtotal');
    const tfootEmbalaje         =   document.querySelector('.embalaje');
    const tfootEnvio            =   document.querySelector('.envio');
    const tfootTotal            =   document.querySelector('.total');
    const tfootIgv              =   document.querySelector('.igv');
    const tfootTotalPagar       =   document.querySelector('.total-pagar');
    const tfootDescuento        =   document.querySelector('.descuento');


    const amounts       =   {
                                    subtotal:0,
                                    embalaje:0,
                                    envio:0,
                                    total:0,
                                    igv:0,
                                    totalPagar:0,
                                    monto_descuento:0
                                }

    let dtDetalleVenta      =   null;
    let dtStocksVenta       =   null;

    let carrito = [];
    let modelo_id;
    let asegurarCierre=5;

    document.addEventListener('DOMContentLoaded',async ()=>{

        dtStocksVenta       =    iniciarDataTable('table-stocks');
        dtDetalleVenta      =    iniciarDataTable('table-detalle');

        events();

        loadSelect2();
        cargarProductosPrevios();     //======== FORMATEAR DETALLE ==============
        asegurarCierre=1;


    })

    function events(){

        eventsCliente();
        eventsModalEnvio();

        btnAgregarDetalle.addEventListener('click',async ()=>{

            //========= VALIDAR SELECCIÓN DE PRECIO DE VENTA ======
            if(!$('#precio_venta').val()){
                toastr.error('DEBE SELECCIONAR UN PRECIO DE VENTA','OPERACIÓN INCORRECTA');
                return;
            }

            //======== ANIMACIÓN ======
            mostrarAnimacion();
            //======= LIMPIAR ALERTAS PREVIAS ======
            toastr.clear();

            //const inputsCantidad        = document.querySelectorAll('.inputCantidad');

            //========= AGREGAR PRODUCTO ========
            agregarProducto();

            //======== REORDENAR CARRITO =======
            reordenarCarrito();

            //========= CALCULAR SUBTOTAL =====
            calcularSubTotal();

            //========= DESTRUIR DATATABLE DETALLE VENTA ======
            destruirDataTable(dtDetalleVenta);

            //======== LIMPIAR TABLA DETALLE VENTA ======
            limpiarTabla('table-detalle');

            //========= PINTAR TABLA DETALLE VENTA =======
            pintarDetalle();

            //===== RECALCULANDO DESCUENTOS, ESTO EDITA LA TABLA DETALLE VENTA TMB =====
            carrito.forEach((c)=>{
                calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
            })

            //======= INICIAR DATATABLE DETALLE VENTA =======
            dtDetalleVenta  =   iniciarDataTable('table-detalle');

            //======= CALCULAR MONTOS ======
            calcularMontos();

            toastr.info('PRODUCTO AGREGADO');
            ocultarAnimacion();

        })


        //===== VALIDAR CONTENIDO DE INPUTS CANTIDAD ========
        //===== VALIDAR TFOOTS EMBALAJE Y ENVIO ======
        document.addEventListener('input',(e)=>{

            if(e.target.classList.contains('inputCantidad')){
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }

            if (e.target.classList.contains('embalaje') || e.target.classList.contains('envio')) {
                // Eliminar ceros a la izquierda, excepto si es el único carácter en el campo o si es seguido por un punto decimal y al menos un dígito
                e.target.value = e.target.value.replace(/^0+(?=\d)|(?<=\D)0+(?=\d)|(?<=\d)0+(?=\.)|^0+(?=[1-9])/g, '');

                // Evitar que el primer carácter sea un punto
                e.target.value = e.target.value.replace(/^(\.)/, '');

                // Reemplazar todo excepto los dígitos y el punto decimal
                e.target.value = e.target.value.replace(/[^\d.]/g, '');

                // Reemplazar múltiples puntos decimales con uno solo
                e.target.value = e.target.value.replace(/(\..*)\./g, '$1');

                calcularMontos();
            }

            if(e.target.classList.contains('detailDescuento')){
                //==== CONTROLANDO DE QUE EL VALOR SEA UN NÚMERO ====
                const valor = event.target.value;
                const producto_id   =   e.target.getAttribute('data-producto-id');
                const color_id      =   e.target.getAttribute('data-color-id');

                //==== SI EL INPUT ESTA VACÍO ====
                if(valor.trim().length === 0){
                    //===== CALCULAR DESCUENTO Y PINTARLO ======
                    calcularDescuento(producto_id,color_id,0);
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
                if(porcentaje_desc>100){
                    event.target.value = 100;
                    porcentaje_desc = event.target.value;
                }

                //==== CALCULAR DESCUENTO Y PINTARLO ====
                calcularDescuento(producto_id,color_id,porcentaje_desc)
                //===== CALCULAR Y PINTAR MONTOS =======
                calcularMontos();
            }

        })

        //===== ELIMINAR PRODUCTO-COLOR DEL CARRITO =========
        document.addEventListener('click',(e)=>{
            if(e.target.classList.contains('delete-product')){
                console.log(e.target);
               eliminarProductoColor(e.target);
            }
        })

        //======= GRABAR =======
        document.querySelector('#formActualizarVenta').addEventListener('submit',(e)=>{
            e.preventDefault();

            toastr.clear();
            if(carrito.length === 0){
                toastr.error('EL DETALLE DE VENTA ESTÁ VACÍO!!!');
                return;
            }

            actualizarVenta(e.target);

        })

        //=========== MODAL DESPACHO =========
        document.querySelector('.btn-envio').addEventListener('click',()=>{

            //======= COLCANDO EN MODAL ENVIO EL NOMBRE DEL CLIENTE =======
            const cliente_nombre            =   $("#cliente").find('option:selected').text();
            console.log(cliente_nombre);

            const nroDocumento              =   cliente_nombre.split(':')[1].split('-')[0].trim();
            const cliente_nombre_recortado  =   cliente_nombre.split('-')[1].trim()
            const tipo_documento            =   cliente_nombre.split(':')[0];

            console.log(cliente_nombre);
            console.log(cliente_nombre_recortado);
            console.log(nroDocumento);

            if(tipo_documento === "DNI" || tipo_documento === "CARNET EXT."){
                //====== COLOCAR TEXTO DEL SPAN =====
                document.querySelector('.span-tipo-doc-dest').textContent     =   tipo_documento;
                //====== SELECCIONAR LA OPCIÓN RESPECTIVA EN SELECT TIPO DOC DEST ======
                if(tipo_documento === "DNI"){
                    $('#tipo_doc_destinatario').val(0).trigger('change');
                    if(nroDocumento.trim() != "99999999"){
                        document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                        document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                        document.querySelector('#nombres_destinatario').value   =   cliente_nombre_recortado;
                    }
                }
                if(tipo_documento === "CARNET EXT."){
                    $('#tipo_doc_destinatario').val(1).trigger('change');
                    document.querySelector('#nro_doc_destinatario').value   =   nroDocumento;
                    document.querySelector('#nombres_destinatario').value   =   cliente_nombre_recortado;
                }
            }

            //========= ABRIR MODAL ENVÍO =======
            $("#modal_envio").modal("show");
        })
    }

    //====== CARGAR SELECT2 =======
    function loadSelect2(){

        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
        });

        $(".select2_modal_cliente").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%'
        });

        $('#cliente').select2({
            width:'100%',
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
                    return "No se encontraron productos";
                }
            },
            ajax: {
                url: '{{route("utilidades.getClientes")}}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data,params) {
                    if(data.success){
                        params.page     =   params.page || 1;
                        const clientes  =   data.clientes;
                        return {
                            results: clientes.map(item => ({
                                id: item.id,
                                text: item.descripcion
                            })),
                            pagination: {
                                more: data.more
                            }
                        };
                    }else{
                        toastr.error(data.message,'ERROR EN EL SERVIDOR');
                        return {
                            results:[]
                        }
                    }

                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(data) {
                if (data.loading) {
                    return $('<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>');
                }
                return data.text;
            },
        });

        $('#producto').select2({
            width:'100%',
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
                url: '{{route('utilidades.getProductosTodos')}}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        almacen_id: $('#almacen').val(),
                        page: params.page || 1
                    };
                },
                processResults: function(data,params) {
                    if(data.success){
                        params.page     =   params.page || 1;
                        const productos =   data.productos;
                        return {
                            results: productos.map(item => ({
                                id: item.producto_id,
                                text: item.producto_completo
                            })),
                            pagination: {
                                more: data.more
                            }
                        };
                    }else{
                        toastr.error(data.message,'ERROR EN EL SERVIDOR');
                        return {
                            results:[]
                        }
                    }

                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(data) {
                if (data.loading) {
                    return $('<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>');
                }
                return data.text;
            },
        });

    }

    function cambiarCondicion(condicion_id) {

        if (condicion_id) {
            const condiciones           = @json($condiciones);
            const condicion_filtrada    = condiciones.find((c) => c.id == condicion_id);
            const dias                  = condicion_filtrada.dias;

            const fecha_registro = @json($documento->fecha_documento);

            if (fecha_registro) {

                const fecha = new Date(fecha_registro);

                if (isNaN(fecha.getTime())) {
                    console.error("Fecha de vencimiento inválida");
                    return;
                }

                fecha.setDate(fecha.getDate() + dias);

                const nueva_fecha_vencimiento = fecha.toISOString().split('T')[0];

                document.querySelector('#fecha_vencimiento').value = nueva_fecha_vencimiento;
            }
        }
    }






    function regresarClick(event){
        event.preventDefault();
        if (!event.target.classList.contains("disabled")) {
            event.target.classList.add("disabled");
            window.location.href = '{{ route('ventas.documento.index') }}';
        }
    }

    //===== ELIMINAR PRODUCTO COLOR ====
    function eliminarProductoColor(pc){

       //========== obteniendo producto_id color_id ======
       const producto_id    =   pc.getAttribute('data-producto');
       const color_id       =   pc.getAttribute('data-color');


       //===== OBTENIENDO ITEM DEL CARRITO ========
        const item = carrito.filter((c)=>{
            return c.producto_id == producto_id && c.color_id == color_id;
        })


        //=== FORMANDO OBJETO ====
         const producto = {
            producto_id    : producto_id,
            color_id       : color_id,
            tallas         :   item[0].tallas
        }

        //===== ELIMINANDO DEL CARRITO ===
        carrito = carrito.filter((c)=>{
              return !(c.producto_id == producto_id && c.color_id == color_id);
        })

        //this.actualizarStockLogico(producto,'eliminar')

        destruirDataTable(dtDetalleVenta);
        limpiarTabla('table-detalle')
        pintarDetalle();
        dtDetalleVenta  =   iniciarDataTable('table-detalle');
        calcularMontos();

        toastr.success(`${item[0].producto_nombre} - ${item[0].color_nombre}`,'ELIMINADO DEL DETALLE');

    }

    //========= VALIDAR TIPO DOC =======
    function validarTipo() {
        var enviar = false

        if ($('#tipo_cliente_documento').val() == '0' && $('#tipo_venta').val() == 'FACTURA') {
            toastr.error('El tipo de documento del cliente es diferente a RUC.', 'Error');
            enviar = true;
        }
        return enviar
    }

    //=================== PINTAR MONTOS ==============
    const pintarMontos = ()=>{
        tfootSubtotal.textContent   =   cotizacion.sub_total;
        tfootEmbalaje.value         =   cotizacion.monto_embalaje;
        tfootEnvio.value            =   cotizacion.monto_envio;
        tfootTotal.textContent      =   cotizacion.total;
        tfootIgv.textContent        =   cotizacion.total_igv;
        tfootTotalPagar.textContent =   cotizacion.total_pagar;
    }

    function actualizarVenta(formActualizarVenta){

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
        title: "Desea actualizar el documento de venta?",
        text: "Se realizarán cambios",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, actualizar!",
        cancelButtonText: "No!",
        reverseButtons: true
        }).then(async (result) => {
        if (result.isConfirmed) {

            try {

                Swal.fire({
                    title: 'Actualizando documento de venta...',
                    text: 'Por favor, espera',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });


                const documento_id  =   @json($documento->id);
                const formData      =   new FormData(formActualizarVenta);
                formData.append('lstVenta',JSON.stringify(carrito));
                formData.append('amounts',JSON.stringify(amounts));
                formData.append('tipo_venta',@json($documento->tipo_venta_id));

                const res           =   await axios.post(route('ventas.documento.update',{id:documento_id}),formData,{
                    headers: {
                        "X-HTTP-Method-Override": "PUT"
                    }
                });

                if(res.data.success){

                    toastr.success(res.data.message,'OPERACIÓN COMPLETADA');

                    let url_open_pdf = '{{ route("ventas.documento.comprobante", [":id", ":size"]) }}'
                    .replace(':id', res.data.documento_id)
                    .replace(':size', 80);

                    window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');
                    location    = "{{ route('ventas.documento.index') }}";

                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
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
                    toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.', 'ERROR DE CONEXIÓN');
                } else {
                    toastr.error(error,'ERROR EN LA PETICIÓN ACTUALIZAR VENTA');
                    Swal.close();
                }
            }

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire({
            title: "Operación cancelada",
            text: "No se realizaron acciones",
            icon: "error"
            });
        }
        });
    }


    //=========== ENVIAR VENTA ===========
    function enviarVenta()
    {
        axios.get("{{ route('Caja.movimiento.verificarestado') }}").then((value) => {
            let data = value.data;
            if (!data.success) {
                toastr.error(data.mensaje);
            } else {
                let envio_ok = true;

                var tipo = validarTipo();

                if (tipo == false) {
                    cargarProductos();
                    //CARGAR DATOS TOTAL
                    // $('#monto_sub_total').val($('.subtotal').text())
                    // $('#monto_total_igv').val($('.igv').text())
                    // $('#monto_total').val($('.total').text())

                    document.getElementById("moneda").disabled = false;
                    document.getElementById("observacion").disabled = false;
                    document.getElementById("fecha_documento_campo").disabled = false;
                    document.getElementById("fecha_atencion_campo").disabled = false;
                    document.getElementById("empresa_id").disabled = false;
                    document.getElementById("cliente_id").disabled = false;
                    document.getElementById("condicion_id").disabled = false;
                    //HABILITAR EL CARGAR PAGINA
                }
                else
                {
                    envio_ok = false;
                }

                if(envio_ok)
                {
                    let formDocumento = document.getElementById('enviar_documento');
                    let formData = new FormData(formDocumento);

                    var object = {};
                    formData.forEach(function(value, key){
                        object[key] = value;
                    });

                    //var json = JSON.stringify(object);

                    var datos = object;
                    var init = {
                        // el método de envío de la información será POST
                        method: "POST",
                        headers: { // cabeceras HTTP
                            // vamos a enviar los datos en formato JSON
                            'Content-Type': 'application/json'
                        },
                        // el cuerpo de la petición es una cadena de texto
                        // con los datos en formato JSON
                        body: JSON.stringify(datos) // convertimos el objeto a texto
                    };

                    var url = '{{ route("consultas.ventas.documento.no.update",":id") }}';
                    url = url.replace(":id","{{ $documento->id }}")
                    var textAlert = "¿Seguro que desea guardar cambios?";
                    Swal.fire({
                        title: 'Opción Guardar',
                        text: textAlert,
                        icon: 'question',
                        customClass: {
                            container: 'my-swal'
                        },
                        showCancelButton: true,
                        confirmButtonColor: "#1ab394",
                        confirmButtonText: 'Si, Confirmar',
                        cancelButtonText: "No, Cancelar",
                        showLoaderOnConfirm: true,
                        allowOutsideClick: false,
                        preConfirm: (login) => {
                            return fetch(url,init)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText)
                                    }
                                    return response.json()
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        `Ocurrió un error`
                                    );
                                })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value !== undefined && result.isConfirmed) {
                            if(result.value.errors)
                            {
                                let mensaje = sHtmlErrores(result.value.data.mensajes);
                                toastr.error(mensaje);

                                asegurarCierre = 1;
                                document.getElementById("moneda").disabled = true;
                                document.getElementById("observacion").disabled = true;
                                document.getElementById("fecha_documento_campo").disabled = true;
                                document.getElementById("fecha_atencion_campo").disabled = true;
                                document.getElementById("empresa_id").disabled = true;
                                document.getElementById("cliente_id").disabled = true;
                                document.getElementById("condicion_id").disabled = true;
                            }
                            else if(result.value.success)
                            {
                                toastr.success('¡Documento de venta modificado!','Exito')
                                console.log(result);
                                asegurarCierre = 5;

                                location = "{{ route('ventas.documento.index') }}";
                            }
                            else
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: '¡'+ result.value.mensaje +'!',
                                    customClass: {
                                        container: 'my-swal'
                                    },
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                                asegurarCierre = 1;
                                $('#asegurarCierre').val(1);
                                document.getElementById("moneda").disabled = true;
                                document.getElementById("observacion").disabled = true;
                                document.getElementById("fecha_documento_campo").disabled = true;
                                document.getElementById("fecha_atencion_campo").disabled = true;
                                document.getElementById("empresa_id").disabled = true;
                                document.getElementById("cliente_id").disabled = true;
                                document.getElementById("condicion_id").disabled = true;
                            }
                        }
                    });

                }
            }
        })
    }


    //======== CARGAR PRODUCTOS ======
    function cargarProductos() {
        $('#productos_tabla').val(JSON.stringify(carrito));
    }

    function obtenerInputsLlenos(){

    }


    //=========== AGREGAR PRODUCTOS AL CARRITO =============
    async function agregarProducto() {

        const inputsCantidad = document.querySelectorAll('.inputCantidad');

        for (const ic of inputsCantidad) {

            const cantidad = ic.value ? ic.value : null;
            if (cantidad) {

                const producto      = formarProducto(ic);
                const indiceExiste  = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                //===== PRODUCTO NUEVO =====
                if (indiceExiste == -1) {
                                const objProduct = {
                                    producto_id:            producto.producto_id,
                                    color_id:               producto.color_id,
                                    modelo_nombre:          producto.modelo_nombre,
                                    producto_nombre:        producto.producto_nombre,
                                    producto_codigo:        producto.producto_codigo,
                                    color_nombre:           producto.color_nombre,
                                    precio_venta:           producto.precio_venta,
                                    monto_descuento:        0,
                                    porcentaje_descuento:   0,
                                    precio_venta_nuevo:     0,
                                    subtotal_nuevo:         0,
                                    tallas: [{
                                        talla_id:           producto.talla_id,
                                        talla_nombre:       producto.talla_nombre,
                                        cantidad:           producto.cantidad
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
                const indiceProductoColor = carrito.findIndex(p => p.producto_id == producto.producto_id && p.color_id == producto.color_id);

                if (indiceProductoColor !== -1) {
                    const indiceTalla = carrito[indiceProductoColor].tallas.findIndex(t => t.talla_id == producto.talla_id);

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

    //====== REORDENAR CARRITO =======
    const reordenarCarrito= ()=>{
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    //============ VALIDAR CANTIDAD CON STOCK LOGICO =======
    async function validarCantidadCarrito(inputCantidad){
        const stockLogico           =   await  this.getStockLogico(inputCantidad);
        const cantidadSolicitada    =   inputCantidad.value;
        return stockLogico>=cantidadSolicitada;
    }

    //====== OBTENER STOCK LOGICO ACTUALIZADO DEL PRODUCTO COLOR TALLA =====
    async function getStockLogico(inputCantidad){
            const producto_id           =   inputCantidad.getAttribute('data-producto-id');
            const color_id              =   inputCantidad.getAttribute('data-color-id');
            const talla_id              =   inputCantidad.getAttribute('data-talla-id');

            try {
                const url = `/get-stocklogico/${producto_id}/${color_id}/${talla_id}`;
                const response = await axios.get(url);
                if(response.data.message=='success'){
                    const stock_logico  =   response.data.data[0].stock_logico;
                    return stock_logico;
                }

            } catch (error) {
                toastr.error(`El producto no cuenta con registros en esa talla`,"Error");
                event.target.value='';
                console.error('Error al obtener stock logico:', error);
                return null;
            }
    }

    //============== formar objeto producto ================
    function formarProducto(ic){

        const producto_id       = ic.getAttribute('data-producto-id');
        const producto_nombre   = ic.getAttribute('data-producto-nombre');
        const color_id          = ic.getAttribute('data-color-id');
        const color_nombre      = ic.getAttribute('data-color-nombre');
        const talla_id          = ic.getAttribute('data-talla-id');
        const talla_nombre      = ic.getAttribute('data-talla-nombre');
        const precio_venta      = $('#precio_venta').find('option:selected').text();
        const cantidad          = parseFloat(ic.value?ic.value:0);

        const monto_descuento           =   0.0;
        const porcentaje_descuento      =   0.0;
        const precio_venta_nuevo        =   0.0;
        const subtotal_nuevo            =   0.0;

        const producto = {producto_id,producto_nombre,color_id,color_nombre,
                                talla_id,talla_nombre,cantidad,precio_venta,
                                monto_descuento,porcentaje_descuento,precio_venta_nuevo,subtotal_nuevo};
        return producto;
    }

    //============= ACTUALIZAR STOCK LOGICO ==============
    async function actualizarStockLogico(producto,modo,cantidadAnterior){
        //modo=="eliminar"?asegurarCierre=0:asegurarCierre=1;
        //carrito.length>0?asegurarCierre=1:0;
        try {
            const res= await this.axios.post(route('consultas.ventas.documento.no.cantidad'), {
                'producto_id'   :   producto.producto_id,
                'color_id'      :   producto.color_id,
                'talla_id'      :   producto.talla_id,
                'cantidad'      :   producto.cantidad,
                'condicion'     :   asegurarCierre,
                'modo'          :   modo,
                'cantidadAnterior'    :   cantidadAnterior,
                'tallas'        :   producto.tallas,
            });

            console.log(res)

        } catch (ex) {

        }
    }

     //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
     async function getColoresTallas(){
        mostrarAnimacion();
        const producto_id   =   $('#producto').val();
        const almacen_id    =   $('#almacen').val();

        if(producto_id && almacen_id){
            try {
                const res   =   await   axios.get(route('utilidades.getColoresTalla',{almacen_id,producto_id}));
                if(res.data.success){
                    destruirDataTable(dtStocksVenta);
                    pintarTableStocks(res.data.producto_color_tallas);
                    dtStocksVenta   =   iniciarDataTable('table-stocks');
                    pintarPreciosVenta(res.data.producto_color_tallas);
                    //loadCarrito();
                    //loadPrecioVentaProductoCarrito(producto_id);
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }finally{
                ocultarAnimacion();
            }
        }else{
            destruirDataTable(dtStocksVenta);
            limpiarTabla('table-stocks');
            dtStocksVenta       =    iniciarDataTable('table-stocks');
            ocultarAnimacion();
        }
    }


    //======= CARGAR STOCKS LOGICOS DE PRODUCTOS POR MODELO =======
    async function getProductosByModelo(idModelo){
        mostrarAnimacion();
        modelo_id = idModelo;
        btnAgregarDetalle.disabled=true;

        if(modelo_id){
            try {
                const url = `/get-producto-by-modelo/${modelo_id}`;
                const response = await axios.get(url);
                console.log(response.data);
                pintarTableStocks(response.data.stocks,tallasBD,response.data.producto_colores);
                loadCantPrevias();
            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            }finally{
                ocultarAnimacion();
            }
        }else{
            tableStocksBody.innerHTML = ``;
            ocultarAnimacion();
        }
    }

    //=========== CALCULAR MONTOS =======
    const calcularMontos = ()=>{
        let subtotal    =   0;
        let embalaje    =   tfootEmbalaje.value?parseFloat(tfootEmbalaje.value):0;
        let envio       =   tfootEnvio.value?parseFloat(tfootEnvio.value):0;
        let total       =   0;
        let igv         =   0;
        let total_pagar =   0;
        let descuento   =   0;

        //====== subtotal es la suma de todos los productos ======
        carrito.forEach((c)=>{
            if(c.porcentaje_descuento === 0){
                subtotal    +=  parseFloat(c.subtotal);
            }else{
                subtotal    +=  parseFloat(c.subtotal_nuevo);
            }
            descuento += parseFloat(c.monto_descuento);
        })

        total_pagar =   subtotal + embalaje + envio;
        total       =   total_pagar/1.18;
        igv         =   total_pagar - total;

        tfootTotalPagar.textContent =   'S/. ' + total_pagar.toFixed(2);
        tfootIgv.textContent        =   'S/. ' + igv.toFixed(2);
        tfootTotal.textContent      =   'S/. ' + total.toFixed(2);
        tfootSubtotal.textContent   =   'S/. ' + subtotal.toFixed(2);
        tfootDescuento.textContent  =   'S/. ' + descuento.toFixed(2);

        amounts.totalPagar        =   total_pagar.toFixed(2);
        amounts.igv               =   igv.toFixed(2);
        amounts.total             =   total.toFixed(2);
        amounts.embalaje          =   embalaje.toFixed(2);
        amounts.envio             =   envio.toFixed(2);
        amounts.subtotal          =   subtotal.toFixed(2);
        amounts.monto_descuento   =   descuento.toFixed(2);
    }

    const cargarProductosPrevios=()=>{
        //====== CARGANDO CARRITO ======
        const producto_color_procesados = [];

        detalles.forEach((productoPrevio)=>{
            const id    =   `${productoPrevio.producto_id}-${productoPrevio.color_id}`;

            if(!producto_color_procesados.includes(id)){
                const producto ={
                    producto_id: productoPrevio.producto_id,
                    producto_nombre:productoPrevio.nombre_producto,
                    color_id:productoPrevio.color_id,
                    color_nombre:productoPrevio.nombre_color,
                    precio_venta:parseFloat(productoPrevio.precio_unitario).toFixed(2),
                    subtotal:0,
                    subtotal_nuevo:0,
                    porcentaje_descuento: parseFloat(productoPrevio.porcentaje_descuento),
                    monto_descuento:0,
                    precio_venta_nuevo:0,
                    tallas:[]
                }

                //==== BUSCANDO SUS TALLAS ====
                const tallas = detalles.filter((t)=>{
                    return t.producto_id==productoPrevio.producto_id && t.color_id==productoPrevio.color_id;
                })

                if(tallas.length > 0){
                    const producto_color_tallas = [];
                    tallas.forEach((t)=>{
                        const talla = {
                            talla_id:t.talla_id,
                            talla_nombre:t.nombre_talla,
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

        //===== PINTANDO DETALLE ======
        pintarDetalle();

        //========= PINTAR DESCUENTOS Y CALCULARLOS ============
        carrito.forEach((c)=>{
            calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
        })


        //===== CALCULAR MONTOS Y PINTARLOS ======
        calcularMontos();
    }

    //======= CALCULAR DESCUENTO ========
    const calcularDescuento = (producto_id,color_id,porcentaje_descuento)=>{
        const indiceExiste = carrito.findIndex((c)=>{
            return c.producto_id==producto_id && c.color_id==color_id;
        })

        if(indiceExiste !== -1){
            const producto_color_editar =  carrito[indiceExiste];

            //===== APLICANDO DESCUENTO ======
            producto_color_editar.porcentaje_descuento =    porcentaje_descuento;
            producto_color_editar.monto_descuento      =    porcentaje_descuento === 0?0:producto_color_editar.subtotal*(porcentaje_descuento/100);
            producto_color_editar.precio_venta_nuevo   =    porcentaje_descuento === 0?0:(producto_color_editar.precio_venta*(1-porcentaje_descuento/100)).toFixed(2);
            producto_color_editar.subtotal_nuevo       =    porcentaje_descuento === 0?0:(producto_color_editar.subtotal*(1-porcentaje_descuento/100)).toFixed(2);

            carrito[indiceExiste] = producto_color_editar;


            //==== ACTUALIZANDO PRECIO VENTA Y SUBTOTAL EN EL HTML ====
            const detailPrecioVenta =   document.querySelector(`.precio_venta_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`);
            const detailSubtotal    =   document.querySelector(`.subtotal_${producto_color_editar.producto_id}_${producto_color_editar.color_id}`);

            if(porcentaje_descuento !== 0){
                detailPrecioVenta.textContent = producto_color_editar.precio_venta_nuevo;
                detailSubtotal.textContent    = producto_color_editar.subtotal_nuevo;
            }else{
                detailPrecioVenta.textContent   =   producto_color_editar.precio_venta;
                detailSubtotal.textContent      =   producto_color_editar.subtotal;
            }

        }
    }

    //======== CARGAR SUBTOTAL =======
    function calcularSubTotal(){
        carrito.forEach((p)=>{
            let cantidadTallas=0;
            p.tallas.forEach((t)=>{
                cantidadTallas += parseFloat(t.cantidad);
            })
            p.subtotal=cantidadTallas* parseFloat(p.precio_venta);
        })
    }

    //======= PINTAR PRECIOS VENTA =======
    function pintarPreciosVenta(producto_color_tallas){
        //======= LIMPIAR SELECT2 DE PRODUCTOS ======
        $('#precio_venta').empty();

        //====== LLENAR =======

        if(producto_color_tallas){
            if(producto_color_tallas.precio_venta_1 != null){
                const option_1 = new Option(producto_color_tallas.precio_venta_1, 'precio_venta_1', false, false);
                $('#precio_venta').append(option_1);
            }

            if(producto_color_tallas.precio_venta_2 != null){
                const option_2 = new Option(producto_color_tallas.precio_venta_2, 'precio_venta_2', false, false);
                $('#precio_venta').append(option_2);
            }

            if(producto_color_tallas.precio_venta_3 != null){
                const option_3 = new Option(producto_color_tallas.precio_venta_3, 'precio_venta_3', false, false);
                $('#precio_venta').append(option_3);
            }
        }

        // Refrescar Select2
        $('#precio_venta').trigger('change');
    }

    //========= PINTAR TABLA STOCKS ==========
    const pintarTableStocks = (producto) => {
        let filas = ``;
        const tableStocksBody = document.querySelector('#table-stocks tbody');

        producto.colores.forEach((color) => {
            filas += `
                <tr>
                    <th scope="row" data-producto=${producto.id} data-color=${color.id}>
                        <div style="width:200px;">${producto.nombre}</div>
                    </th>
                    <th scope="row">${color.nombre}</th>
            `;

            color.tallas.forEach((talla) => {
                if (talla.stock_logico == 0) {
                    filas += `
                        <td style="background-color: rgb(210, 242, 242);">
                            <p style="margin:0;width:20px;text-align:center;">${talla.stock_logico}</p>
                        </td>
                          <td width="8%">
                            <input style="width:50px;text-align:center;border: 2px solid #207ebc;" type="text" class="form-control inputCantidad"
                                id="inputCantidad_${producto.id}_${color.id}_${talla.id}"
                                data-producto-id="${producto.id}"
                                data-producto-nombre="${producto.nombre}"
                                data-color-nombre="${color.nombre}"
                                data-talla-nombre="${talla.nombre}"
                                data-color-id="${color.id}" data-talla-id="${talla.id}"
                                data-producto-codigo="${producto.codigo}">
                        </td>
                    `;
                } else {
                    filas += `
                        <td style="background-color: rgb(210, 242, 242);">
                            <p style="margin:0;width:20px;text-align:center;font-weight:bold;">${talla.stock_logico}</p>
                        </td>
                        <td width="8%">
                            <input style="width:50px;text-align:center;border: 2px solid #207ebc;" type="text" class="form-control inputCantidad"
                                id="inputCantidad_${producto.id}_${color.id}_${talla.id}"
                                data-producto-id="${producto.id}"
                                data-producto-nombre="${producto.nombre}"
                                data-color-nombre="${color.nombre}"
                                data-talla-nombre="${talla.nombre}"
                                data-color-id="${color.id}" data-talla-id="${talla.id}"
                                data-producto-codigo="${producto.codigo}">
                        </td>
                    `;
                }
            });

            filas += `</tr>`;
        });

        tableStocksBody.innerHTML = filas;
    }

    //============== PINTAR DETALLE ===========
    function pintarDetalle(){
        let fila        = ``;
        let htmlTallas  = ``;

        carrito.forEach((c)=>{
            htmlTallas=``;
                fila+= `<tr>
                            <td>
                                <i class="fas fa-trash-alt btn btn-danger delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>
                            </td>
                            <th>${c.producto_nombre}</th>
                            <th>${c.color_nombre}</th>
                        `;

                //tallas
                tallasBD.forEach((t)=>{
                    let cantidad = c.tallas.filter((ct)=>{
                        return t.id==ct.talla_id;
                    });
                    cantidad.length!=0?cantidad=cantidad[0].cantidad:cantidad=0;

                    if(cantidad == 0){
                        htmlTallas += `<td></td>`;
                    }else{
                        htmlTallas += `<td style="font-weight:bold;">${cantidad}</td>`;
                    }

                })


                htmlTallas+=`   <td style="text-align: right;">
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
                                    <input data-producto-id="${c.producto_id}" data-color-id="${c.color_id}"
                                    style="width:130px; margin: 0 auto;" value="${c.porcentaje_descuento}"
                                    class="form-control detailDescuento"></input>
                                </td>
                            </tr>`;

                fila+=htmlTallas;
                tableDetalleBody.innerHTML=fila;
        })
    }

    //======== LLENAR CANTIDADES PREVIAS AL TABLERO DE STOCKS =====
    function loadCantPrevias(){

        carrito.forEach((p)=>{
            const select_precio_venta = document.querySelector(`#precio-venta-${p.producto_id}`);
            console.log('lad cant prev')
            console.log(p);
            console.log(select_precio_venta)
            if(select_precio_venta){
                select_precio_venta.value = p.precio_venta;
            }
            p.tallas.forEach((t)=>{
                const inputLoad = document.querySelector(`#inputCantidad_${p.producto_id}_${p.color_id}_${t.talla_id}`);
                if(inputLoad){
                    inputLoad.value = t.cantidad;
                }
            })
        })
    }

    function cambiarAlmacen(almacen_id){

        toastr.clear();

        mostrarAnimacion();

        //======== LIMPIAR SELECTS ======
        $('#producto').val(null).trigger('change');
        $('#precio_venta').val(null).trigger('change');

        //======= LIMPIAR TABLERO STOCKS ======
        destruirDataTable(dtStocksVenta);
        limpiarTabla('table-stocks');
        dtStocksVenta       =    iniciarDataTable('table-stocks');

        //========== LIMPIAR DETALLE DE LA VENTA ========
        carrito.length  =   0;
        destruirDataTable(dtDetalleVenta);
        limpiarTabla('table-detalle');
        pintarDetalle(carrito);
        dtDetalleVenta       =    iniciarDataTable('table-detalle');

        carrito.forEach((c)=>{
            calcularDescuento(c.producto_id,c.color_id,c.porcentaje_descuento);
         })
        calcularMontos();

        ocultarAnimacion();
        toastr.info('SE HA LIMPIADO EL FORMULARIO');

    }


    //========= evento al cerrar la ventana ========
    /*window.onbeforeunload = () => {
        if (asegurarCierre == 1) {
            devolverCantidades()
        }
    }
        */

    //======== devolver cantidades =========
    /*async function devolverCantidades(){

        await this.axios.post(route('consultas.ventas.documento.no.devolver.cantidades'), {
            detalles: JSON.stringify(detalles),
            carrito: JSON.stringify(carrito)
        });

    }*/


</script>

@endpush
