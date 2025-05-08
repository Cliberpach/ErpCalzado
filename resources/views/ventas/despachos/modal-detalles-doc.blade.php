<div class="modal fade" id="modal_detalles_doc" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow rounded border-0">
            
            {{-- Header --}}
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-info-circle mr-2"></i> DETALLE
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Subtítulo --}}
            <div class="px-4 pt-2">
                <small class="text-muted font-italic">Productos del despacho</small>
            </div>

            {{-- Body --}}
            <div class="modal-body content_cliente pt-2">

                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 mb-3">
                        <label class="font-weight-bold mb-0">DOCUMENTO:</label>
                        <div id="info_documento" class="border p-2 rounded bg-light">--</div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 mb-3">
                        <label class="font-weight-bold mb-0">SEDE DESPACHO:</label>
                        <div id="info_sede_despacho" class="border p-2 rounded bg-light">--</div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 mb-3">
                        <label class="font-weight-bold mb-0">ALMACÉN DESPACHO:</label>
                        <div id="info_almacen_despacho" class="border p-2 rounded bg-light">--</div>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" id="table-detalles-doc">
                                <thead class="bg-light text-dark text-center">
                                    <tr>
                                        <th scope="col">MODELO</th>
                                        <th scope="col">PRODUCTO</th>
                                        <th scope="col">COLOR</th>
                                        <th scope="col">TALLA</th>
                                        <th scope="col">CANT</th>
                                        <th scope="col">CANT. CAMBIADA</th>
                                        <th scope="col">CANT. SIN CAMBIO</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               
            </div>

            {{-- Footer --}}
            <div class="modal-footer d-flex justify-content-between px-4">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script src="https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-2.0.5/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/date-1.5.2/r-3.0.2/sp-2.3.1/datatables.min.js"></script>

<script>
    function dataTableDetalles(){
        return  new DataTable('#table-detalles-doc',{
            "order": [
                        [0, 'desc']
            ],
            buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'ATENCIONES DEL PEDIDO'
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'ATENCIONES DEL PEDIDO'
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
</script>
@endpush

