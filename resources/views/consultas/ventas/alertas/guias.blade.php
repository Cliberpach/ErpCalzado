@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('consulta-alertas-active', 'active')
@section('consulta-ventas-alertas-guias-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 col-md-12">
       <h2  style="text-transform:uppercase"><b>Listado de Guias no enviadas</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Guias de Remisión</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-guias table-striped table-bordered table-hover" style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center"># DOC</th>
                                    <th class="text-center">FECHA DOC.</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">PESO</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">SUNAT</th>
                                    <th class="text-center">DESCRIPCION</th>
                                    <th class="text-center">PDF</th>
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
<style>
    .letrapequeña {
        font-size: 11px;
    }

</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
$(document).ready(function() {
    var ventas = [];
    // DataTables
    loadTable();

});

function loadTable()
{
    $('.dataTables-guias').dataTable().fnDestroy();
    $('.dataTables-guias').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'NOTAS DE CREDITO NO ENVIADAS'
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
        "ajax": "{{ route('consultas.ventas.alerta.getTableGuias') }}",
        "columns": [
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data){
                    return data.serie + '-' + data.correlativo
                }
            },
            {
                data: "fecha",
                className: "text-center letrapequeña",
                name: "guias_remision.created_at"
            },
            {
                data: "cliente",
                className: "text-center letrapequeña",
                name: "guias_remision.cliente"
            },
            {
                data: "peso_productos",
                className: "text-center letrapequeña",
                name: "guias_remision.peso_productos"
            },
            {
                data: "cantidad_productos",
                className: "text-center letrapequeña",
                name: "guias_remision.cantidad_productos"
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    switch (data.sunat) {
                        case "1":
                            return "<span class='badge badge-primary' d-block>ACEPTADO</span>";
                            break;
                        case "2":
                            return "<span class='badge badge-danger' d-block>NULA</span>";
                            break;
                        default:
                            return "<span class='badge badge-success' d-block>REGISTRADO</span>";
                    }
                },
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    if(data.getCdrResponse)
                    {
                        return data.code + "-" + data.description;
                    }
                    else{
                        return "-";
                    }
                },
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    return "<button class='btn btn-info btn-pdf mb-1' title='PDF'>PDF</button>"
                }
            },
            {
                data: null,
                className: "text-center letrapequeña",
                render: function(data) {
                    let cadena = "";

                    if(data.code_regularize != '1033' && data.code != '0')
                    {
                        cadena = cadena + "<button type='button' class='btn btn-sm btn-success m-1' onclick='enviarSunat(" +data.id+ ")'  title='Enviar Sunat'><i class='fa fa-send'></i> Sunat</button>";
                    }
                    else {
                        cadena = cadena + "<span class='badge badge-warning'>CDR</span>";
                    }

                    return cadena;
                }
            },

        ],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if(aData.code == '1033')
            {
                $('td', nRow).css('display', 'none');
            }
        },
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },
        "order": [],
    });
    return false;
}

$(".dataTables-guias").on('click','.btn-pdf',function(){
    var data = $(".dataTables-guias").dataTable().fnGetData($(this).closest('tr'));
    var url = '{{ route("ventas.guiasremision.show", ":id")}}';
    url = url.replace(':id',data.id);

    window.open(url, "Comprobante SISCOM", "width=900, height=600")
});

function enviarSunat(id , sunat) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: "Opción Enviar a Sunat",
        text: "¿Seguro que desea enviar documento de venta a Sunat?",
        showCancelButton: true,
        icon: 'info',
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
        // showLoaderOnConfirm: true,
    }).then((result) => {
        if (result.value) {

            var url = '{{ route("consultas.ventas.alerta.sunat_guias", ":id")}}';
            url = url.replace(':id',id);

            window.location.href = url

            Swal.fire({
                title: '¡Cargando!',
                type: 'info',
                text: 'Enviando documento de venta a Sunat',
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            })

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })

}

@if(Session::has('sunat_exito'))
    Swal.fire({
        icon: 'success',        
        title: '{{ Session::get("id_sunat") }}',
        text: '{{ Session::get("descripcion_sunat") }}',
        showConfirmButton: false,
        timer: 2500
    })
@endif

@if(Session::has('sunat_error'))
    Swal.fire({
        icon: 'error',
        title: '{{ Session::get("id_sunat") }}',
        text: '{{ Session::get("descripcion_sunat") }}',
        showConfirmButton: false,
        timer: 5500
    })
@endif
</script>
@endpush
