@extends('layout')

@section('almacenes-active', 'active')
@section('nota_salidad-active', 'active')

@section('bread-module', 'Almacén')
@section('bread-submodule', 'Almacénes')
@section('hero-title', 'Lista de Notas de Salida')
@section('hero-subtitle', 'Almacénes')

@section('btn-add')
    <a class="main-btn-add" href="{{ route('almacenes.nota_salidad.create') }}">
        <i class="fas fa-plus-circle"></i> Nuevo
    </a>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            @include('almacenes.nota_salidad.tables.tbl_ns_list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // DataTables
            $('.dataTables-ingreso_mercaderia').DataTable({
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('almacenes.nota_salidad.data') }}",
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
                        data: 'almacen_origen_nombre',
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
                            //Ruta Detalle

                            //Ruta Modificar
                            var url_editar = '{{ route('almacenes.nota_salidad.edit', ':id') }}';
                            url_editar = url_editar.replace(':id', data.id);

                            var url_detalles = '{{ route('almacenes.nota_salidad.show', ':id') }}';
                            url_detalles = url_detalles.replace(':id', data.id);

                            return "<div class='btn-group' style='text-transform:capitalize;'><button data-toggle='dropdown' class='btn btn-primary btn-sm  dropdown-toggle'><i class='fa fa-bars'></i></button><ul class='dropdown-menu'>" +
                                "<li><a class='dropdown-item' href='" + url_detalles +
                                "' title='Detalles' ><b><i class='fa fa-eye'></i> Detalles</a></b></li>" +
                                "<li class=''><a class='dropdown-item' href='#' title='PDF' onclick='comprobante(" +
                                data.id +
                                ")'><b><i class='fas fa-file-pdf'></i> PDF</a></b></li>" +
                                "<li class='d-none'><a class='dropdown-item' href='" + url_editar +
                                "' title='Modificar' ><b><i class='fa fa-edit'></i> Modificar</a></b></li>" +
                                "<li class='d-none'><a class='dropdown-item' onclick='eliminar(" +
                                data.id +
                                ")' title='Eliminar'><b><i class='fa fa-trash'></i> Eliminar</a></b></li>" +
                                "</ul></div>"
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

        function comprobante(id) {
            var url = '{{ route('almacenes.nota_salidad.getPdf', ':id') }}';
            url = url.replace(':id', id + '-100');
            window.open(url, "Comprobante SISCOM", "width=900, height=600")
        }


        function eliminar(id) {
            Swal.fire({
                title: 'Opción Eliminar',
                text: "¿Seguro que desea guardar cambios?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    //Ruta Eliminar
                    var url_eliminar = '{{ route('almacenes.nota_salidad.destroy', ':id') }}';
                    url_eliminar = url_eliminar.replace(':id', id);
                    $(location).attr('href', url_eliminar);

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
    </script>
@endpush
