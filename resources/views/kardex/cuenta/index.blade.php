@extends('layout')
@section('content')

@section('kardex-active', 'active')
@section('kardex_cuenta-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Kardex Cuentas</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Kardex Stock</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                    <label for="cuenta_bancaria_id" style="font-weight:bold;">CUENTA BANCARIA</label>
                                    <select data-placeholder="Seleccionar" id="cuenta_bancaria_id"
                                        class="form-select select2_form" aria-label="Default select example">
                                        <option value=""></option>
                                        @foreach ($cuentas_bancarias as $cuenta_bancaria)
                                            <option value="{{ $cuenta_bancaria->id }}">
                                                {{ $cuenta_bancaria->banco_nombre . ':' . $cuenta_bancaria->nro_cuenta }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                                    <label for="fecha_inicio" style="font-weight:bold;">FECHA INICIO</label>
                                    <input type="date" class="form-control" id="fecha_inicio">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                                    <label for="fecha_fin" style="font-weight:bold;">FECHA FIN</label>
                                    <input type="date" class="form-control" id="fecha_fin">
                                </div>
                                <div class="col-lg-5 col-md-3 col-sm-12 col-xs-12"
                                    style="text-align: end;margin-top:auto;">
                                    <button class="btn btn-success btnFiltrar"><i class="fas fa-search"></i>
                                        FILTRAR</button>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <label for="ingresos" style="font-weight: bold;">INGRESOS</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fas fa-hand-holding-usd color-success-icon"></i>
                                        </span>
                                        <input readonly type="text" id="ingresos" class="form-control colorVerde"
                                            placeholder="00.00" aria-label="Ingresos" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <label for="egresos" style="font-weight: bold;">EGRESOS</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fas fa-file-invoice-dollar color-success-icon"></i>
                                        </span>
                                        <input readonly id="egresos" type="text" class="form-control colorVerde"
                                            placeholder="00.00" aria-label="Egresos" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                    <label for="saldo" style="font-weight: bold;">SALDO</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class="fas fa-chart-line color-success-icon"></i>
                                        </span>
                                        <input readonly id="saldo" type="text" class="form-control colorVerde"
                                            placeholder="00.00" aria-label="Saldo" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12" style="display:flex;justify-content:end;">
                                    <!-- Botón Excel -->
                                    <button class="btn btn-success" style="margin-right: 10px;"
                                        onclick="downloadExcel();">
                                        <i class="fas fa-file-excel"></i> EXCEL
                                    </button>

                                    <!-- Botón PDF -->
                                    <button class="btn btn-danger" onclick="downloadPdf()">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                @include('kardex.cuenta.tables.tbl_kcuenta_list')
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
@endpush

@push('scripts')
<script>
    let dtKardex = null;

    document.addEventListener('DOMContentLoaded', () => {
        iniciarDataTableKardex();
        iniciarSelect2();
        events();
    })

    function events() {

        document.addEventListener('click', (event) => {

            if (event.target.closest('.btnFiltrar')) {
                filtrar();
            }
        });

    }

    function iniciarDataTableKardex() {
        const urlGetKardex = '{{ route('consultas.kardex.cuenta.getKCuenta') }}';

        dtKardex = new DataTable('#tbl_kcuenta_list', {
            serverSide: true,
            processing: true,
            ajax: {
                url: urlGetKardex,
                type: 'GET',
                data: function(d) {
                    d.cuenta_bancaria_id = document.querySelector('#cuenta_bancaria_id').value;
                    d.fecha_inicio = document.querySelector('#fecha_inicio').value;
                    d.fecha_fin = document.querySelector('#fecha_fin').value;
                },
                dataSrc: function(data) {
                    document.querySelector('#ingresos').value = formatoMoneda(data.total_ingresos);
                    document.querySelector('#egresos').value = formatoMoneda(data.total_egresos);
                    document.querySelector('#saldo').value = formatoMoneda(data.saldo);
                    return data.data;
                }
            },
            "order": [
                [1, 'desc'],
                [0, 'desc']
            ],
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'fecha_registro',
                    name: 'fecha_registro'
                },
                {
                    data: null,
                    name: 'cuenta_bancaria',
                    render: function(data, type, row) {
                        return `${row.banco_abreviatura} : ${row.nro_cuenta}`;
                    }
                },
                {
                    data: 'metodo_pago_nombre',
                    name: 'metodo_pago_nombre'
                },
                {
                    data: 'tipo_documento',
                    name: 'tipo_documento'
                },
                {
                    data: 'documento',
                    name: 'documento'
                },
                {
                    data: 'stock_previo',
                    name: 'stock_previo',
                    visible: false,
                    className: "text-end",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'entrada',
                    name: 'entrada',
                    className: "text-end",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'salida',
                    name: 'salida',
                    className: "text-end",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'stock_posterior',
                    name: 'stock_posterior',
                    className: "text-end",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'registrador_nombre',
                    name: 'registrador_nombre'
                },
            ],
            language: {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    function iniciarSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            width: '100%',
        });
    }

    function filterDataTable() {
        dtKardex.ajax.reload();
    }


    function downloadExcel() {

        toastr.clear();
        const res = validarFiltros();
        if (!res) return;

        const url = @json(route('consultas.kardex.cuenta.excel'));

        const params = {
            cuenta_bancaria_id: document.querySelector('#cuenta_bancaria_id').value,
            fecha_inicio: document.querySelector('#fecha_inicio').value,
            fecha_fin: document.querySelector('#fecha_fin').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }

    function downloadPdf() {

        toastr.clear();
        const res = validarFiltros();
        if (!res) return;

        const url = @json(route('consultas.kardex.cuenta.pdf'));

        const params = {
            cuenta_bancaria_id: document.querySelector('#cuenta_bancaria_id').value,
            fecha_inicio: document.querySelector('#fecha_inicio').value,
            fecha_fin: document.querySelector('#fecha_fin').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.open(finalUrl, '_blank');

    }

    function validarFiltros() {
        const cuenta_bancaria_id = document.querySelector('#cuenta_bancaria_id').value;
        if (!cuenta_bancaria_id) {
            toastr.error('SELECCIONE UNA CUENTA BANCARIA');
            $('#cuenta_bancaria_id').select2('open');
            return false;
        }
        return true;
    }

    function filtrar() {

        toastr.clear();
        const res = validarFiltros();
        if (!res) return;

        const fecha_inicio = document.querySelector('#fecha_inicio').value;
        const fecha_fin = document.querySelector('#fecha_fin').value;

        if (fecha_inicio > fecha_fin && fecha_fin && fecha_inicio) {
            toastr.error('LA FECHA DE INICIO DEBE SER MENOR IGUAL A LA FECHA FINAL!!');
            document.querySelector('#fecha_inicio').focus();
            return;
        }

        dtKardex.ajax.reload();

    }
</script>
@endpush
