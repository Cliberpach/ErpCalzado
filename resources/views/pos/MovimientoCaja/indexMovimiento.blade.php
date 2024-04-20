@extends('layout') @section('content')
    @include('pos.MovimientoCaja.create')
    @include('pos.MovimientoCaja.cerrar')
    @include('pos.MovimientoCaja.detallesMovimiento')
@section('caja-movimiento-active', 'active')
@section('caja-chica-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>LISTADO DE MOVIMIENTOS DE CAJAS</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Movimiento Caja</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        @can('haveaccess', 'movimiento_caja.create')
            <a class="btn btn-block btn-w-m btn-modal btn-primary m-t-md" href="#">
                <i class="fa fa-plus-square"></i> Añadir nuevo
            </a>
        @endcan

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <input type="hidden" name="" id="filtros" value="INACTIVO">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row form-group">
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
                                    <label for="" class="text-white">Buscar</label>
                                    <button type="button" class="btn btn-block btn-primary" disabled id="reload">
                                        <i class="fa fa-search"></i>
                                        Buscar
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <label for=""><strong>Total Venta:</strong> <span id="totalVenta">S/
                                            0.00</span> </label>
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
                                <table class="table dataTables-cajas table-striped table-bordered table-hover"
                                    style="text-transform:uppercase">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="text-center">CAJA</th>
                                            <th class="text-center">CANTIDAD INICIAL</th>
                                            <th class="text-center">FECHA APERTURA</th>
                                            <th class="text-center">FECHA CIERRE</th>
                                            <th class="text-center">CANTIDAD CIERRE</th>
                                            <th class="text-center">VENTA DEL DIA</th>
                                            <th class="text-center">ACCIONES</th>

                                         </tr>
                                    </thead>

                                </table>
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
<!-- DataTable -->
<link href="{{ asset('Inspinia/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<style>
    .my-swal {
        z-index: 3000 !important;
    }
</style>
@endpush
@push('scripts')
<!-- DataTable -->
<script src="{{ asset('Inspinia/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>


<script>
    var detalles_colaborades = document.getElementById("modal_detalles_colaboradores");
    var cuerpo_colaborades = document.querySelector('#modal_detalles_colaboradores table tbody');
    let btnEnviar = document.getElementById('btnEnviarAperturaCaja');

    function verificarUsuariosVentas(event){
        event.preventDefault();
        let cant= document.querySelectorAll('input[name="usuarioVentas[]"]');
        let form= document.getElementById('crear_caja_movimiento');
        if(cant.length>0){
            form.submit();
        }else{
            alert('Seleccione al menos un usuairo de ventas para la apertura de caja');
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
        let btnColab= document.getElementById('btnRetirarColaboradores');

        let body = '';
        if (datos.length>0) {
            btnColab.style.display='block';
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
                btnColab.style.display='none';
                body = '<tr> <th colspan="3" class="text-center"> Sin colaborades disponibles </th>  </tr>';
        }

        cuerpo_colaborades.innerHTML = body;

        $(detalles_colaborades).modal("show");
    }



    $(function() {
        var table = $('.dataTables-cajas').DataTable({
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
                            ")' title='Pdf'><i class='fa fa-file-pdf-o'></i></a></div>";
                        if (data.fecha_Cierre == "-") {
                            html = `<div class='btn-group'>
                                <button class='btn btn-warning btn-sm' onclick='cerrarCaja(${data.id})' title='Modificar'><i class='fa fa-lock'> Close</i></button>
                                <button class='btn btn-danger btn-sm'  onclick='reporte(${data.id})' title='Pdf'><i class='fa fa-file-pdf-o'></i></button>
                                <button class='btn btn-block btn-sm btn-modal btn-primary' id='btn_mostrar_colaborades_${data.id}'  data_id=${data.id}  onclick='mostrarColaboradores(${data.id})'>Detalles</button>
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
            table.draw();
        });
    });

    function reporte(id) {
        var url = "{{ route('Caja.reporte.movimiento', ':id') }}"
        url = url.replace(':id', id);
        window.open(url, "REPORTE CAJA", "width=900, height=600")
    }

    function cerrarCaja(id) {
        axios.get("{{ route('Caja.datos.cierre') }}", {
            params: {
                id: id
            }
        }).then((value) => {
            var datos = value.data;
            $("#modal_cerrar_caja #movimiento_id").val(id);
            $("#modal_cerrar_caja #caja").val(datos.caja);
            $("#modal_cerrar_caja #colaborador").val(datos.colaborador);
            $("#modal_cerrar_caja #monto_inicial").val(datos.monto_inicial);
            $("#modal_cerrar_caja #ingreso").val(convertFloat(datos.ingresos).toFixed(2));
            $("#modal_cerrar_caja #egreso").val(convertFloat(datos.egresos).toFixed(2));
            $("#modal_cerrar_caja #saldo").val(convertFloat(datos.saldo).toFixed(2));
            $("#modal_cerrar_caja").modal("show");
        }).catch((value) => {})
    }

    $(".btn-modal").click(function(e) {
        e.preventDefault();
        $("#modal_crear_caja").modal("show");
    });



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
