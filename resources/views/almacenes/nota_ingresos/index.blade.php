@extends('layout')
@section('content')

@section('almacenes-active', 'active')
@section('nota_ingreso-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-8">
        <h2 style="text-transform:uppercase"><b>Listado de Notas de Ingreso</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Notas de Ingreso</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a class="btn btn-block btn-w-m btn-primary m-t-md" href="{{ route('almacenes.nota_ingreso.create') }}">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
        <a class="btn btn-block btn-w-m btn-primary m-t-md" id="importar" href="#">
            <i class="fa fa-file-excel-o"></i> Importar
        </a>

    </div>
</div>
@include('almacenes.nota_ingresos.modalfile')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        @include('almacenes.nota_ingresos.tables.tbl_ni_list')
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

    $(document).ready(function() {

        $('.dataTables-errores').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "responsive":true,
            "buttons": [],
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },

            "columnDefs": [{
                    "targets": [0],
                    className: "text-center",
                },
                {
                    "targets": [1],
                    className: "text-center",
                },
                {
                    "targets": [2],
                    className: "text-center",
                },
                {
                    "targets": [3],
                    className: "text-center",
                }
            ],

        });

        //============ DATATABLE NOTA INGRESO =========
        $('.dataTables-ingreso_mercaderia').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "responsive":true,
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'Tablas Generales'
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
            "processing": true,
            "ajax": "{{ route('almacenes.nota_ingreso.data') }}",
            "columns": [
                {
                    data: 'id',
                    className: "text-center"
                },
                {
                    data: 'registrador_nombre',
                    className: "text-center"
                },
                {
                    data: 'created_at',
                    className: "text-center"
                },
                {
                    data: 'almacen_destino_nombre',
                    className: "text-center"
                },
                {
                    data: 'cadena_detalles',
                    className: "text-center"
                },
                {
                    data: 'observacion',
                    className: "text-center"
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {

                        //Ruta Modificar
                        var url_editar = '{{ route('almacenes.nota_ingreso.edit', ':id') }}';
                        url_editar = url_editar.replace(':id', data.id);

                        let url_etiquetas =
                            '{{ route('almacenes.nota_ingreso.generarEtiquetas', ':nota_id') }}';
                        url_etiquetas = url_etiquetas.replace(':nota_id', data.id);

                        return `<div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                               <i class="fa fa-th"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="${url_editar}">
                                    <i class="fa fa-eye"></i> VER
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="${url_etiquetas}" target="_blank" id="adhesivo_${data.id}">
                                    <i class="fa fa-barcode"></i> GENERAR ETIQUETAS
                                </a>
                            </div>
                            </div>`;
                    }
                }

            ],
            "language": {
                "url": "{{ asset('Spanish.json') }}"
            },
            "order": [
                [0, "desc"]
            ],
        });

        $('.dataTables-ingreso_mercaderia').on('init.dt', function() {
            @if (Session::has('generarAdhesivos') && Session::has('nota_id'))
                toastr.success('{{ Session::get('generarAdhesivos') }}', 'CARGANDO');
                const nota_id = '{{ Session::get('nota_id') }}';
                document.querySelector(`#adhesivo_${nota_id}`).click();
            @endif
        });

    });

    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })
</script>
@endpush
