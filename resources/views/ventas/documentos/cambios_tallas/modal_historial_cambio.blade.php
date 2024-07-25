<div class="modal inmodal" id="modal-historial-cambios" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-history modal-icon"></i>               
                <h4 class="modal-title">HISTORIAL CAMBIOS</h4>
                <small class="font-bold">Tallas</small>  
            </div>
            <div class="modal-body">
                <div class="row">
                   <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table-historial-cambios">
                                <thead>
                                  <tr>
                                    <th scope="col">PRODUCTO REEMPLAZADO</th>
                                    <th scope="col">PRODUCTO REEMPLAZANTE</th>
                                    <th scope="col">CANTIDAD</th>
                                    <th scope="col">USUARIO</th>
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
                <div class="col-md-6 text-right">
                    <button type="button"  class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    //======= FUNCTION OBTENER HISTORIAL DE CAMBIOS DE TALLAS =========
    async function getHistorialCambiosTallas(detalle_id,documento_id){
        try {
            const res   =   await axios.get(route('venta.cambiarTallas.getHistorialCambiosTallas',{detalle_id,documento_id}));
            return res.data;
        } catch (error) {
            const data = {success:false,message:error};
            return data;
        }
    }

    //======= PINTAR TABLA HISTORIAL CAMBIOS DE TALLAS ========
    function pintarTableHistorialCambios(historial_cambios_tallas){
        const tbodyTableHistorial   =   document.querySelector('#table-historial-cambios tbody');
        let filas   =   ``;
        historial_cambios_tallas.forEach((c)=>{
            filas += `<tr>
                        <th scope="row">${c.producto_reemplazado_nombre}-${c.color_reemplazado_nombre}-${c.talla_reemplazado_nombre}</th>
                        <td>${c.producto_reemplazante_nombre}-${c.color_reemplazante_nombre}-${c.talla_reemplazante_nombre}</td>
                        <td>${c.cantidad_cambiada}</td>
                        <td>${c.user_nombre}</td>
                        <td>${c.created_at}</td>
                    </tr>`;
        })
        tbodyTableHistorial.innerHTML   =   filas;
    }
</script>