<div class="modal fade" id="modal_detalle_orden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
            
            <div class="col-12">
                <div class="row">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <i class="fas fa-info-circle" style="color: #e3e9f2;font-size:100px;"></i>                
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <h5 class="modal-title" id="exampleModalLabel">Detalle - <span style="font-weight: bold;" id="span_nro_orden">Orden</span></h5>
                </div>
                
            </div>
            
           
            
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            @include('pedidos.ordenes.tables.table_detalle_orden')
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function pintarDetalleOrden(lstDetalleOrden){
        let filas   =   ``;
        const tbody =   document.querySelector('#table_detalle_orden tbody');

        lstDetalleOrden.forEach((producto)=>{
            filas   += `<tr>
                        <th>${producto.modelo_nombre}</th>
                        <td>${producto.producto_nombre}</td>
                        <td>${producto.color_nombre}</td>
                        <td>${producto.talla_nombre}</td>
                        <td>${producto.cantidad}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }
  </script>