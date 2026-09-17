@extends('layout')

@section('caja-movimiento-active', 'active')
@section('caja-chica-active', 'active')

@section('bread-module', 'Caja')
@section('bread-submodule', 'Apertura caja')
@section('hero-title', 'Lista de Aperturas Caja')
@section('hero-subtitle', 'Caja')

@section('btn-add')
    <a class="main-btn-add" href="#" onclick="openMdlAbrirCaja()">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    @include('pos.MovimientoCaja.modals.mdl_abrir_caja')
    @include('pos.MovimientoCaja.modals.mdl_cerrar_caja')
    @include('pos.MovimientoCaja.modals.mdl_docs_no_pagados')
    @include('pos.MovimientoCaja.modals.mdl_estado_cajas')
    @include('pos.MovimientoCaja.detallesMovimiento')


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <input type="hidden" name="" id="filtros" value="INACTIVO">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row form-group align-items-end">
                                    <div class="col-md-2 filtro_inactivo">
                                        <label for="">Mes</label>
                                        <select name="mes" id="mes" class="custom-select">
                                            <option value="01" {{ $mes == '01' ? 'selected' : '' }}>ENERO</option>
                                            <option value="02" {{ $mes == '02' ? 'selected' : '' }}>FEBRERO</option>
                                            <option value="03" {{ $mes == '03' ? 'selected' : '' }}>MARZO</option>
                                            <option value="04" {{ $mes == '04' ? 'selected' : '' }}>ABRIL</option>
                                            <option value="05" {{ $mes == '05' ? 'selected' : '' }}>MAYO</option>
                                            <option value="06" {{ $mes == '06' ? 'selected' : '' }}>JUNIO</option>
                                            <option value="07" {{ $mes == '07' ? 'selected' : '' }}>JULIO</option>
                                            <option value="08" {{ $mes == '08' ? 'selected' : '' }}>AGOSTO</option>
                                            <option value="09" {{ $mes == '09' ? 'selected' : '' }}>SEPTIEMBRE</option>
                                            <option value="10" {{ $mes == '10' ? 'selected' : '' }}>OCTUBRE</option>
                                            <option value="11" {{ $mes == '11' ? 'selected' : '' }}>NOVIEMBRE</option>
                                            <option value="12" {{ $mes == '12' ? 'selected' : '' }}>DICIEMBRE</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 filtro_inactivo">
                                        <label for="">Año</label>
                                        <select name="anio" id="anio" class="custom-select">
                                            @foreach ($lstAnios as $anio)
                                                <option value="{{ $anio->value }}"
                                                    {{ $anio_ == $anio->value ? 'selected' : '' }}>{{ $anio->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 filtro_activo d-none">
                                        <label for="">Desde:</label>
                                        <input type="date" id="desde" class="form-control"
                                            value="{{ FechaActual() }}">
                                    </div>
                                    <div class="col-md-2 filtro_activo d-none">
                                        <label for="">Hasta:</label>
                                        <input type="date" id="hasta" class="form-control"
                                            value="{{ FechaActual() }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Caja</label>
                                        <select id="filter_caja" class="form-control" style="width:100%;">
                                            <option value="">Todas</option>
                                            @foreach ($lstCajas as $caja)
                                                <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="text-white">Buscar</label>
                                        <button type="button" class="btn btn-block btn-primary" disabled id="reload">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="text-white">.</label>
                                        <button type="button" class="btn btn-block btn-info btn-sm"
                                            onclick="abrirModalEstadoCajas()" title="Ver estado actual de las cajas">
                                            <i class="fas fa-store-alt mr-1"></i> Ver estado cajas
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <label for=""><strong>Total Venta:</strong></label>
                                        <div><span id="totalVenta" class="font-weight-bold">S/ 0.00</span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="" class="mb-0" style="cursor: pointer;"
                                            onclick="FiltrarPorFecha()">
                                            <strong>
                                                <span id="textFilter">Filtrar por fechas</span>
                                                <i class="fa fa-filter"></i>
                                            </strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    @include('pos.MovimientoCaja.tables.tbl_list_movimientos_caja')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .my-swal {
            z-index: 3000 !important;
        }

        /* El menú ⋮ se cuelga del <body> mientras está abierto (ver
           flotarMenuAcciones). Queda por debajo de los modales, que usan 1050. */
        .menu-reportes-caja {
            z-index: 1040;
            min-width: 180px;
            text-transform: none;
            text-align: left;
        }

        /* Estado flotante: el menú está colgado del <body>. Hay que anular
           .dropdown-menu-right (right:0): fuera del .btn-group se resuelve
           contra el viewport y, junto al left que pone el JS, estiraba el menú
           de un borde al otro de la pantalla. También el float y el margin que
           trae .dropdown-menu. El JS sólo escribe top/left; el ancho lo decide
           el contenido, entre min-width y max-width. */
        .menu-reportes-caja.menu-flotante {
            position: fixed;
            right: auto;
            bottom: auto;
            float: none;
            width: auto;
            max-width: 280px;
            margin: 0;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ mix('js/tomselect.js') }}"></script>
    <script>
        var detalles_colaborades = document.getElementById("modal_detalles_colaboradores");
        var cuerpo_colaborades = document.querySelector('#modal_detalles_colaboradores table tbody');
        let btnEnviar = document.getElementById('btnEnviarAperturaCaja');

        let dtMovimientoCajas = null;

        document.addEventListener('DOMContentLoaded', () => {
            iniciarDataTableMovimientos();
            flotarMenuAcciones();
            iniciarSelect2();
            events();
        })


        function events() {
            eventsMdlAbrirCaja();
            eventsCerrarCaja();
        }

        // La tabla vive dentro de .table-responsive (overflow) y DataTables mete
        // ahí un contenedor posicionado, así que el menú quedaba recortado y la
        // tabla sacaba scroll. Mientras está abierto se cuelga del <body> con
        // position:fixed y al cerrarse vuelve a su sitio: flota por encima sin
        // tocar el tamaño de la tabla.
        function flotarMenuAcciones() {
            var SEL_GRUPO = '.dataTables-cajas .btn-group';
            var $menuFlotante = null;

            // Borde izquierdo mínimo: el área de contenido, para no invadir
            // nunca el menú lateral.
            function limiteIzquierdo() {
                var wrapper = document.getElementById('page-wrapper');
                var min = wrapper ? wrapper.getBoundingClientRect().left : 0;
                return Math.max(min, 0) + 4;
            }

            function colocar($btn, $menu) {
                var r = $btn[0].getBoundingClientRect();
                // El menú ya está en el DOM y es medible (display:block +
                // visibility:hidden), así que outerWidth/Height son reales.
                var ancho = $menu.outerWidth(),
                    alto = $menu.outerHeight();
                var vh = window.innerHeight,
                    vw = window.innerWidth,
                    minLeft = limiteIzquierdo(),
                    sep = 2;

                var top = r.bottom + sep;
                if (top + alto > vh - 4) {
                    top = r.top - alto - sep; // no entra debajo: se abre hacia arriba
                }
                if (top < 4) {
                    top = 4;
                }

                var left = r.right - ancho; // alineado a la derecha del botón
                if (left + ancho > vw - 4) {
                    left = vw - ancho - 4;
                }
                if (left < minLeft) {
                    left = minLeft;
                }

                $menu.css({
                    top: Math.round(top) + 'px',
                    left: Math.round(left) + 'px'
                });
            }

            $(document)
                .on('show.bs.dropdown', SEL_GRUPO, function() {
                    var $menu = $(this).children('.dropdown-menu');
                    if (!$menu.length) return;
                    $menuFlotante = $menu;
                    // Se cuelga del <body> y se hace medible sin que se vea:
                    // display:block da tamaño real, visibility:hidden evita el
                    // parpadeo en la esquina antes de posicionarlo.
                    $menu.data('grupoOrigen', this)
                        .appendTo(document.body)
                        .addClass('menu-flotante')
                        .css({
                            top: '0px',
                            left: '0px',
                            display: 'block',
                            visibility: 'hidden'
                        });
                    colocar($(this).find('[data-toggle="dropdown"]'), $menu);
                })
                .on('shown.bs.dropdown', SEL_GRUPO, function() {
                    if (!$menuFlotante) return;
                    // Ya posicionado: se muestra. Se recoloca por si el ancho
                    // cambió al aplicarse la clase .show.
                    colocar($(this).find('[data-toggle="dropdown"]'), $menuFlotante);
                    $menuFlotante.css('visibility', 'visible');
                })
                .on('hidden.bs.dropdown', SEL_GRUPO, function() {
                    if (!$menuFlotante) return;
                    $menuFlotante.removeAttr('style')
                        .removeClass('menu-flotante')
                        .appendTo($menuFlotante.data('grupoOrigen'));
                    $menuFlotante = null;
                });

            // Al scrollear o redimensionar el menú sigue al botón; si el botón
            // deja de verse, se cierra.
            $(window).on('scroll resize', function() {
                if (!$menuFlotante) return;
                var $btn = $($menuFlotante.data('grupoOrigen')).find('[data-toggle="dropdown"]');
                if (!$btn.length) return;
                var r = $btn[0].getBoundingClientRect();
                if (r.bottom < 0 || r.top > window.innerHeight) {
                    $btn.dropdown('toggle');
                    return;
                }
                colocar($btn, $menuFlotante);
            });
        }

        function iniciarDataTableMovimientos() {
            dtMovimientoCajas = $('.dataTables-cajas').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [{
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'MOVIMIENTOS DE CAJAS'
                    },
                    {
                        titleAttr: 'Imprimir',
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Imprimir',
                        customize: function(win) {
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ],
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    type: "GET",
                    url: '{{ route('Caja.get_movimientos_cajas') }}',
                    dataType: 'json',
                    data: function(d) {
                        $("#reload").prop("disabled", true);
                        d.mes = $("#mes").val();
                        d.anio = $("#anio").val();
                        d.filter = $("#filtros").val();
                        d.desde = $("#desde").val();
                        d.hasta = $("#hasta").val();
                        d.caja_id = window.filterCajaSelect ? window.filterCajaSelect.getValue() : $("#filter_caja").val();
                    }
                },
                "columns": [
                    //Caja chica
                    {
                        data: 'id',
                        className: "text-center",
                        "visible": false
                    },
                    {
                        data: 'caja',
                        className: "text-center"
                    },
                    {
                        data: 'colaborador_nombre',
                        className: "text-center"
                    },
                    {
                        data: 'sede_nombre',
                        className: "text-center"
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            const {
                                cantidad_inicial
                            } = data;
                            let formato = formatoMoneda(cantidad_inicial);
                            return formato;
                        }
                    },

                    {
                        data: 'fecha_Inicio',
                        className: "text-center"
                    },
                    {
                        data: 'fecha_Cierre',
                        className: "text-center"
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            const {
                                cantidad_final
                            } = data;
                            if (!isNaN(Number(cantidad_final))) {
                                let formato = formatoMoneda(cantidad_final);
                                return formato;
                            } else {
                                return cantidad_final;
                            }

                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            const {
                                totales
                            } = data;
                            const {
                                TotalVentaDelDia
                            } = totales;
                            let formato = formatoMoneda(TotalVentaDelDia);
                            return formato;
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        "render": function(data, type, row, meta) {
                            var html = `<div class='btn-group'>
                                <a class='btn btn-primary btn-sm' href='#' title='Caja Cerrada'><i class='fa fa-check'> Caja Cerrada</i></a>
                                ${menuReportes(data)}
                                </div>`;
                            if (data.fecha_Cierre == "-") {
                                html = `<div class='btn-group'>
                                <button class='btn btn-warning btn-sm' onclick='cerrarCaja(${data.id})' title='Modificar'><i class='fa fa-lock'> Close</i></button>
                                ${menuReportes(data)}
                                <button class='btn btn-block btn-sm btn-primary' id='btn_mostrar_colaborades_${data.id}'  data_id=${data.id}  onclick='mostrarColaboradores(${data.id})'>Detalles</button>
                                </div>
                                `
                            }
                            return html;
                        }
                    }
                ],
                "language": {
                    "url": "{{ asset('Spanish.json') }}"
                },
                "order": [
                    [0, "desc"]
                ],
            }).on("draw", function() {
                $("#reload").prop("disabled", false);
                let tabla = $('.dataTables-cajas').DataTable();
                let _TotalVentaDelDia = 0.00;
                tabla.rows().data().each((el, index) => {
                    const {
                        totales
                    } = el;
                    const {
                        TotalVentaDelDia
                    } = totales;
                    _TotalVentaDelDia = _TotalVentaDelDia + TotalVentaDelDia;
                });
                $("#totalVenta").text(formatoMoneda(_TotalVentaDelDia));
            });

            $(document).on("click", "#reload", function() {
                dtMovimientoCajas.draw();
            });
        }

        function iniciarSelect2() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            const cajaEl = document.getElementById('filter_caja');
            if (cajaEl && !cajaEl.tomselect) {
                window.filterCajaSelect = new TomSelect(cajaEl, {
                    create: false,
                    allowEmptyOption: true,
                    placeholder: 'Todas las cajas',
                    plugins: ['clear_button'],
                    onChange: function() { dtMovimientoCajas && dtMovimientoCajas.draw(); }
                });
            }
        }


        function verificarSeleccion(id) {
            let verificar = document.getElementById(`checkBox${id}`);

            if (verificar.checked) {

                // Se agregara el atributo name para que  se guarde ese dato
                document.getElementById(`idUsuario${id}`).setAttribute('name', 'usuarioVentas[]');

            } else {
                // Se quitara el atributo name para que no se guarde ese dato
                document.getElementById(`idUsuario${id}`).removeAttribute('name');
            }


        }

        // Obtiene los datos de los colabores presentes en cada apertura de caja
        function mostrarColaboradores(id) {
            let url = '/get-colaborades/' + id;
            fetch(url)
                .then(response => response.json())
                .then(data => mostrarData(data))
                .catch(error => {
                    console.log('error al obtener los datos', error);
                });
        }

        // rellena la tabla que muestra los colabores que participan en la apertura de caja
        function mostrarData(datos) {
            let btnColab = document.getElementById('btnRetirarColaboradores');

            let body = '';
            if (datos.length > 0) {
                btnColab.style.display = 'block';
                datos.forEach(element => {
                    body += `<tr>
                <th>
                    <div class="m-auto p-auto">
                    <input type="checkbox" class="btn-check" id="checkBox${element.usuario_id}" onclick="verificarSeleccion(${element.usuario_id})">
                    <input type="hidden" id='idUsuario${element.usuario_id}' value="${element.usuario_id}">
                    <input type="hidden" name="movimiento" value="${element.movimiento_id}">
                    </div>

                </th>
                <th>
                    ${element.usuario}
                </th>
                <th>
                    ${element.fecha_entrada}
                </th>
                </tr>`
                });

            } else {
                btnColab.style.display = 'none';
                body = '<tr> <th colspan="3" class="text-center"> Sin colaborades disponibles </th>  </tr>';
            }

            cuerpo_colaborades.innerHTML = body;

            $(detalles_colaborades).modal("show");
        }


        // El nombre viaja también como último tramo de la URL: el visor de
        // Adobe Acrobat ignora el Content-Disposition y toma de ahí el nombre
        // al guardar. El servidor lo manda ya armado en la fila.
        function urlConNombre(plantilla, id, nombre) {
            var url = plantilla.replace(':id', id);
            return nombre ? url + '/' + nombre + '.pdf' : url;
        }

        function reporte(id, nombre) {
            var url = urlConNombre("{{ route('Caja.reporte.movimiento', ':id') }}", id, nombre);
            window.open(url, "REPORTE CAJA", "width=900, height=600")
        }

        function reporteCantidades(id, nombre) {
            var url = urlConNombre("{{ route('Caja.reporte.productos', ':id') }}", id, nombre);
            window.open(url, "REPORTE CANTIDADES CAJA", "width=900, height=600")
        }

        // Menú ⋮ de la columna ACCIONES. Sólo lleva reportes: los botones que
        // ejecutan algo (cerrar caja, detalles) siguen visibles en la fila.
        // data-display="static" desactiva Popper: el menú se saca al <body> y se
        // posiciona a mano (ver flotarMenuAcciones más abajo).
        function menuReportes(fila) {
            const id = fila.id;
            return `<button type='button' class='btn btn-secondary btn-sm'
                        data-toggle='dropdown' data-display='static'
                        aria-haspopup='true' aria-expanded='false' title='Más opciones'>
                        <i class='fas fa-ellipsis-v'></i>
                    </button>
                    <ul class='dropdown-menu dropdown-menu-right menu-reportes-caja'>
                        <li>
                            <a class='dropdown-item' href='#' onclick='reporte(${id}, "${fila.nombre_dinero}"); return false;'>
                                <i class='fas fa-chart-bar text-danger mr-2'></i> Reporte Dinero
                            </a>
                        </li>
                        <li>
                            <a class='dropdown-item' href='#' onclick='reporteCantidades(${id}, "${fila.nombre_cantidades}"); return false;'>
                                <i class='fas fa-clipboard-list text-success mr-2'></i> Reporte Cantidades
                            </a>
                        </li>
                    </ul>`;
        }

        //========= TRAER DATOS DEL MOVIMIENTO CAJA Y ABRIR MODAL CERRAR CAJA =======
        async function cerrarCaja(id) {
            if (!id) return;

            mostrarAnimacion();
            const validado = await validarVentasNoPagadas(id);
            ocultarAnimacion();
            if (!validado) return;

            mostrarAnimacion();
            axios.get("{{ route('Caja.datos.cierre') }}", { params: { id } })
                .then(function(res) {
                    $('#movimiento_id').val(id);
                    poblarModalCierre(res.data);
                })
                .catch(function() {})
                .finally(function() { ocultarAnimacion(); });
        }

        async function validarVentasNoPagadas(movimiento_id) {
            try {
                const res = await axios.get(
                    '{{ route('caja.movimiento.verificarVentasNoPagadas', ['movimiento_id' => ':movimiento_id']) }}'
                    .replace(':movimiento_id', movimiento_id)
                );
                if (!res.data.success) {
                    toastr.error(res.data.exception, res.data.message);
                    return false;
                }
                if (res.data.docs_no_pagados.length > 0) {
                    poblarModalDocNoPagados(res.data.docs_no_pagados);
                    return false;
                }
                return true;
            } catch (e) {
                toastr.error('Error al validar documentos pendientes');
                return false;
            }
        }

        function formatoMoneda(monto) {
            let res = new Intl.NumberFormat("es-PE", {
                    style: 'currency',
                    currency: "PEN"
                })
                .format(monto);
            return res;
        }

        function FiltrarPorFecha() {
            let filtros = $("#filtros").val();
            if (filtros == "INACTIVO") {
                $(".filtro_inactivo").addClass("d-none");
                $(".filtro_activo").removeClass("d-none");
                $("#filtros").val("ACTIVO");
                $("#textFilter").text("Ocultar filtros");
            } else {
                $(".filtro_inactivo").removeClass("d-none");
                $(".filtro_activo").addClass("d-none");
                $("#filtros").val("INACTIVO");
                $("#textFilter").text("Filtrar por fechas");
            }
        }
    </script>
@endpush
