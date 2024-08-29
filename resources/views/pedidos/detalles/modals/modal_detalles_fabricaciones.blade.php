<div class="modal fade" id="modal_detalles_fabricaciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">FABRICACIONES</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            @include('pedidos.detalles.tables.table_detalles_fabricaciones')
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
</div>

<script>
    async function openMdlFabricaciones(pedido_id,producto_id,color_id,talla_id){
        try {
            toastr.clear();
            mostrarAnimacionCotizacion();
            const res   =  await axios.get(route('pedidos.pedidos_detalles.getDetallesFabricaciones',{pedido_id,producto_id,color_id,talla_id}));
            console.log(res);
            if(res.data.success){
                pintarMdlDetallesFabricaciones(res.data.fabricaciones);
                $('#modal_detalles_fabricaciones').modal('show');
                toastr.info('VISUALIZANDO FABRICACIONES');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
            }
        } catch (error) {
            toastr.error(error,'ERROR EN LA PETICIÓN VER FABRICACIONES DEL DETALLE');
        }finally{
            ocultarAnimacionCotizacion();
        }
    }

    function pintarMdlDetallesFabricaciones(lstFabricaciones){
        const tbody =   document.querySelector('#table_detalles_fabricaciones tbody');
        let filas   =   ``;

        

        lstFabricaciones.forEach((fabricacion)=>{
            let etiquetaEstado  =   `-`;

            if(fabricacion.estado === 'EN PROCESO'){
                etiquetaEstado  =   `<span class="badge badge-success">${fabricacion.estado}</span>`;
            }

            if(fabricacion.estado === 'FINALIZADO'){
                etiquetaEstado  =   `<span class="badge badge-primary">${fabricacion.estado}</span>`;
            }

            if(fabricacion.estado === 'ANULADO'){
                etiquetaEstado  =   `<span class="badge badge-danger">${fabricacion.estado}</span>`;
            }

            filas   +=  `<tr>
                            <th>OP-${fabricacion.id}</th>
                            <td>${fabricacion.fecha_registro}</td>
                            <td>${fabricacion.usuario}</td>
                            <td>${fabricacion.fecha_propuesta_atencion}</td>
                            <td>
                                <div style="display:flex;justify-content:center;">
                                    ${fabricacion.observacion? fabricacion.observacion:'-'}
                                </div>
                            </td>
                            <td>${fabricacion.tipo}</td>
                            <td>
                                <div style="display:flex;justify-content:center;">
                                    ${etiquetaEstado}
                                </div>
                            </td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }
</script>