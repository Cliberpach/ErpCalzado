<div class="modal inmodal" id="modal_ventas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">VENTAS PENDIENTES DE PAGO</h4>
                <small class="font-bold ventas-title"></small>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table dataTables-ventas-pendientes table-striped table-bordered table-hover">
                                <thead>
                                    <th>TIPO DOC</th>
                                    <th># DOC</th>
                                    <th>FECHA DOC</th>
                                    <th>MONTO</th>
                                    <th><i class="fa fa-dashboard"></i></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
<script>
    function initTable(cliente_id, condicion_id)
    {
        let timerInterval;
        let clientes = [];
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
                    url : '{{ route('ventas.getDocumentClient') }}',
                    data : {'_token': $('input[name=_token]').val(), 'cliente_id': cliente_id, 'condicion_id': condicion_id},
                    success: function(response) {
                        if (response.success) {
                            ventas = [];
                            ventas = response.ventas;
                            loadTable(ventas);
                            timerInterval = 0;
                            Swal.resumeTimer();
                            //console.log(colaboradores);
                        } else {
                            Swal.resumeTimer();
                            ventas = [];
                            loadTable(ventas);
                        }
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    }

    function loadTable(ventas)
    {
        $('.dataTables-ventas-pendientes').dataTable().fnDestroy();
        $('.dataTables-ventas-pendientes').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel',
                    title: 'VENTAS PENDIENTES'
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
                //DOCUMENTO DE VENTA
                {
                    data: 'tipo_venta',
                    className: "text-center letrapequeña",
                },
                {
                    data: 'numero_doc',
                    className: "text-center letrapequeña",
                },
                {
                    data: 'fecha_documento',
                    className: "text-center letrapequeña"
                },
                {
                    data: 'total',
                    className: "text-center letrapequeña",
                },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    render: function(data) {

                        let cadena = '';

                        if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE')
                        {
                            cadena = cadena +
                            "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                        }

                        return cadena;
                    }
                }

            ],
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },
            "order": [],
        });
    }

    $(".dataTables-ventas-pendientes").on('click','.pagar',function(){
        var data = $(".dataTables-ventas-pendientes").dataTable().fnGetData($(this).closest('tr'));
        $('.pago-title').html(data.numero_doc);
        $('#monto_venta').val(data.total);
        $('#venta_id').val(data.id);
        $('#importe').val(data.total);
        $('.pago-subtitle').html(data.cliente);

        $('#cuenta_id').val('').trigger('change.select2');
        $('#modo_pago').val('1-EFECTIVO').trigger('change.select2');
        $('#modal_pago').modal('show');
        initCuentas(data.empresa_id);
    });

    function initCuentas(empresa_id)
    {
        $("#cuenta_id").empty().trigger('change');
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
                    url : '{{ route('ventas.documento.getCuentas') }}',
                    data : {'_token': $('input[name=_token]').val(), 'empresa_id': empresa_id},
                    success: function(response) {
                        if (response.success) {
                            if (response.cuentas.length > 0) {
                                $('#cuenta_id').append('<option></option>').trigger('change');
                                for(var i = 0;i < response.cuentas.length; i++)
                                {
                                    var newOption = '<option value="'+response.cuentas[i].id+'">'+response.cuentas[i].descripcion + ': ' + response.cuentas[i].num_cuenta +'</option>';
                                    $('#cuenta_id').append(newOption).trigger('change');
                                }

                            } else {
                                //toastr.error('CuentaS no encontradas.', 'Error');
                            }
                            timerInterval = 0;
                            Swal.resumeTimer();
                        } else {
                            timerInterval = 0;
                            Swal.resumeTimer();
                        }
                    }
                });
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        });
    }
</script>
@endpush
