<div class="modal inmodal" id="modal_historial_atenciones" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 94%;">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-concierge-bell modal-icon"></i>                
                <h4 class="modal-title">HISTORIAL ATENCIONES</h4>
                <p class="font-bold">PEDIDO # <span class="pedido_id_span"></span></p>
            </div>
            <div class="modal-body content_cliente">
               <div class="row">
                    <div class="col-12">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>ATENCIONES</h5>
                                @include('ventas.pedidos.tables-historial.table-atenciones')

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5>DETALLES DEL DOCUMENTO</h5>
                                @include('ventas.pedidos.tables-historial.table-atenciones-detalles')
                            </div>
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
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function eventsModalAtenciones(){
        document.addEventListener('click',(e)=>{
            const filaCercana   =   e.target.closest('tr');
            if(filaCercana && filaCercana.classList.contains('rowAtencion')){
                const pedido_id     =   filaCercana.getAttribute('data-pedido-id');
                const atencion_id   =   filaCercana.getAttribute('data-atencion-id');

                getAtencionDetalles(pedido_id,atencion_id);
            }
        })
    }


    async function getAtencionDetalles(pedido_id,atencion_id){
         //===== OBTENIENDO DETALLES DEL PEDIDO =======
         try {
            const res   =   await axios.get(route('ventas.pedidos.getAtencionDetalles',{pedido_id,atencion_id}));
            console.log(res);
            const type  =   res.data.type;
            if(type == 'success'){
                const atencion_detalles   =   res.data.atencion_detalles;
                pintarTableAtencionesDetalles(atencion_detalles);
            }
        } catch (error) {
        
        }
    }

    function pintarTableAtencionesDetalles(atencion_detalles) {
        const bodyPedidoDetalles    =   document.querySelector('#table-atenciones-detalles tbody');

        bodyPedidoDetalles.innerHTML    =   '';
        let body    =   ``;

        atencion_detalles.forEach((ad)=>{
            body    +=  `<tr><th scope="row">${ad.producto_nombre}</th>
            <td scope="row">${ad.color_nombre}</td>
            <td scope="row">${ad.talla_nombre}</td>
            <td scope="row">${ad.cantidad}</td>
            </tr>`;
        })

        bodyPedidoDetalles.innerHTML    =   body;
    }
</script>
@endpush


