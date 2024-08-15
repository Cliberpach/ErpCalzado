<div class="modal fade" id="modal_detalles_despachos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Despachos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            @include('pedidos.detalles.tables.table_detalles_despachos')
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>


<script>

  async function openMdlDespachos(pedido_id,producto_id,color_id,talla_id){
    try {
      mostrarAnimacionCotizacion();
      const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesDespachos',{pedido_id,producto_id,color_id,talla_id}));
            
      if(res.data.success){
        pintarMdlDetallesDespachos(res.data.despachos);
        $('#modal_detalles_despachos').modal('show');
        toastr.info('VISUALIZANDO DESPACHOS');
      }else{
        toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
      }
    } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER DESPACHOS DEL DETALLE');
    }finally{
      ocultarAnimacionCotizacion();
    }
  }

  function pintarMdlDetallesDespachos(lstDespachos){
    const tbody =   document.querySelector('#table_detalles_despachos tbody');
    let filas   =   ``;
    console.log(lstDespachos);
    lstDespachos.forEach((despacho)=>{
    let badge_estado_despacho   =   ``;

    if(despacho.estado_despacho === "DESPACHADO"){
      badge_estado_despacho   =   `<span class="badge badge-success">${despacho.estado_despacho}</span>`;
    }

    if(despacho.estado_despacho === "PENDIENTE"){
      badge_estado_despacho   =   `<span class="badge badge-danger">${despacho.estado_despacho}</span>`;
    }

    if(despacho.estado_despacho === "EMBALADO"){
      badge_estado_despacho   =   `<span class="badge badge-warning">${despacho.estado_despacho}</span>`;
    }

    filas   +=  `<tr>
                    <th>${despacho.serie}-${despacho.correlativo}</th>
                    <td>${despacho.cliente}</td>
                    <td>${despacho.usuario}</td>
                    <td>${despacho.user_despachador_nombre}</td>
                    <td>${despacho.fecha_venta}</td>
                    <td>${badge_estado_despacho}</td>
                    <td>${despacho.fecha_despacho}</td>
                    <td>${despacho.cantidad}</td>
                </tr>`;
    })

    tbody.innerHTML =   filas;
  }
</script>
