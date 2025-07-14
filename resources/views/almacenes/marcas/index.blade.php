@extends('layout')
@section('content')
@include('almacenes.marcas.create')
@include('almacenes.marcas.edit')
@include('almacenes.marcas.modalfile')
@section('almacenes-active', 'active')
@section('marca-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Listado de Marcas del Producto terminado</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Marcas</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a data-toggle="modal" data-target="#modal_crear_marca"  class="btn btn-block btn-w-m btn-primary m-t-md" href="#">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
        <a class="btn btn-block btn-w-m btn-primary m-t-md btn-modal-file" href="#">
            <i class="fa fa-plus-square"></i> Importar Excel
        </a>
    </div>

</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
        <div class="ibox ">

            <div class="ibox-content">

                <div class="table-responsive">
                    <table class="table dataTables-articulo table-striped table-bordered table-hover"  style="text-transform:uppercase">
                    <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th class="text-center">DESCRIPCION</th>
                            <th class="text-center">PROCEDENCIA</th>
                            <th class="text-center">CREADO</th>
                            <th class="text-center">ACTUALIZADO</th>
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
<style>
    .my-swal {
        z-index: 3000 !important;
    }
</style>
@endpush

@push('scripts')

<script>

    $(document).ready(function() {


        $('.dataTables-articulo').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "processing":true,
            "ajax": '{{ route("getmarca")}}' ,
            "columns": [
                //Tabla General
                {data: 'id', className:"text-center", "visible":false},
                {data: 'marca', className:"text-center"},
                {data: 'procedencia', className:"text-center"},
                {data: 'fecha_creacion', className:"text-center"},
                {data: 'fecha_actualizacion', className:"text-center"},
                {
                    data: null,
                    className:"text-center",
                    render: function (data) {

                        return "<div class='btn-group'><button class='btn btn-warning btn-sm modificarDetalle' onclick='obtenerData("+data.id+")' type='button' title='Modificar'><i class='fa fa-edit'></i></button><a class='btn btn-danger btn-sm' href='#' onclick='eliminar("+data.id+")' title='Eliminar'><i class='fa fa-trash'></i></a></div>"
                    }
                }

            ],
            "language": {
                        "url": "{{asset('Spanish.json')}}"
            },
            "order": [],



        });

    });

    //Controlar Error
    $.fn.DataTable.ext.errMode = 'throw';

    function obtenerData($id) {
        var table = $('.dataTables-articulo').DataTable();
        var data = table.rows().data();
        limpiarError()
        data.each(function (value, index) {
            if (value.id == $id) {
                $('#tabla_id_editar').val(value.id);
                $('#marca_editar').val(value.marca);
                $('#procedencia_editar').val(value.procedencia);
            }
        });

        $('#modal_editar_marca').modal('show');


    }

    //Old Modal Editar
    @if ($errors->has('marca') )
        $('#modal_editar_marca').modal({ show: true });
    @endif

    function limpiarError() {
        $('#marca_editar').removeClass( "is-invalid" )
        $('#error-marca').text('')
    }

    $('#modal_editar_marca').on('hidden.bs.modal', function(e) {
        limpiarError()
    });

    //Old Modal Crear
    @if ($errors->has('descripcion_guardar') )
        $('#modal_crear_marca').modal({ show: true });
    @endif

    function guardarError() {
        $('#marca_guardar').removeClass( "is-invalid" )
        $('#error-marca-guardar').text('')
    }

    $('#modal_crear_marca').on('hidden.bs.modal', function(e) {
        guardarError()
        $('#marca_guardar').val('')

    });

    function eliminar(id) {
        const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger',
                    },
                    buttonsStyling: false
                })
        Swal.fire({
            title: 'Opción Eliminar',
            text: "¿Seguro que desea eliminar registro?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
            }).then((result) => {
            if (result.isConfirmed) {
                //Ruta Eliminar
                var url_eliminar = '{{ route("almacenes.marcas.destroy", ":id")}}';
                url_eliminar = url_eliminar.replace(':id',id);
                $(location).attr('href',url_eliminar);

                }else if (
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
    $(".btn-modal-file").on('click', function () {
        $("#modal_file").modal("show");
    });
</script>



<script>
    $(document).ready(function() {
        $("#marca_guardar").on("change", validarNombre);
    })


    $('#crear_marca').submit(function(e) {
        e.preventDefault();
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                container: 'my-swal',
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })

        if ($('#marca_existe').val() == '0') {
            Swal.fire({
                customClass: {
                    container: 'my-swal'
                },
                title: 'Opción Guardar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {

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

    function validarNombre() {
        // Consultamos nuestra BBDD
        $.ajax({
            dataType: 'json',
            type: 'post',
            url: '{{ route('almacenes.marcas.exist') }}',
            data: {
                '_token': $('input[name=_token]').val(),
                'marca': $(this).val(),
                'id': null
            }
        }).done(function(result) {
            if (result.existe == true) {
                toastr.error('La marca ya se encuentra registrada', 'Error');
                $(this).focus();
                $('#marca_existe').val('1')
            } else {
                $('#marca_existe').val('0')
            }
        });
    }
</script>
@endpush
