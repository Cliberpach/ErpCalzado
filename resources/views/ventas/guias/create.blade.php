@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA GUIA DE REMISION</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('ventas.guiasremision.index')}}">Guias de Remision</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-bottom: 0px;">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    @include('ventas.guias.forms.form_guia_create')
                </div>
            </div>
        </div>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-top:0px;">

    <div class="row">
        <div class="col-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h3 class="font-weight-bold text-primary">
                                <i class="fas fa-box-open"></i> PRODUCTOS
                            </h3>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-xs-12 mb-3">
                            <label class="required">Modelo</label>
                            <select id="modelo"
                                class="select2_form form-control {{ $errors->has('modelo') ? ' is-invalid' : '' }}"
                                onchange="getProductosByModelo(this.value)" >
                                <option></option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}"
                                        {{ old('modelo') == $modelo->id ? 'selected' : '' }}>
                                        {{ $modelo->descripcion }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"><b><span
                                        id="error-producto"></span></b></div>
                        </div>
                        <div class="col-12 mb-5">
                            @include('ventas.guias.table-stocks')
                        </div>           
                        <div class="col-lg-2 col-xs-12">
                            <button  type="button" id="btn_agregar_detalle"
                                class="btn btn-warning btn-block">
                                    <i class="fa fa-plus"></i> AGREGAR
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>





@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')

<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>

        $('#cantidad').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $(document).ready(function() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            $("#departamento").on("change", cargarProvincias);

            $('#provincia').on("change", cargarDistritos);
        });

        function cargarProvincias() {
            var departamento_id = $("#departamento").val();
            if (departamento_id !== "" || departamento_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        departamento_id: departamento_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.provincias') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#provincia").empty();
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.provincias != null) {
                                $("#provincia").empty().trigger("change");
                                let codigoProvincia = $("#codigoProvincia").val();
                                if(codigoProvincia == ""){
                                    $("#provincia").select2({
                                        placeholder: "SELECCIONAR",
                                        allowClear: true,
                                        height: '200px',
                                        width: '100%',
                                        data: data.provincias
                                    }).val($("#provincia").find(':selected').val()).trigger('change');
                                }else{
                                    $("#provincia").select2({
                                        placeholder: "SELECCIONAR",
                                        allowClear: true,
                                        height: '200px',
                                        width: '100%',
                                        data: data.provincias
                                    });
                                    $("#provincia").select2("val", codigoProvincia);
                                    $("#codigoProvincia").val("");
                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function cargarDistritos() {
            var provincia_id = $("#provincia").val();
            if (provincia_id !== null && provincia_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        provincia_id: provincia_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.distritos') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#ubigeo_llegada").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.distritos != null) {
                                var selected = $('#ubigeo_llegada').find(':selected').val();
                                // $("#ubigeo_llegada").select2({
                                //     data: data.distritos
                                // });

                                let codigoDistrito = $("#codigoDistrito").val();
                                if(codigoDistrito == ""){
                                    $("#ubigeo_llegada").select2({
                                        placeholder: "SELECCIONAR",
                                        allowClear: true,
                                        height: '200px',
                                        width: '100%',
                                        data: data.distritos
                                    }).val($("#ubigeo_llegada").find(':selected').val()).trigger('change');
                                }else{
                                    $("#ubigeo_llegada").select2({
                                        placeholder: "SELECCIONAR",
                                        allowClear: true,
                                        height: '200px',
                                        width: '100%',
                                        data: data.distritos
                                    });
                                    $("#ubigeo_llegada").select2("val", codigoDistrito);
                                    $("#codigoDistrito").val("");
                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        // Solo campos numericos
        $('#ubigeo_partida, #dni_conductor').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

         $(document).ready(function() {


             //DATATABLE - COTIZACION
             table = $('.dataTables-detalle-documento').DataTable({
                 "dom": 'lTfgitp',
                 "bPaginate": true,
                 "bLengthChange": true,
                 "responsive": true,
                 "bFilter": true,
                 "bInfo": false,
                 "columnDefs": [
                    //  {
                    //    "targets": 0,
                    //      "visible": false,
                    //      "searchable": false
                    //  },
                    {
                        searchable: false,
                         "targets": [0],
                         "className": 'text-center'
                     },
                     {
                         searchable: false,
                         "targets": [1],
                         "className": 'text-center'
                     },
                     {
                         "targets": [2],
                         "className": 'text-center'
                     },
                     {
                         "targets": [3],
                         "className": 'text-center'
                     },
                     {
                         "targets": [4],
                         "className": 'text-center'
                     },
                    //   {
                    //       "targets": [5],
                    //   },
                    //   {
                    //       "targets": [6],
                    //       "className": 'text-center',
                    //      "visible": false,
                    //   },
                    //  {
                    //      "targets": [7],
                    //      "className": 'text-center',
                    //      "visible": false,
                    //  },
                    //  {
                    //      "targets": [8],
                    //      "visible": false,
                    //      "className": 'text-center'
                    //  },
                 ],
                 'bAutoWidth': false,
                 "language": {
                     url: "{{asset('Spanish.json')}}"
                 },
                 "order": [[ 0, "desc" ]],
             });

             //DIRECCION DE LA TIENDA OLD

             //Controlar Error
             $.fn.DataTable.ext.errMode = 'throw';
         });

        function limpiarErrores() {
            $('#cantidad').removeClass("is-invalid")
            $('#error-cantidad').text('')

            $('#precio').removeClass("is-invalid")
            $('#error-precio').text('')

            $('#producto').removeClass("is-invalid")
            $('#error-producto').text('')
        }

        function limpiarDetalle() {
            $('#precio').val('')
            $('#cantidad').val('')
            $('#presentacion_producto').val('')
            $('#codigo_nombre_producto').val('')
            $('#producto').val($('#producto option:first-child').val()).trigger('change');

        }

        function obtenerMedida(id) {
            var medida = ""
            @foreach(unidad_medida() as $medida)
                if ("{{$medida->id}}" == id) {
                    medida = "{{$medida->simbolo.' - '.$medida->descripcion}}"
                }
            @endforeach
            return medida
        }

        function registrosProductos() {
            var table = $('.dataTables-detalle-documento').DataTable();
            var registros = table.rows().data().length;
            return registros
        }

        function validarFecha() {
            var enviar = false
            var productos = registrosProductos()
            if ($('#fecha_documento_campo').val() == '') {
                toastr.error('Ingrese Fecha de Documento.', 'Error');
                $("#fecha_documento_campo").focus();
                enviar = true;
            }

            if ($('#fecha_atencion_campo').val() == '') {
                toastr.error('Ingrese Fecha de Atención.', 'Error');
                $("#fecha_atencion_campo").focus();
                enviar = true;
            }

            if (productos == 0) {
                toastr.error('Ingrese al menos 1 Producto.', 'Error');
                enviar = true;
            }
            return enviar
        }

        $('#enviar_documento').submit(function(e) {
            e.preventDefault();
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


        })
</script>

<script>
    $(function(){
        let cliente_id = $("#cliente_id").val();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                _token: $('input[name=_token]').val(),
                cliente_id
            },
            url: "{{ route('ventas.cliente.getcustomer') }}",
            success: function(data) {
                const { departamento_id, provincia_id, distrito_id,direccion } = data;
                $("#codigoProvincia").val(provincia_id);
                $("#codigoDistrito").val(distrito_id);
                $("#departamento").select2("val", departamento_id);
                $("#direccion_tienda").val(direccion)
            }
        });
    });
</script>
@endpush