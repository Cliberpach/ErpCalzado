<div class="modal inmodal" id="modal_docs_no_pagados" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">

            <div class="modal-header" style="background:linear-gradient(135deg,#922b21,#e74c3c); color:white; border-bottom:none;">
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title mb-0">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Documentos Pendientes de Pago
                </h4>
            </div>

            <div class="modal-body">
                <div class="alert mb-3" style="background:#fdf2f0; border:1px solid #f1948a; color:#7b241c; font-size:13px; border-radius:6px;">
                    <i class="fas fa-info-circle mr-1"></i>
                    No se puede cerrar la caja mientras existan ventas sin pagar.
                    Por favor regularice los siguientes documentos antes de continuar.
                </div>

                <table class="table table-sm table-bordered mb-0" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th style="width:10%; background:#922b21; color:white; border-color:#7b241c;">#</th>
                            <th style="background:#922b21; color:white; border-color:#7b241c;">Documento</th>
                            <th style="width:30%; background:#922b21; color:white; border-color:#7b241c;">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_docs_no_pagados">
                    </tbody>
                </table>
            </div>

            <div class="modal-footer" style="border-top:1px solid #dee2e6;">
                <small class="text-muted mr-auto" style="font-size:12px;">
                    <i class="fas fa-lock text-danger mr-1"></i>Cierre bloqueado hasta regularizar.
                </small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Entendido
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function poblarModalDocNoPagados(docs) {
        let rows = '';
        docs.forEach(function(doc, i) {
            rows += '<tr>'
                  + '<td class="text-center text-muted">' + (i + 1) + '</td>'
                  + '<td class="text-center font-weight-bold" style="color:#922b21;">'
                  +     doc.serie + '-' + doc.correlativo
                  + '</td>'
                  + '<td class="text-center">'
                  +     '<span class="badge" style="background:#f39c12; color:white; padding:4px 8px;">PENDIENTE</span>'
                  + '</td>'
                  + '</tr>';
        });
        document.getElementById('tbody_docs_no_pagados').innerHTML = rows;
        $('#modal_docs_no_pagados').modal('show');
    }
</script>
@endpush
