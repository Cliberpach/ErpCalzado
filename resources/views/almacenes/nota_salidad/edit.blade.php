@extends('layout') @section('content')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVAS NOTA DE SALIDAD</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('almacenes.nota_salidad.index')}}">Nota de Salidad</a>
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
                    <input type="hidden" id='asegurarCierre' >
                    <form action="{{route('almacenes.nota_salidad.update',$notasalidad->id)}}" method="POST" id="enviar_ingresos">
                        {{method_field('PUT')}}
                        {{csrf_field()}}
                       
                            <div class="col-sm-12">
                                <h4 class=""><b>Notas de Salidad</b></h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>Registrar datos de la Nota de Salidad:</p>
                                    </div>
                                </div>
                            	<div class="form-group row">
                                   
                                    <div class="col-sm-4"  id="fecha">
                                        <label>Fecha</label>
                                        <div class="input-group date">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input type="date" id="fecha" name="fecha"
                                                class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}"
                                                value="{{old('fecha',$notasalidad->fecha)}}"
                                                autocomplete="off" readonly required>
                                            @if ($errors->has('fecha'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('fecha') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                <div class="col-sm-4">
                                        <label>Origen</label>
                                        <input type="text" name="origen" id="origen" readonly value="ALMACEN DE PRODUCTO TERMINADO" class="form-control">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="required">Destino</label>
                                        <select name="destino" id="destino" class="form-control select2_form" required>
                                            <option value="">Seleccionar Destino</option>
                                            @foreach ($destinos as $tabla)
                                                <option {{ $notasalidad->destino == $tabla->descripcion ? 'selected' : '' }} value="{{$tabla->id}}">{{$tabla->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                   
                            <input type="hidden" id="notadetalle_tabla" name="notadetalle_tabla[]">
                            <input type="hidden" id="notadetalle" name="notadetalle" value="{{$detalle}}">
                       
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class=""><b>Detalle de la Nota de Salida</b></h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                        	
                                            <div class="col-lg-6 col-xs-12">
                                                <label class="col-form-label required">Producto-lote:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="producto_lote_form" readonly> 
                                                    <span class="input-group-append"> 
                                                        <button type="button" class="btn btn-primary" id="buscarLotes" data-toggle="modal" data-target="#modal_lote"><i class='fa fa-search'></i> Buscar
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="invalid-feedback"><b><span id="error-producto"></span></b>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="col-form-label">Cantidad </label>
                                                <input type="number" id="cantidad_form" class="form-control">
                                                <div class="invalid-feedback"><b><span id="error-cantidad"></span></b>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="amount">&nbsp;</label>
                                                    <a class="btn btn-block btn-warning enviar_detalle"
                                                        style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
                                                </div>
                                            </div>

                                            <input type="hidden" name="producto_form" id="producto_form">
                                            <input type="hidden" name="lote_form" id="lote_form">
                                            
                                        </div>
                                        
                                        <hr>

                                        <div class="table-responsive">
                                            <table
                                                class="table dataTables-ingreso table-striped table-bordered table-hover"
                                                 onkeyup="return mayus(this)">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th class="text-center">ACCIONES</th>
                                                        <th class="text-center">Cantidad</th>
                    									<th class="text-center">Producto-Lote</th>
                                                        <th></th>
                                                        <th></th>
                    									
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

                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.</small>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{route('almacenes.nota_salidad.index')}}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
@include('almacenes.nota_salidad.modal')
@include('almacenes.nota_salidad.modalote')
@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}"
    rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">


<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

@endpush

@push('scripts')
<!-- Data picker -->
<script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
<!-- Date range use moment.js same as full calendar plugin -->
<script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
<!-- Date range picker -->
<script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>

<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>




<script>
//Select2
$(".select2_form").select2({
    placeholder: "SELECCIONAR",
    allowClear: true,
    width: '100%',
});


$('#enviar_ingresos').submit(function(e) {
    e.preventDefault();
    var correcto = true;
    cargarDetalle();

    if($('#notadetalle_tabla').val() === '[]')
    {
        toastr.warning('Faltan detalles a esta nota de salida.','Warning');
        correcto = false;
    }

    if (correcto) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        Swal.fire({
            title: 'Opción Guardar',
            text: "¿Seguro que desea guardar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {                
                $('#asegurarCierre').val(2)
                this.submit();
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

})


$(document).ready(function() {

    // DataTables
    obtenerLotesproductos();
    $('.dataTables-ingreso').DataTable({
        "bPaginate": true,
        "bLengthChange": true,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "language": {
            "url": "{{asset('Spanish.json')}}"
        },

        "columnDefs": [{
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            {

                "targets": [1],
                className: "text-center",
                render: function(data, type, row) {
                    return "<div class='btn-group'>" +
                        "<a class='btn btn-warning btn-sm modificarDetalle btn-edit'  style='color:white;' title='Modificar'><i class='fa fa-edit'></i></a>" +
                        "<a class='btn btn-danger btn-sm' id='borrar_detalle' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                        "</div>";
                }
            },
            {
                "targets": [2],
                className: "text-center",
            },
            {
                "targets": [3],
                className: "text-center",
            },
            {
                "targets": [4],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [5],
                "visible": false,
                "searchable": false
            },

        ],

    });

    var detalle = JSON.parse($("#notadetalle").val());
    var t = $('.dataTables-ingreso').DataTable();
        for (var i = 0; i < detalle.length; i++) {
        t.row.add([
                detalle[i].producto_id,
                '',
                detalle[i].cantidad,
                detalle[i].producto+"-"+detalle[i].lote,
                detalle[i].producto_id,
                detalle[i].lote_id
            ]).draw(false);
        }

})

//Borrar registro de articulos
$(document).on('click', '#borrar_detalle', function(event) {

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false
    })

    Swal.fire({
        title: 'Opción Eliminar',
        text: "¿Seguro que desea eliminar Producto?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var table = $('.dataTables-ingreso').DataTable();
            var data = table.row($(this).parents('tr')).data();
            var detalle = {
                lote_id: data[5],
                cantidad: data[2],
            }
            //DEVOLVER LA CANTIDAD LOGICA
            cambiarCantidad(detalle,'0')
            $('#asegurarCierre').val(1);
            table.row($(this).parents('tr')).remove().draw();
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



});


//Validacion al ingresar tablas
$(".enviar_detalle").click(function() {

    var enviar = false;
    var cantidad = $('#cantidad_form').val();
    var lote = $('#lote_form').val();
    var producto = $('#producto_form').val();

    console.log('cantidad:' +cantidad+ ', lote: ' + lote+', producto: ' + producto)

                    
    if(cantidad.length == 0 || lote.length == 0 || producto.length == 0)
    {
        toastr.error('Ingrese datos', 'Error');
        enviar=true;
    }else {
        var existe = buscarProducto($('#lote_form').val())
        if (existe == true) {
            toastr.error('Producto ya se encuentra ingresado.', 'Error');
            enviar = true;
        }
    }
   
    
    if (enviar != true) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        Swal.fire({
            title: 'Opción Agregar',
            text: "¿Seguro que desea agregar Producto?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {

                var detalle = {
                	cantidad: $('#cantidad_form').val(),
                    lote_id: $('#lote_form').val(),
                    producto_id:$( "#producto_form" ).val(),
                    producto_lote:$('#producto_lote_form').val()
                   
                }
                agregarTabla(detalle);                
                cambiarCantidad(detalle,'1');
                $('#asegurarCierre').val(1)
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
})

function buscarProducto(id) {
    var existe = false;
    var t = $('.dataTables-ingreso').DataTable();
    t.rows().data().each(function(el, index) {
        if (el[5] == id) {
            existe = true
        }
    });
    return existe
}

//CAMBIAR LA CANTIDAD LOGICA DEL PRODUCTO
function cambiarCantidad(detalle, condicion) {
    $.ajax({
        type : 'POST',
        url : '{{ route('almacenes.nota_salidad.cantidad') }}',
        data : {
            '_token' : $('input[name=_token]').val(),
            'producto_id' : detalle.lote_id,
            'cantidad' : detalle.cantidad,
            'condicion' : condicion,
        }
    }).done(function (result){
        //alert('REVISAR')
        console.log(result)
    });
}


$(document).on('click', '.btn-edit', function(event) {
    var table = $('.dataTables-ingreso').DataTable();
    var data = table.row($(this).parents('tr')).data();
    $.ajax({
        type : 'POST',
        url : '{{ route('almacenes.nota_salidad.obtener.lote') }}',
        data : {
            '_token' : $('input[name=_token]').val(),
            'lote_id' : data[5],
        }
    }).done(function (response){
        if(response.success)
        {
            $('#modal_editar_detalle #indice').val(table.row($(this).parents('tr')).index());
            $('#modal_editar_detalle #cantidad').val(data[2]);
            $('#modal_editar_detalle #cantidad_actual').val(data[2]);
            $('#modal_editar_detalle #producto_lote').val(data[3]);
            $("#modal_editar_detalle #producto").val(data[4]);
            $("#modal_editar_detalle #lote").val(data[5])
            $('#modal_editar_detalle').data("abierto","1")
            $('#modal_editar_detalle').modal('show');

            let suma_cant = parseFloat(response.lote.cantidad_logica) + parseFloat(data[2]);
            console.log(suma_cant)
            //AGREGAR LIMITE A LA CANTIDAD SEGUN EL LOTE SELECCIONADO
            $("#modal_editar_detalle #cantidad").attr({
                "max" : suma_cant,
                "min" : 1,
            });
        }
        else{
            toastr.warning('Ocurrió un error porfavor recargar la pagina.')
        } 
    });
});

function agregarTabla($detalle) {
    var t = $('.dataTables-ingreso').DataTable();
    t.row.add([
        $detalle.producto_id,'',
    	$detalle.cantidad,
    	$detalle.producto_lote,
    	$detalle.producto_id,
        $detalle.lote_id
    ]).draw(false);
    cargarDetalle()
}

function cargarDetalle() {
    var notadetalle = [];
    var table = $('.dataTables-ingreso').DataTable();
    var data = table.rows().data();
    data.each(function(value, index) {
        let fila = {
            cantidad: value[2],
            lote_id: value[5],
            producto_id: value[4],
        };

        notadetalle.push(fila);

    });
    $('#notadetalle_tabla').val(JSON.stringify(notadetalle))
}

//DEVOLVER CANTIDADES A LOS LOTES
function devolverCantidades() {
    //CARGAR PRODUCTOS PARA DEVOLVER LOTE
    cargarDetalle() 
    $.ajax({
        dataType : 'json',
        type : 'post',
        url : '{{ route('almacenes.nota_salidad.devolver.cantidades') }}',
        data : {
            '_token' : $('input[name=_token]').val(),
            'cantidades' :  $('#notadetalle_tabla').val(),
            'nota_id' : '{{ $notasalidad->id }}'
        }
    }).done(function (result){
        console.log(result)
    });
}

</script>

<script>
    window.onbeforeunload = function () { 
        //DEVOLVER CANTIDADES 
        if($('#asegurarCierre').val() == 1 ) {
            devolverCantidades();
            let detalles = JSON.parse($("#notadetalle").val());
            let newdetalles = JSON.parse($("#notadetalle_tabla").val());
            let arr = [];
            detalles.forEach(element => {
                let cont = 0;
                //let iIndice = newdetalles.findIndex(detalle => detalle.lote_id === element.lote_id);
                let existe = false;
                while(cont < newdetalles.length)
                {
                    if(newdetalles[cont].lote_id == element.lote_id)
                    {
                        cont = newdetalles.length;
                        existe = true;
                    }
                    cont++;
                }    
                
                if(!existe)
                {
                    arr.push(element);
                }
            });

            if(arr.length > 0)
            {
                $.ajax({
                    dataType : 'json',
                    type : 'post',
                    url : '{{ route('almacenes.nota_salidad.devolver.lotesinicio') }}',
                    data : {
                        '_token' : $('input[name=_token]').val(),
                        'cantidades' :  JSON.stringify(arr),
                    }
                }).done(function (result){
                    console.log(result)
                });
            }
        }
    
    };
</script>
@endpush