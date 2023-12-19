@extends('layout') @section('content')
    @include('mantenimiento.talonarios.create')
    @include('mantenimiento.talonarios.edit')
@section('mantenimiento-active', 'active')
@section('talonarios-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2><b>LISTADO DE TALONARIOS</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Talonarios</strong>
            </li>

        </ol>
    </div>
    <div class="col-lg-2 col-md-2">
        <a data-toggle="modal" data-target="#modal_crear_talonario"  class="btn btn-block btn-w-m btn-primary m-t-md" href="#">
            <i class="fa fa-plus-square"></i> Añadir nuevo
        </a>
    </div>

</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table dataTables-talonario table-striped table-bordered table-hover"  style="text-transform:uppercase">
                            <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center">EMPRESA</th>
                                <th class="text-center"></th>
                                <th class="text-center">TIPO DOCUMENTO</th>
                                <th class="text-center">SERIE</th>
                                <th class="text-center">NRO. INICIO</th>
                                <th class="text-center">NRO. FINAL</th>
                                <th class="text-center">NRO. ACTUAL</th>
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
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <style>
        .my-swal {
            z-index: 3000 !important;
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTable -->
    <script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <script>

        $(document).ready(function() {

            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            $('.dataTables-talonario').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: 'Tablas Generales'
                    },
                    {
                        titleAttr: 'Imprimir',
                        extend: 'print',
                        text:      '<i class="fa fa-print"></i> Imprimir',
                        customize: function (win){
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
                "processing":true,
                "ajax": '{{ route("mantenimiento.talonario.getTable")}}' ,
                "columns": [
                    //Tabla General
                    {data: 'id', className:"text-center", "visible": false},
                    {data: 'empresa_id', className:"text-center", "visible": false},
                    {data: 'empresa', className:"text-left"},
                    {data: 'tipo_documento', className:"text-center", "visible": false},
                    {data: 'tipo_documento_descripcion', className:"text-center"},
                    {data: 'serie', className:"text-center"},
                    {data: 'numero_inicio', className:"text-center"},
                    {data: 'numero_final', className:"text-center"},
                    {data: 'numero_actual', className:"text-center"},
                    {
                        data: null,
                        className:"text-center",
                        render: function (data) {
                            return "<div class='btn-group'>" +
                                        "<button class='btn btn-warning btn-sm modificarDetalle' onclick='obtenerData("+data.id+")' type='button' title='Modificar'>" +
                                            "<i class='fa fa-edit'></i>" +
                                        "</button>" +
                                        "<a class='btn btn-danger btn-sm' href='#' onclick='eliminar("+data.id+")' title='Eliminar'>" +
                                            "<i class='fa fa-trash'></i>" +
                                        "</a>" +
                                    "</div>"
                        }
                    }
                ],
                "language": {
                    "url": "{{asset('Spanish.json')}}"
                },
                "order": [[ 0, "desc" ]],
            });
        });

        //Controlar Error
        $.fn.DataTable.ext.errMode = 'throw';

        function obtenerData($id) {
            var table = $('.dataTables-talonario').DataTable();
            var data = table.rows().data();
            limpiarError()
            data.each(function (value, index) {
                if (value.id == $id) {
                    $('#id_editar').val(value.id);
                    $('#empresa_editar').val(value.empresa_id).trigger('change');
                    $('#tipo_documento_editar').val(value.tipo_documento).trigger('change');
                    $('#serie_editar').val(value.serie);
                    $('#numero_inicio_editar').val(value.numero_inicio);
                    $('#numero_final_editar').val(value.numero_final);
                    $('#numero_actual_editar').val(value.numero_actual);
                }
            });

            $('#modal_editar_talonario').modal('show');
        }

        //Old Modal Editar
        @if ($errors->has('tipo_documento_editar') || $errors->has('serie_editar') || $errors->has('numero_inicio_editar')
            || $errors->has('numero_final_editar') || $errors->has('numero_actual_editar'))
            $('#modal_editar_talonario').modal({ show: true });
        @endif

        function limpiarError() {
            $('#empresa_editar').removeClass( "is-invalid" );
            $('#tipo_documento_editar').removeClass( "is-invalid" );
            $('#serie_editar').removeClass( "is-invalid" );
            $('#numero_inicio_editar').removeClass( "is-invalid" );
            $('#numero_final_editar').removeClass( "is-invalid" );
            $('#numero_actual_editar').removeClass( "is-invalid" );
            $('#error-empresa-editar').text('');
            $('#error-tipo-documento-editar').text('');
            $('#error-serie-editar').text('');
            $('#error-numero-inicio-editar').text('');
            $('#error-numero-final-editar').text('');
            $('#error-numero-actual-editar').text('');
        }

        $('#modal_editar_talonario').on('hidden.bs.modal', function(e) {
            limpiarError()
        });

        //Old Modal Crear
        @if ($errors->has('empresa_guardar') ||  $errors->has('tipo_documento_guardar') || $errors->has('serie_guardar')
            || $errors->has('numero_inicio_guardar') || $errors->has('numero_final_guardar') || $errors->has('numero_actual_guardar'))
            $('#modal_crear_talonario').modal({ show: true });
        @endif

        function guardarError() {
            $('#empresa_guardar').removeClass( "is-invalid" );
            $('#tipo_documento_guardar').removeClass( "is-invalid" );
            $('#serie_guardar').removeClass( "is-invalid" );
            $('#error-tipo-documento-guardar').text('');
            $('#error-serie-guardar').text('');
        }

        $('#modal_crear_talonario').on('hidden.bs.modal', function(e) {
            guardarError()
            $('#empresa_guardar').val('').trigger('change');
            $('#tipo_documento_guardar').val('').trigger('change');
            $('#serie_guardar').val('');
            $('#numero_inicio_guardar').val('');
            $('#numero_final_guardar').val('');
            $('#numero_actual_guardar').val('');
        });


        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
            },
            buttonsStyling: false
        })


        function eliminar(id) {

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
                    var url_eliminar = '{{ route("mantenimiento.talonario.destroy", ":id")}}';
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

        $('#editar_talonario').submit(function(e){
            e.preventDefault();
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    container: 'my-swal',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false

            })

            Swal.fire({
                customClass: {
                    container: 'my-swal'
                },
                title: 'Opción Modificar',
                text: "¿Seguro que desea modificar los cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isValidateNumeros(true))
                        this.submit();
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
        })

        $('#crear_talonario').submit(function(e){
            e.preventDefault();
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    container: 'my-swal',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            })

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
                    if (isValidateNumeros())
                        this.submit();
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
        });

        function isValidateNumeros(isEditar = false)
        {
            // Validamos número de talonarios
            var numero_inicio = '', numero_final = '', numero_actual = '';
            if (isEditar) {
                numero_inicio = parseInt($("#numero_inicio_editar").val());
                numero_final = parseInt($("#numero_final_editar").val());
                numero_actual = parseInt($("#numero_actual_editar").val());
            } else {
                numero_inicio = parseInt($("#numero_inicio_guardar").val());
                numero_final = parseInt($("#numero_final_guardar").val());
                numero_actual = parseInt($("#numero_actual_guardar").val());
            }

            if (numero_inicio <= 0) {
                toastr.error('El número inicial debe ser un valor numérico positivo', 'Error');
                return false;
            }
            if (!Number.isNaN(numero_final) && numero_final <= 0) {
                toastr.error('El número final debe ser un valor numérico positivo', 'Error');
                return false;
            }
            if (numero_actual <= 0) {
                toastr.error('El número actual debe ser un valor numérico positivo', 'Error');
                return false;
            }
            if (!Number.isNaN(numero_final) && numero_inicio > numero_final) {
                toastr.error('El número inicial debe ser menor o igual que el número final', 'Error');
                return false;
            }
            if (numero_actual < numero_inicio) {
                toastr.error('El número actual debe ser mayor o igual al número inicial', 'Error');
                return false;
            }
            if (!Number.isNaN(numero_final) && numero_actual > numero_final) {
                toastr.error('El número actual debe ser menor o igual al número final', 'Error');
                return false;
            }

            return true;
        }

    </script>
@endpush
