<div class="modal inmodal" id="modal_detalles_recibos_caja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <i class="fas fa-history modal-icon"></i>                
                    <h3>HISTORIAL DE USO</h3>
                </div>
                
       
            </div>
            <div class="modal-body">
                <div class="row">
                   <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table-detalles-recibo">
                                <thead>
                                  <tr>
                                    <th scope="col">DOC</th>
                                    <th scope="col">FECHA</th>
                                    <th scope="col">USUARIO</th>
                                    <th scope="col">SALDO ANTES</th>
                                    <th scope="col">MONTO USADO</th>
                                    <th scope="col">SALDO DESPUÉS</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                              </table>
                        </div>
                   </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.css" rel="stylesheet">

@push('scripts')
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.js"></script>
@endpush

<script>
    let dataTableDetallesRecibo     =   null;

    function loadDataTableDetallesRecibo(){
        dataTableDetallesRecibo     =   new DataTable('#table-detalles-recibo',{
            "order": [
                        [0, 'desc']
            ],
            buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'DETALLES DEL RECIBO CAJA'
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'ATENCIONES DEL RECIBO DE CAJA'
                    },
                ], 
            dom: '<"buttons-container"B><"search-length-container"lf>tp',
            bProcessing: true,
            language: {
                    processing:     "Procesando datos...",
                    search:         "BUSCAR: ",
                    lengthMenu:    "MOSTRAR _MENU_ ITEMS",
                    info:           "MOSTRANDO _START_ A _END_ DE _TOTAL_ ITEMS",
                    infoEmpty:      "MOSTRANDO 0 ITEMS",
                    infoFiltered:   "(FILTRADO de _MAX_ ITEMS)",
                    infoPostFix:    "",
                    loadingRecords: "CARGA EN CURSO",
                    zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable:     "NO HAY ITEMS DISPONIBLES",
                    paginate: {
                        first:      "PRIMERO",
                        previous:   "ANTERIOR",
                        next:       "SIGUIENTE",
                        last:       "ÚLTIMO"
                    },
                    aria: {
                        sortAscending:  ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
            }
        });
    }

    function pintarDetallesRecibo(detalles){
        dataTableDetallesRecibo.clear().draw();
        detalles.forEach((d)=> {
            dataTableDetallesRecibo.row.add([
                `${d.documento_serie}-${d.documento_correlativo}`,
                d.fecha_uso,
                d.usuario,
                d.saldo_antes,
                d.monto_usado,
                d.saldo_despues
            ]).draw();
        })
    }
</script>