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
            iniciarSelect2();
            events();
        })


        function events() {
            eventsMdlAbrirCaja();
            eventsCerrarCaja();
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
                            var html =
                                "<div class='btn-group'><a class='btn btn-primary btn-sm' href='#' title='Caja Cerrada'><i class='fa fa-check'> Caja Cerrada</i></a><a class='btn btn-danger btn-sm' href='#'  onclick='reporte(" +
                                data.id +
                                ")' title='Pdf'><i class='fas fa-file-pdf'></i></a></div>";
                            if (data.fecha_Cierre == "-") {
                                html = `<div class='btn-group'>
                                <button class='btn btn-warning btn-sm' onclick='cerrarCaja(${data.id})' title='Modificar'><i class='fa fa-lock'> Close</i></button>
                                <button class='btn btn-danger btn-sm'  onclick='reporte(${data.id})' title='Pdf'><i class='fas fa-file-pdf'></i></button>
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


        function reporte(id) {
            var url = "{{ route('Caja.reporte.movimiento', ':id') }}"
            url = url.replace(':id', id);
            window.open(url, "REPORTE CAJA", "width=900, height=600")
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
