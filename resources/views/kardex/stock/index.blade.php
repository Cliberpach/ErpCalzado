@extends('layout')
@section('content')

@section('kardex-active', 'active')
@section('kardex_stock-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Kardex Stock</b></h2>
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
        <div class="col-12 mb-3">
            <div class="row align-items-end">
                <div class="col-12 col-md-4">
                    <label for="modelo" style="font-weight: bold;">MODELO</label>
                    <select name="modelo" id="modelo" class="select2_form form-control">
                        <option value=""></option>
                        @foreach ($modelos as $modelo)
                            <option value="{{ $modelo->id }}">{{ $modelo->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button class="btn btn-success btn-block" onclick="dtKStock.ajax.reload()"><i
                            class="fa fa-refresh"></i></button>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="excelKardexStock()">
                                <i class="fa fa-file-excel-o"></i> EXCEL
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('kardex.stock.tables.tbl_kardex_stock')
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
    let dtKStock = null;
    document.addEventListener('DOMContentLoaded', () => {
        iniciarSelect2();
        iniciarDtKStock();
        events();
    })

    function events() {

    }

    function iniciarDtKStock() {
        dtKStock = $('#tbl_kardex_stock').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 100,
            ajax: {
                url: '{{ route('consultas.kardex.stock.getKStock') }}',
                type: 'GET',
                data: function(d) {
                    d.modelo = $('#modelo').val();
                }
            },
            columns: [{
                    data: 'producto_nombre',
                    name: 'p.nombre'
                },
                {
                    data: 'color_nombre',
                    name: 'c.descripcion'
                },

                @foreach ($tallas as $talla)
                    {
                        data: 'talla_{{ $talla->id }}',
                        name: 'talla_{{ $talla->id }}',
                        className: 'text-center',
                        searchable: false
                    },
                @endforeach
            ],
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros en total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
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

    function excelKardexStock() {
        const url = @json(route('consultas.kardex.stock.excelKardexStock'));

        const params = {
            modelo: document.querySelector('#modelo').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;
    }

</script>
@endpush
