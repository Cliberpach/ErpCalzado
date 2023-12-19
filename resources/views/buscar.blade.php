<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ErpSiscom| Sistema de Producción</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('img/siscom.ico')}}" />

    <link href="{{asset('Inspinia/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('Inspinia/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('Inspinia/css/style.css')}}" rel="stylesheet">



    <!-- Toastr style -->
    <link href="{{asset('Inspinia/css/plugins/toastr/toastr.min.css')}}" rel="stylesheet">

    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <!-- DataTable -->
    <link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
</head>

<body class="gray-bg">
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 m-4">
                <div class="loginscreen animated fadeInDown">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4 class="">Buscar comprobante electrónico</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-12">
                                    <form class="m-t" id="form-buscar">
                                        @csrf
                                        <div class="form-group">
                                            <label class="required">Tipo Documento</label>
                                            <select  class="select2_form form-control" style="text-transform: uppercase; width:100%" name="tipo_documento" id="tipo_documento" required>
                                                <option></option>
                                                @foreach (tipos_venta() as $tipo)
                                                    @if ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')
                                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Fecha Emisión</label>
                                            <input type="date" class="form-control" name="fecha_emision" id="fecha_emision" value="{{ $hoy }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Serie</label>
                                            <input type="text" class="form-control" name="serie" id="serie" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Número</label>
                                            <input type="text" class="form-control" name="correlativo" id="correlativo" value="" onkeypress="isNumber(event)">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Número Cliente (RUC/DNI/CE)</label>
                                            <input type="text" class="form-control" name="documento" id="documento" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Monto Total</label>
                                            <input type="text" class="form-control" name="total" id="total" value="" onkeypress="return filterFloat(event, this);">
                                        </div>
                                        <div class="row">
                                            <div class="col-6"></div>
                                            <div class="col-6 text-right">
                                                <button type="submit" class="btn btn-success m-b">Buscar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 d-none" id="tbl-doc">
                                    <div class="table-responsive">
                                        <table class="table dataTables-documento">
                                                <thead>
                                                    <tr>
                                                        <th>Cliente</th>
                                                        <th>Número</th>
                                                        <th>Total</th>
                                                        <th>Descargas</th>
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
                    <p class="m-t"> <small>SISCOM &copy; 2022 </small> </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{asset('Inspinia/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/popper.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/bootstrap.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{asset('Inspinia/js/inspinia.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/pace/pace.min.js')}}"></script>

    <!-- jQuery UI -->
    <script src="{{asset('Inspinia/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>

    <!-- Toastr script -->
    <script src="{{asset('Inspinia/js/plugins/toastr/toastr.min.js')}}"></script>

    <!-- Propio scripts -->
    <script src="{{ asset('Inspinia/js/scripts.js') }}"></script>

    <!-- SweetAlert -->
    <script src="{{asset('SweetAlert/sweetalert2@10.js')}}"></script>
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <!-- DataTable -->
    <script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
    <script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });
        });

        $('#form-buscar').submit(function(e) {
            e.preventDefault();
            $('#tbl-doc').addClass('d-none');
            let frmBuscar = document.getElementById('form-buscar');
            let formData = new FormData(frmBuscar);
            axios.post('{{ route("buscar.getDocument")}}', formData).then((value) => {
                let response = value.data;
                console.log(response);
                if(response.success)
                {
                    $('#tbl-doc').removeClass('d-none');
                    loadTable(response.comprobantes)
                }
                else
                {
                    toastr.error(response.mensaje);
                }
            })
        });

        function comprobanteElectronico(id) {
            var url = '{{ route("ventas.documento.comprobante", ":id")}}';
            url = url.replace(':id',id+'-100');
            window.open(url, "Comprobante SISCOM", "width=900, height=600")
        }

        function xmlElectronico(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            });

            Swal.fire({
                title: "Opción XML",
                text: "¿Seguro que desea obtener el documento de venta en xml?",
                showCancelButton: true,
                icon: 'info',
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                // showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.value) {

                    var url = '{{ route("ventas.documento.xml", ":id")}}';
                    url = url.replace(':id',id);

                    window.location.href = url

                    // Swal.fire({
                    //     title: '¡Cargando!',
                    //     type: 'info',
                    //     text: 'Generando XML',
                    //     showConfirmButton: false,
                    //     onBeforeOpen: () => {
                    //         Swal.showLoading()
                    //     }
                    // })

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

        function loadTable(comprobantes)
        {
            $('.dataTables-documento').dataTable().fnDestroy();
            $('.dataTables-documento').DataTable({
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": false,
                'bSearching': false,
                "bInfo": false,
                "bSort" : false,
                "aaSorting": [],
                "bAutoWidth": false,
                "data": comprobantes,
                "columns": [
                    {
                        data: 'documento_cliente',
                        className: "text-left"
                    },
                    {
                        data: 'numero_doc',
                        className: "text-left"
                    },
                    {
                        data: 'total',
                        className: "text-center"
                    },

                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            return "<button type='button' class='btn btn-default' onclick='xmlElectronico(" +data.id+ ")' title='XML'>XML</button>" +
                            "<button type='button' class='btn btn-default' onclick='comprobanteElectronico("+data.id+")' title='PDF'>PDF</button>";

                        },
                    },

                ],
                "language": {
                            "url": "{{asset('Spanish.json')}}"
                },
                "order": [[ 0, "desc" ]],


            });
        }
    </script>
</body>

</html>
