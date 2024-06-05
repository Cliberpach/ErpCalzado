
<!-- Modal -->
<div class="modal inmodal" id="modal_resumen_detalle" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg" style="max-width: 80%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-info-circle modal-icon"></i>                
                <h4 class="modal-title">DETALLE RESUMEN</h4>
                <h3 class="font-bold" id="resumen-title">Registrar</h3>
            </div>
            <div class="modal-body content_cliente">
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table-resumen-detalle" style="width:100%">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">DOC</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">DNI/RUC</th>
                                    <th scope="col">TOTAL</th>
                                    <th scope="col">SUBTOTAL</th>
                                    <th scope="col">IGV</th>
                                    
                                    <th scope="col">FECHA</th>
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

<script>
    let datatableResumenDetalle      =  null;

    async function getDetallesResumen(resumen_id){
        try {
            const res   =   await axios.get(route('ventas.resumenes.getDetalles',resumen_id));
            console.log(res);
            if(res.data.success){
                pintarDetallesResumen(res.data.resumen_detalle);
            }else{
                datatableResumenDetalle.clear();
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA SOLICITUD DETALLE RESÚMEN');
        }
    }

    function pintarDetallesResumen(detalles_resumen){
        datatableResumenDetalle.clear();

        detalles_resumen.forEach(function(dato) {
            datatableResumenDetalle.row.add([
                dato.resumen_id,
                `${dato.documento_serie}-${dato.documento_correlativo}`,
                dato.cliente,
                dato.documento_doc_cliente,
                dato.documento_total,
                dato.documento_subtotal,
                dato.documento_igv,
                dato.fecha
            ]);
        });

        datatableResumenDetalle.draw();
    }

    function loadDataTableDetallesResumen(){
        datatableResumenDetalle     =   new DataTable('#table-resumen-detalle',{
            "order": [
                        [0, 'desc']
            ],
            buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-file-excel-o" style="font-size:15px;"></i> Excel',
                        title: 'DETALLES DEL RESUMEN',
                    },
                    {
                        extend: 'print',
                        className: 'custom-button btn-check', 
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: 'DETALLES DEL RESUMEN'
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