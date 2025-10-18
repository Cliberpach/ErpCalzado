@extends('layout')
@section('content')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>REGISTRAR NUEVA NOTA DE SALIDA</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.nota_salidad.index') }}">Nota de Salida</a>
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
                    @include('almacenes.nota_salidad.forms.form_ns_create')
                </div>
            </div>
        </div>

    </div>
</div>

@stop

@push('styles')
@endpush

@push('scripts')

<script>
    //Select2
    $(".select2_form").select2({
        placeholder: "SELECCIONAR",
        allowClear: true,
        width: '100%',
    });
</script>

<script>
    const tallasBD = @json($tallas);
    const tableStocksBody = document.querySelector('#tabla_ns_productos tbody');
    const btnAgregarDetalle = document.querySelector('#btn_agregar_detalle');
    const bodyTablaDetalle = document.querySelector('#tabla_ns_detalle tbody');
    let detallesSalida = [];
    let formNotaSalida = document.querySelector(
    '#enviar_nota_salida'); //boton que hace de formulario para enviar los registros salida
    const btnGrabar = document.querySelector('#btn_grabar'); // boton que guardar los registros de salida
    const inputProductos = document.querySelector('#notadetalle_tabla');
    let modelo_id = null;
    let dtNsDetalle = null;
    let dtNsProductos = null;

    document.addEventListener('DOMContentLoaded', () => {
        events();
        dtNsDetalle = iniciarDataTable('tabla_ns_detalle');
        dtNsProductos = iniciarDataTable('tabla_ns_productos');
    })

    function events() {

        //Agrega los datos que son de salida
        btnAgregarDetalle.addEventListener('click', () => {
            let inputsCantidad = document.querySelectorAll('.inputCantidad');
            inputsCantidad.forEach((ic) => {
                const cantidad = ic.value ? ic.value : 0;
                if (cantidad != 0) {
                    const producto = formarProducto(ic);
                    const indiceExiste = detallesSalida.findIndex((p) => {
                        return p.producto_id == producto.producto_id && p.color_id == producto
                            .color_id && p.talla_id == producto.talla_id
                    })
                    if (indiceExiste == -1) {
                        detallesSalida.push(producto);
                    } else {
                        const productoModificar = detallesSalida[indiceExiste];
                        productoModificar.cantidad = producto.cantidad;
                        detallesSalida[indiceExiste] = productoModificar;
                    }
                } else {
                    const producto = formarProducto(ic);
                    const indiceExiste = detallesSalida.findIndex((p) => {
                        return p.producto_id == producto.producto_id && p.color_id == producto
                            .color_id && p.talla_id == producto.talla_id
                    })
                    if (indiceExiste !== -1) {
                        detallesSalida.splice(indiceExiste, 1);
                    }
                }
                // let producto =formarProducto(ic);
                // detallesSalida.push(producto);
            })

            reordenarDetallesSalida();

            destruirDataTable(dtNsDetalle);
            limpiarTabla('tabla_ns_detalle');
            pintarDetallesSalida(detallesSalida);
            dtNsDetalle = iniciarDataTable('tabla_ns_detalle');
        })

        /////////////
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-product')) {
                const productoId = e.target.getAttribute('data-producto');
                const colorId = e.target.getAttribute('data-color');

                eliminarProducto(productoId, colorId);

                destruirDataTable(dtNsDetalle);
                limpiarTabla('tabla_ns_detalle');
                pintarDetallesSalida(detallesSalida);
                dtNsDetalle = iniciarDataTable('tabla_ns_detalle');
            }
        })

        //============ EVENTO ENVIAR FORMULARIO =============
        formNotaSalida.addEventListener('submit', (e) => {
            e.preventDefault();
            btnGrabar.disabled = true;

            if (detallesSalida.length > 0) {

                const formData = new FormData(formNotaSalida);
                formData.append('lstNs', JSON.stringify(detallesSalida))
                formData.append('registrador_id', @json($registrador->id));
                formData.append('sede_id', @json($sede_id));
                registrarNotaSalida(formData);

            } else {
                toastr.error('El detalle de la nota de salida está vacío!!!')
                btnGrabar.disabled = false;
            }

        })

        //======= validar el contenido input cantidad =======
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('inputCantidad')) {
                e.target.value = e.target.value.replace(/^0+|[^0-9]/g, '');
                this.validarCantidadInstantanea(e);
            }
        })


    }

    //============ REORDENAR CARRITO ===============
    const reordenarDetallesSalida = () => {
        detallesSalida.sort(function(a, b) {
            if (a.producto_id === b.producto_id) {
                return a.color_id - b.color_id;
            } else {
                return a.producto_id - b.producto_id;
            }
        });
    }


    function pintarDetallesSalida(detallesSalida) {
        let fila = ``;
        let htmlTallas = ``;
        const producto_color_procesado = [];

        detallesSalida.forEach((c) => {
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
                tallasBD.forEach((t) => {
                    let cantidad = detallesSalida.filter((ct) => {
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

    //========= FUNCIÓN ELIMINAR PRODUCTO DEL CARRITO =============
    const eliminarProducto = (productoId, colorId) => {
        console.log(detallesSalida);
        detallesSalida = detallesSalida.filter((p) => {
            return !(p.producto_id == productoId && p.color_id == colorId);
        })
    }

    //======= CARGAR STOCKS LOGICOS DE PRODUCTOS POR MODELO =======
    async function getProductosByModelo(idModelo) {

        toastr.clear();
        modelo_id = idModelo;
        btnAgregarDetalle.disabled = false;
        const almacen_origen_id = $('#almacen_origen').val();

        if (!modelo_id) {
            return;
        }

        if (almacen_origen_id.toString().length === 0) {

            const selectElement = document.getElementById('modelo');
            $(selectElement).select2('destroy');
            selectElement.value = null;
            selectElement.onchange = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR',
                allowClear: true
            });
            selectElement.onchange = function() {
                getProductosByModelo(this.value);
            };

            toastr.error('DEBES SELECCIONAR UN ALMACÉN DE ORIGEN!!!');
            return;
        }

        if (modelo_id) {
            try {
                mostrarAnimacion();
                const url = route('almacenes.nota_salidad.getProductosAlmacen', {
                    modelo_id,
                    almacen_id: almacen_origen_id
                });
                const response = await axios.get(url);

                if (response.data.success) {
                    destruirDataTable(dtNsProductos);
                    limpiarTabla('tabla_ns_productos');
                    pintarTableStocks(response.data.stocks, tallasBD, response.data.producto_colores);
                    dtNsProductos = iniciarDataTable('tabla_ns_productos');
                } else {
                    toastr.error(response.data.message, 'ERROR EN EL SERVIDOR')
                }

            } catch (error) {
                console.error('Error al obtener productos por modelo:', error);
            } finally {
                ocultarAnimacion();
            }
        }
    }


    //======== VALIDAR CANTIDAD DE INPUTS AL ESCRIBIR =========
    async function validarCantidadInstantanea(event) {

        btnAgregarDetalle.disabled = true;
        const cantidadSolicitada = event.target.value;

        try {

            if (cantidadSolicitada !== '') {

                const almacen_id = document.querySelector('#almacen_origen').value;

                if (!almacen_id) {
                    toastr.error('DEBES SELECCIONAR UN ALMACÉN DE ORIGEN!!!');
                    return;
                }

                const stock_logico = await this.getStockLogico(event.target, almacen_id);

                if (stock_logico < cantidadSolicitada) {
                    event.target.classList.add('inputCantidadIncorrecto');
                    event.target.classList.remove('inputCantidadValido');
                    event.target.focus();

                    event.target.value = stock_logico;
                    toastr.error(`Cantidad de salida: ${cantidadSolicitada}, debe ser menor o igual
                        al stock : ${stock_logico}`, "Error");
                } else {
                    event.target.classList.add('inputCantidadValido');
                    event.target.classList.remove('inputCantidadIncorrecto');
                }
            } else {
                event.target.classList.remove('inputCantidadIncorrecto');
                event.target.classList.remove('inputCantidadValido');
            }
        } catch (error) {
            toastr.error(`El producto no cuenta con registros en esa talla`, "Error");
            event.target.value = '';
            console.error('Error al obtener stock logico:', error);
        } finally {
            btnAgregarDetalle.disabled = false;
        }
    }



    //=============== Da forma de objeto a los datos obtenidos de la tabla salida =============
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

    //=========== OBTENER STOCK LOGICO DESDE LA BD =======
    async function getStockLogico(inputCantidad, almacen_id) {


        const producto_id = inputCantidad.getAttribute('data-producto-id');
        const color_id = inputCantidad.getAttribute('data-color-id');
        const talla_id = inputCantidad.getAttribute('data-talla-id');

        try {
            const url = route('almacenes.nota_salidad.getStock', {
                almacen_id,
                producto_id,
                color_id,
                talla_id
            });
            const response = await axios.get(url);

            if (response.data.success) {
                const stock = response.data.stock_logico;
                return stock;
            } else {
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR AL OBTENER STOCK');
                return null;
            }

        } catch (error) {
            toastr.error(error, "ERROR EN LA PETICIÓN OBTENER STOCK");
            event.target.value = '';
            return null;
        }
    }

    //========= PINTAR TABLA STOCKS ==========
    const pintarTableStocks = (stocks, tallas, producto_colores) => {
        let options = ``;
        console.log(stocks);
        producto_colores.forEach((pc) => {
            options += `  <tr>
                            <th scope="row"  data-color=${pc.color_id} >
                                ${pc.color_nombre}
                            </th>
                            <th scope="row" data-producto=${pc.producto_id}  >
                                ${pc.producto_nombre}
                            </th>
                        `;

            let htmlTallas = ``;

            tallas.forEach((t) => {
                const stock = stocks.filter(st => st.producto_id == pc.producto_id && st.color_id ==
                    pc.color_id && st.talla_id == t.id)[0]?.stock || 0;

                htmlTallas += `
                                    <td style="background-color: rgb(210, 242, 242);">${stock}</td>
                                    <td width="8%">
                                        ${stock > 0 ? `
                                            <input type="text" class="form-control inputCantidad"
                                            id="inputCantidad_${pc.producto_id}_${pc.color_id}_${t.id}"
                                            data-producto-id="${pc.producto_id}"
                                            data-producto-nombre="${pc.producto_nombre}"
                                            data-color-nombre="${pc.color_nombre}"
                                            data-talla-nombre="${t.descripcion}"
                                            data-color-id="${pc.color_id}" data-talla-id="${t.id}"
                                            data-lote-id="${t.id}"></input>
                                        ` : ''}
                                    </td>
                                `;
            })



            htmlTallas += `</tr>`;
            options += htmlTallas;
        })

        tableStocksBody.innerHTML = options;
        //btnAgregarDetalle.disabled = false;
    }

    function cambiarAlmacen(selectAlmacen) {


        toastr.clear();
        const almacen_id = selectAlmacen.getAttribute('id');
        const almacen_origen_id = $('#almacen_origen').val();
        const almacen_destino_id = $('#almacen_destino').val();

        //========= SI EL ALMACÉN ORIGEN CAMBIAR, LIMPIAR TABLA PRODUCTOS Y DETALLE =======
        if (almacen_id === 'almacen_origen') {

            //======== LIMPIAR TABLA PRODUCTOS ======
            destruirDataTable(dtNsProductos);
            limpiarTabla('tabla_ns_productos');
            dtNsProductos = iniciarDataTable('tabla_ns_productos');

            //======= LIMPIAR TABLA DETALLE =======
            detallesSalida = [];
            destruirDataTable(dtNsDetalle);
            limpiarTabla('tabla_ns_detalle');
            pintarDetallesSalida(detallesSalida);
            dtNsDetalle = iniciarDataTable('tabla_ns_detalle');

            //======= LIMPIAR SELECT MODELO ========
            const selectElement = document.getElementById('modelo');
            $(selectElement).select2('destroy');
            selectElement.value = null;
            selectElement.onchange = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR',
                allowClear: true
            });
            selectElement.onchange = function() {
                getProductosByModelo(this.value);
            };
        }

        if (!almacen_origen_id && !almacen_destino_id) {
            return;
        }

        if (almacen_origen_id == almacen_destino_id) {

            toastr.error('DEBES SELECCIONAR ALMACENES DIFERENTES!!!');
            const selectElement = document.getElementById(almacen_id);

            $(selectElement).select2('destroy');
            selectElement.value = null;
            selectElement.onchange = null;
            $(selectElement).select2({
                placeholder: 'SELECCIONAR',
                allowClear: true
            });
            selectElement.onchange = function() {
                cambiarAlmacen(this);
            };

            return;
        }

    }

    async function registrarNotaSalida(formData) {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Desea registrar la nota de salida?",
            text: "Se generará una nota de ingreso para el almacén destino!",
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
                    const res = await axios.post(route('almacenes.nota_salidad.store'), formData);

                    if (res.data.success) {
                        window.location = route('almacenes.nota_salidad.index');
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
</script>
@endpush
