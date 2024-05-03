<div class="modal inmodal" id="modal_detalles_doc" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                {{-- <i class="fa fa-user-plus modal-icon"></i> --}}
                <i class="fa-solid fa-memo-circle-info modal-icon"></i>
                <h4 class="modal-title">DETALLE</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')

                <table class="table table-hover" id="table-detalles-doc">
                    <thead>
                      <tr>
                        <th scope="col">PRODUCTO</th>
                        <th scope="col">COLOR</th>
                        <th scope="col">TALLA</th>
                        <th scope="col">CANTIDAD</th>
                      </tr>
                    </thead>
                    <tbody>
                     
                    </tbody>
                </table>

                <div class="sk-spinner sk-spinner-wave d-none">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click.prevent="Cerrar"><i
                            class="fa fa-times"></i> Cerrar</button>
                </div>
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
            dom: '<"buttons-container"B><"search-length-container"lf>t',
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

