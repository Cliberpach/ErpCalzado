@extends('layout') @section('content')

@section('consulta-active', 'active')
@section('utilidad_bruta-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Utilidad</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Detalle documentos de Compra</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="row align-items-end">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde</label>
                        <input type="date" id="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="fecha_desde">Fecha hasta</label>
                        <input type="date" id="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="initTable()"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-utilidad table-striped table-bordered table-hover"
                        style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th class="text-center">FECHA DOC</th>
                                    <th class="text-center">PRODUCTO</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">P. VENTA</th>
                                    <th class="text-center">P. COMP + FLT</th>
                                    <th class="text-center">UTIL</th>
                                    <th class="text-center">IMPORTE</th>
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
    let date = new Date();
    let dia = String(date.getDate()).padStart(2, '0');
    let mes = String(date.getMonth() + 1).padStart(2, '0');
    let anio =  date.getFullYear();
    let output =  anio+'-' +mes + '-' + dia;
    $('#fecha_desde').val(output);
    $('#fecha_hasta').val(output)
    var ventas = [];
    // DataTables
    initTable();

    tablaDatos = $('.dataTables-utilidad').DataTable();

});

function initTable()
{
   
    

    let verificar = true;
    var fecha_desde = $('#fecha_desde').val();
    var fecha_hasta = $('#fecha_hasta').val();
    if (fecha_desde != '' && fecha_desde != null && fecha_hasta == '') {
        verificar = false;
        toastr.error('Ingresar fecha hasta');
    }

    if (fecha_hasta != '' && fecha_hasta != null && fecha_desde == '') {
        verificar = false;
        toastr.error('Ingresar fecha desde');
    }

    if (fecha_desde > fecha_hasta && fecha_hasta != '' && fecha_desde != '') {
        verificar = false;
        toastr.error('Fecha desde debe ser menor que fecha hasta');
    }

    if(verificar)
    {
        let timerInterval;
        Swal.fire({
            title: 'Cargando...',
            icon: 'info',
            customClass: {
                container: 'my-swal'
            },
            timer: 10,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                Swal.stopTimer();
                $.ajax({
                    dataType : 'json',
                    type : 'post',
                    url : '{{ route('consultas.caja.utilidad.getTable') }}',
                    data : {'_token' : $('input[name=_token]').val(), 'fecha_desde' : fecha_desde, 'fecha_hasta' : fecha_hasta},
                    success: function(response) {
                        if (response.success) {
                            ventas = [];
                            ventas = response.ventas;
                            loadTable();
                            timerInterval = 0;
                            Swal.resumeTimer();
                            //console.log(colaboradores);
                        } else {
                            Swal.resumeTimer();
                            ventas = [];
                            loadTable();
                        }
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    }
    return false;
}

function loadTable()
{
    
    $('.dataTables-utilidad').dataTable().fnDestroy();
    $('.dataTables-utilidad').DataTable({
        "dom": '<"html5buttons"B>lTfgitp',
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: 'Excel',
                title: 'Listado de utilidad'
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
        "data": ventas,
        "columns": [
            //DOCUMENTO DE COMPRA
            {
                data: 'fecha_doc',
                className: "text-center",
            },
            {
                data: 'producto',
                className: "text-center",
            },
            {
                data: 'cantidad',
                className: "text-center"
            },
            {
                data: 'precio_venta',
                className: "text-left"
            },
            {
                data: 'precio_compra',
                className: "text-center"
            },
            {
                data: 'utilidad',
                className: "text-center"
            },

            {
                data: 'importe',
                className: "text-center"
            }

        ],
        "fnRowCallback":function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
            if(Number(aData.importe) < 0){
                $('td', nRow).css('background-color', '#FDEBD0');
            }
        },
        "language": {
                    "url": "{{asset('Spanish.json')}}"
        },
        "order": [[ 0, "desc" ]],


    });
    SumarImportes();
    return false;
}
function SumarImportes(){
    var t = $('.dataTables-utilidad').DataTable();
    let utilidadTtoal=0.00;
    let totalVenta = 0.00;
    let totalNotas=0.00;
    let contNect=0;
    let  precioNuevoTotal = 0;
    let  precioNuevTotalNegativo = 0;
    ventas.forEach(function(el, index) {
        utilidadTtoal= utilidadTtoal + Number(el.importe);
        if(Math.sign(el.importe) !=-1){
            totalVenta = totalVenta + Number(el.valorVenta);
            precioNuevoTotal = precioNuevoTotal + Number(el.precio_venta);
        }
        if(Math.sign(el.importe) == -1){
            
            let num = Number(el.importe) * -1;
            totalNotas=totalNotas + Number(el.valorVenta);
            precioNuevTotalNegativo = precioNuevTotalNegativo + Number(el.precio_venta);
            contNect++;
        }
    });
    console.log({
        utilidadTtoal,
        totalNotas,
        totalVenta,
        totalFinal: totalVenta - totalNotas - 509.40,
        porcentaje: (utilidadTtoal / (totalVenta - totalNotas - 509.40)) * 100,
        contNect
    })
}
</script>
@endpush
