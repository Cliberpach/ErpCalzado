@extends('layout') @section('content')

@section('compras-active', 'active')
@section('documento-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-12 col-md-8">
       <h2  style="text-transform:uppercase"><b>Listado de Notas de Crédito Compras</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Notas de crédito</strong>
            </li>
        </ol>
    </div>
    <div class="col-12 col-md-2">
       @if(docCompraValido($documento->id))
        <a href="{{ route('compras.notas.create', array('documento_id' => $documento->id, 'nota' => '0')) }}" class="btn btn-block btn-w-m btn-primary m-t-md">
            <i class="fa fa-plus-square"></i> Nota de crédito
        </a>
       @endif
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-12 m-t">
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-12">
                        <p style="text-transform:uppercase"><strong><i class="fa fa-caret-right"></i> Información del documento de compra:</strong></p>
                    </div>
                </div>
                <div class="row"  style="text-transform:uppercase">
                    <div class="col-md-6 b-r">
                        <div class="row">
                            <div class="col-md-6">
                                <label><strong>Tipo de compra: </strong></label>
                                <p class="text-navy">{{ $documento->tipo_compra }}</p>
                            </div>
                            <div class="col-md-6">
                                <label><strong>Código: </strong></label>
                                <p class="">{{ $documento->serie_tipo.'-'.$documento->numero_tipo }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Proveedor: </strong></label>
                            <p>{{ $documento->proveedor->descripcion }}</p>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><strong>{{ $documento->proveedor->tipo_documento }}</strong></label>
                                    <p>{{ $documento->proveedor->dni != "" ? $documento->proveedor->dni : $documento->proveedor->ruc}}</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label><strong>Dirección: </strong></label>
                                    <p>{{ $documento->proveedor->direccion }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label><strong>Sub Total {{ $documento->moneda == "SOLES" ? "S/" : "$/" }}: </strong></label>
                                <p>{{ number_format($documento->sub_total, 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <label><strong>Igv {{$documento->igv}}%  {{ $documento->moneda == "SOLES" ? "S/" : "$/" }}</strong></label>
                                <p>{{ number_format($documento->total_igv, 2) }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Total {{ $documento->moneda == "SOLES" ? "S/" : "$/" }}: </strong></label>
                            <p>{{ number_format($documento->total, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="row d-none">
                    <div class="col-12 col-md-4">
                        {{
                            $miQr = QrCode::
                                  // format('png')
                                  size(200)  //defino el tamaño
                                  ->backgroundColor(0, 0, 0) //defino el fondo
                                  ->color(255, 255, 255)
                                  ->margin(1)  //defino el margen
                                  ->generate((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
            "https" : "http") . "://" . $_SERVER['HTTP_HOST']."/ventas/documentos/comprobante/".$documento->id.'-100') /** genero el codigo qr **/
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-notas table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th style="display:none;"></th>
                                    <th class="text-center">DOC. AFECTADO</th>
                                    <th class="text-center">FECHA EMISION</th>
                                    <th class="text-center">N°</th>
                                    <th class="text-center">PROVEEDOR</th>
                                    <th class="text-center">MONTO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
    $(document).ready(function() {

        var url = '{{ route("compras.getNotes", ":id")}}';
        url = url.replace(':id', '{{ $documento->id }}');
        // DataTables
        $('.dataTables-notas').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
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
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": false,
            "processing": true,
            "ajax": url,
            "columns": [
                //NOTAS
                {
                    data: 'id',
                    className: "text-center",
                    visible: false
                },

                {
                    data: 'documento_afectado',
                    className: "text-center"
                },

                {
                    data: 'fecha_emision',
                    className: "text-center"
                },
                {
                    data: 'numero',
                    className: "text-center",
                },
                {
                    data: 'cliente',
                    className: "text-left"
                },
                {
                    data: 'monto',
                    className: "text-center"
                },
                {
                    data: null,
                    className: "text-center",
                    render: function(data) {
                        //Ruta Detalle

                        let cadena = "<a class='btn btn-sm btn-danger' href='#' target='_blank' onclick='detalle(" +data.id+ ")' title='Detalle'><b><i class='fa fa-file-pdf-o'></i> Detalle</a>";

                        return cadena;
                    }
                }

            ],
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },
            "order": [
            ],
        });



        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

    });

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })


    function detalle(id) {

        var url = '{{ route("compras.notas.show", ":id")}}';
        url = url.replace(':id',id);
        window.open(url, "Comprobante SISCOM", "width=900, height=600")
    }
</script>
@endpush
