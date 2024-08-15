<div class="modal fade" id="modal_detalles_devoluciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Devoluciones</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            @include('pedidos.detalles.tables.table_detalles_devoluciones')
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>

<script>
    async function openMdlDevoluciones(pedido_id,producto_id,color_id,talla_id){
        try {
            mostrarAnimacionCotizacion();
            const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesDevoluciones',{pedido_id,producto_id,color_id,talla_id}));
            console.log(res);
            if(res.data.success){
                pintarMdlDetallesDevoluciones(res.data.devoluciones);
                $('#modal_detalles_devoluciones').modal('show');
                toastr.info('VISUALIZANDO DEVOLUCIONES');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER DEVOLUCIONES DEL DETALLE');
        }finally{
            ocultarAnimacionCotizacion();
        }
    }

    function pintarMdlDetallesDevoluciones(lstDevoluciones){
        const tbody =   document.querySelector('#table_detalles_devoluciones tbody');
        let filas   =   ``;

        lstDevoluciones.forEach((devolucion)=>{
            filas   +=  `<tr>
                            <th>${devolucion.serie}-${devolucion.correlativo}</th>
                            <td>${devolucion.fecha}</td>
                            <td>${devolucion.usuario}</td>
                            <td>${devolucion.cantidad_devuelta}</td>
                            <td>${devolucion.documento_afectado}</td>
                            <td>PE-${devolucion.pedido_id}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }
</script>