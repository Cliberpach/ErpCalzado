@extends('layout')
@section('content')

@section('almacenes-active', 'active')
@section('nota_ingreso-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVAS NOTA DE INGRESO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.nota_ingreso.index') }}">Notas de Ingreso</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
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
                            @include('almacenes.nota_ingresos.forms.form_nota_ingreso_create')
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class=""><b>Detalle de la Nota de Ingreso</b></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row head-nota-ingreso">
                                                <div class="col-lg-3 col-xs-12">
                                                    <label class="required">Modelo</label>
                                                    <select id="modelo"
                                                        class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                                        onchange="getNiProductos(this)">
                                                        <option></option>
                                                        @foreach ($modelos as $modelo)
                                                            <option value="{{ $modelo->id }}"
                                                                {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                                                {{ $modelo->descripcion }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback"><b><span
                                                                id="error-producto"></span></b></div>
                                                </div>
                                            </div>

                                            <div class="form-group row mt-3 content-window">
                                                <div class="col-lg-12">

                                                    @include('almacenes.nota_ingresos.tables.tbl_ni_productos')
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1">
                                                <div class="col-lg-2 col-xs-12">
                                                    <button disabled type="button" id="btn_agregar_detalle"
                                                        class="btn btn-warning btn-block"><i class="fa fa-plus"></i>
                                                        AGREGAR</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-sm" style="text-transform:uppercase">
                                        <div class="col-lg-12">
                                            <div class="table-responsive">
                                                @include('almacenes.nota_ingresos.tables.tbl_ni_detalle')
                                            </div>
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
                            <a href="{{ route('almacenes.nota_ingreso.index') }}" id="btn_cancelar"
                                class="btn btn-w-m btn-default">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" id="btn_grabar" form="enviar_ingresos"
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
@stop

@push('styles')

<style>
    .talla-no-creada {
        color: rgb(201, 47, 9);
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
    });
</script>


<script>
    const selectModelo = document.querySelector('#modelo');
    const tokenValue = document.querySelector('input[name="_token"]').value;
    const tallas = @json($tallas);
    const colores = @json($colores);
    const bodyTablaProductos = document.querySelector('#tabla_ni_productos tbody');
    const bodyTablaDetalle = document.querySelector('#tabla_ni_detalle tbody');
    const btnAgregarDetalle = document.querySelector('#btn_agregar_detalle');
    const inputProductos = document.querySelector('#notadetalle_tabla');

    const formNotaIngreso = document.querySelector('#enviar_ingresos');
    const btnGrabar = document.querySelector('#btn_grabar');
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    let modelo_id = null;
    let carrito = [];
    let dtNiDetalle = null;
    let dtNiProductos = null;


    document.addEventListener('DOMContentLoaded', () => {
        events();

        dtNiDetalle = iniciarDataTable('tabla_ni_detalle');
        dtNiProductos = iniciarDataTable('tabla_ni_productos');
    })

    function events() {
        //========= EVENTO AGREGAR DETALLE ============
        btnAgregarDetalle.addEventListener('click', () => {

            const inputsCantidad = document.querySelectorAll('.inputCantidad');
            inputsCantidad.forEach((ic) => {
                const cantidad = ic.value ? ic.value : 0;
                if (cantidad != 0) {
                    const producto = formarProducto(ic);
                    const indiceExiste = carrito.findIndex((p) => {
                        return p.producto_id == producto.producto_id && p.color_id == producto
                            .color_id && p.talla_id == producto.talla_id
                    })

                    if (indiceExiste == -1) {
                        carrito.push(producto);
                    } else {
                        const productoModificar = carrito[indiceExiste];
                        productoModificar.cantidad = producto.cantidad;
                        carrito[indiceExiste] = productoModificar;

                    }
                } else {
                    const producto = formarProducto(ic);
                    const indiceExiste = carrito.findIndex((p) => {
                        return p.producto_id == producto.producto_id && p.color_id == producto
                            .color_id && p.talla_id == producto.talla_id
                    })
                    if (indiceExiste !== -1) {
                        carrito.splice(indiceExiste, 1);
                    }
                }

            })

            reordenarCarrito();

            destruirDataTable(dtNiDetalle);
            limpiarTabla('tabla_ni_detalle');
            pintarDetalleNotaIngreso(carrito);
            dtNiDetalle = iniciarDataTable('tabla_ni_detalle');

        })

        //======== EVENTO ELIMINAR PRODUCTO DEL CARRITO ============
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-product')) {
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');
                eliminarProducto(productoId, colorId);
                pintarDetalleNotaIngreso(carrito);
            }
        })

        //============ EVENTO ENVIAR FORMULARIO =============
        formNotaIngreso.addEventListener('submit', async (e) => {
            e.preventDefault();
            btnGrabar.disabled = true;

            if (carrito.length > 0) {

                Swal.fire({
                    title: 'Generar etiquetas adhesivas?',
                    text: "Se generarán de acuerdo a la cantidad de cada talla, con un límite de 100 etiquetas - DEBE HABILITAR LAS VENTANAS EMERGENTES EN SU NAVEGADOR",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, generar',
                    cancelButtonText: "No",
                }).then((result) => {

                    let generarAdhesivos = 'NO';

                    if (result.isConfirmed) {
                        generarAdhesivos = 'SI';
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        generarAdhesivos = 'NO';
                    }

                    const formData = new FormData(e.target);
                    formData.append('lstNi', JSON.stringify(carrito))
                    formData.append('registrador_id', @json($registrador->id));
                    formData.append('sede_id', @json($sede_id));
                    formData.append('generarAdhesivos', generarAdhesivos);
                    registrarNotaIngreso(formData, generarAdhesivos);
                })
            } else {
                toastr.error('El detalle de la nota de ingreso está vacío!!!')
                btnGrabar.disabled = false;
            }

        })

        //============== EVENTO VALIDACIÓN INPUT CANTIDADES ==========
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('inputCantidad')) {
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
            }
        })
    }

    async function registrarNotaIngreso(formData, generarAdhesivos) {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Desea registrar la nota de ingreso?",
            text: "Se ingresará stock en el almacén destino!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Procesando...",
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
                    toastr.clear();
                    const res = await axios.post(route('almacenes.nota_ingreso.store'), formData);
                    console.log(res);
                    if (res.data.success) {

                        if (generarAdhesivos === 'SI') {
                            window.open(route('almacenes.nota_ingreso.generarEtiquetas', {
                                nota_id: res.data.nota_id
                            }), '_blank');
                        }

                        window.location = route('almacenes.nota_ingreso.index');
                        toastr.success(res.data.message, 'OPERCIÓN COMPLETADA');
                    } else {
                        Swal.close();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            Swal.close();
                            toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
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



    //========= FUNCIÓN ELIMINAR PRODUCTO DEL CARRITO =============
    const eliminarProducto = (productoId, colorId) => {
        console.log(carrito);
        carrito = carrito.filter((p) => {
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    function mostrarAnimacion() {
        document.querySelector('.content-window').classList.add('sk__loading');
        document.querySelector('.sk-spinner').classList.remove('hide-window');
    }

    function ocultarAnimacion() {
        document.querySelector('.content-window').classList.remove('sk__loading');
        document.querySelector('.sk-spinner').classList.add('hide-window');
    }

    //============== FUNCIÓN OBTENER PRODUCTOS DE UN MODELO ==============
    async function getNiProductos(e) {

        toastr.clear();
        modelo_id = e.value;

        btnAgregarDetalle.disabled = true;

        const almacen_id = document.querySelector('#almacen_destino').value;

        if (modelo_id && !almacen_id) {
            mostrarAnimacion();
            toastr.error('DEBE SELECCIONAR UN ALMACÉN DE DESTINO!!!');

            destruirDataTable(dtNiProductos);
            limpiarTabla('tabla_ni_productos');
            dtNiProductos = iniciarDataTable('tabla_ni_productos');

            document.querySelector('#modelo').onchange = null;
            $('#modelo').val(null).trigger('change');
            document.querySelector('#modelo').onchange = function() {
                getNiProductos(this);
            };
            $('#almacen_destino').select2('open');
            ocultarAnimacion();
        }

        if (modelo_id && almacen_id) {
            mostrarAnimacion();
            try {
                const res = await axios.get(route('almacenes.nota_ingreso.getProductos', {
                    modelo_id,
                    almacen_id
                }));
                if (res.data.success) {
                    destruirDataTable(dtNiProductos);
                    limpiarTabla('tabla_ni_productos');
                    pintarTableStocks(tallas, res.data.productos);
                    dtNiProductos = iniciarDataTable('tabla_ni_productos');
                } else {
                    toastr.error(res.data.exception, res.data.message);
                }
            } catch (error) {
                toastr.error(error, 'ERROR AL REALIZAR LA SOLICITUD DE PRODUCTOS');
            } finally {
                ocultarAnimacion();
            }
        }

    }

    //============ REORDENAR CARRITO ===============
    const reordenarCarrito = () => {
        carrito.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }

    //=============== FORMAR OBJETO PRODUCTO PARA INSERTAR EN EL CARRITO POSTERIORMENTE =============
    const formarProducto = (ic) => {

        const producto_id = ic.getAttribute('data-producto-id');
        const producto_nombre = ic.getAttribute('data-producto-nombre');
        const color_id = ic.getAttribute('data-color-id');
        const color_nombre = ic.getAttribute('data-color-nombre');
        const talla_id = ic.getAttribute('data-talla-id');
        const talla_nombre = ic.getAttribute('data-talla-nombre');
        const cantidad = ic.value ? ic.value : 0;
        const producto = {
            producto_id,
            producto_nombre,
            color_id,
            color_nombre,
            talla_id,
            talla_nombre,
            cantidad
        };
        return producto;

    }

    //============ RENDERIZAR TABLA DE CANTIDADES ============
    const pintarTableStocks = (tallas, productos) => {
        let options = ``;

        const producto_color_procesados = [];

        productos.forEach((p) => {

            const llave_producto_color = `${p.producto_id}-${p.color_id}`;

            if (!producto_color_procesados.includes(llave_producto_color)) {
                options += `  <tr>
                                <th scope="row"  data-color=${p.color_id} >
                                    ${p.color_nombre}
                                </th>
                                <th scope="row" data-producto=${p.producto_id}>
                                    ${p.producto_nombre}
                                </th>
                        `;

                let htmlTallas = ``;

                tallas.forEach((t) => {

                    //======= BUSCAMOS SI EXISTE EL PRODUCTO-COLOR-TALLA ========
                    const existeProducto = productos.findIndex((item) => {
                        return item.producto_id == p.producto_id && item.color_id == p
                            .color_id && item.talla_id == t.id;
                    });

                    let classProducto = null;
                    let stock = 0;
                    let message = null;

                    existeProducto == -1 ? stock = 0 : stock = productos[existeProducto].stock;
                    existeProducto == -1 ? classProducto = 'talla-no-creada' : classProducto =
                        'talla-creada';
                    existeProducto == -1 ? message = 'AÚN NO SE HA CREADO ESTA TALLA' : message =
                        null;

                    let etiquetaStock = ``;

                    if (message) {
                        etiquetaStock =
                            `<td><p class="${classProducto}" title="${message}" >${stock}</p></td>`;
                    } else {
                        etiquetaStock = `<td><p class="${classProducto}" >${stock}</p></td>`;
                    }

                    htmlTallas += `
                                                ${etiquetaStock}
                                                <td >
                                                    <input type="text" class="form-control inputCantidad"
                                                    data-producto-id="${p.producto_id}"
                                                    data-producto-nombre="${p.producto_nombre}"
                                                    data-color-nombre="${p.color_nombre}"
                                                    data-talla-nombre="${t.descripcion}"
                                                    data-color-id="${p.color_id}" data-talla-id="${t.id}"></input>
                                                </td>
                                            `;
                })
                htmlTallas += `</tr>`;
                options += htmlTallas;

                //======= MARCANDO PRODUCTO COLOR COMO PROCESADO ========
                producto_color_procesados.push(llave_producto_color);
            }

        })


        bodyTablaProductos.innerHTML = options;
        btnAgregarDetalle.disabled = false;
    }

    //====== RENDERIZAR TABLA DETALLE NOTA INGRESO ==============
    function pintarDetalleNotaIngreso(carrito) {
        let fila = ``;
        let htmlTallas = ``;
        const producto_color_procesado = [];


        carrito.forEach((c) => {
            htmlTallas = ``;
            if (!producto_color_procesado.includes(`${c.producto_id}-${c.color_id}`)) {
                fila += `<tr>
                            <td>
                                <i class="fa fa-trash btn btn-danger delete-product"
                                data-producto="${c.producto_id}" data-color="${c.color_id}">
                                </i>
                            </td>
                            <th>${c.producto_nombre} - ${c.color_nombre}</th>`;

                //tallas
                tallas.forEach((t) => {
                    let cantidad = carrito.filter((ct) => {
                        return ct.producto_id == c.producto_id && ct.color_id == c.color_id && t
                            .id == ct.talla_id;
                    });
                    cantidad.length != 0 ? cantidad = cantidad[0].cantidad : cantidad = 0;
                    htmlTallas += `<td>${cantidad}</td>`;
                })



                fila += htmlTallas;
                bodyTablaDetalle.innerHTML = fila;
                producto_color_procesado.push(`${c.producto_id}-${c.color_id}`)
            }
        })
    }


    function cambiarAlmacenDestino() {

        destruirDataTable(dtNiProductos);
        limpiarTabla('tabla_ni_productos');
        dtNiProductos = iniciarDataTable('tabla_ni_productos')

        carrito = [];
        destruirDataTable(dtNiDetalle);
        limpiarTabla('tabla_ni_detalle');
        dtNiDetalle = iniciarDataTable('tabla_ni_detalle');

        $('#modelo').val(null).trigger('change');
    }
</script>
@endpush
