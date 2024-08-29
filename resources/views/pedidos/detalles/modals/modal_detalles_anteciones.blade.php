<div class="modal fade" id="modal_detalles_atenciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Atenciones</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            @include('pedidos.detalles.tables.table_detalles_atenciones')
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>

<script>
  
  async function openMdlAtenciones(pedido_id,producto_id,color_id,talla_id){
        try {
          toastr.clear();
          mostrarAnimacionCotizacion();
          const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesAtenciones',{pedido_id,producto_id,color_id,talla_id}));
            
          if(res.data.success){
            pintarMdlDetallesAtenciones(res.data.documentos_atenciones);
            $('#modal_detalles_atenciones').modal('show');
            toastr.info('VISUALIZANDO ATENCIONES');
          }else{
            toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
          }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER ATENCIONES DEL DETALLE');
        }finally{
            ocultarAnimacionCotizacion();
        }

    }

    function pintarMdlDetallesAtenciones(lstAtenciones){
        const tbody =   document.querySelector('#table_detalles_atenciones tbody');
        let filas   =   ``;

        lstAtenciones.forEach((atencion)=>{
            filas   +=  `<tr>
                            <th>${atencion.serie}-${atencion.correlativo}</th>
                            <td>${atencion.cliente}</td>
                            <td>${atencion.usuario}</td>
                            <td>${atencion.created_at}</td>
                            <td>${atencion.cantidad}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }
</script>