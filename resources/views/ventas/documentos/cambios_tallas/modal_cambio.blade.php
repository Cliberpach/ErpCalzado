<div class="modal inmodal" id="modal-cambio-talla" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-shoe-prints modal-icon"></i>                
                <h4 class="modal-title">ELEGIR TALLA</h4>
                <small class="font-bold">Tallas</small>  
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <label for="talla" style="font-weight: bold;">TALLAS DISPONIBLES</label>
                        <select name="talla" id="talla" class="select2_form" onchange="setStock(this)">

                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="stock" style="font-weight: bold;">STOCK DISPONIBLE</label>
                        <input readonly type="text" id="stock" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <label for="stock" style="font-weight: bold;">CANTIDAD A CAMBIAR</label>
                        <input type="text" id="cantidad_cambio" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button"  class="btn btn-success btn-sm" id="btn-cambiar-talla" ><i class="fas fa-check"></i> Cambiar</button>
                    <button type="button"  class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const cambios =   []; 

    function eventsModalCambios(){
        document.querySelector('#cantidad_cambio').addEventListener('input',(e)=>{
            const regex = /\D/g;

            e.target.value = e.target.value.replace(regex, '');
        })

        document.querySelector('#btn-cambiar-talla').addEventListener('click',async (e)=>{
            const inputCantidadCambiar          =   document.querySelector('#cantidad_cambio');
            if(inputCantidadCambiar.value.trim().length === 0){
                inputCantidadCambiar.focus();
                toastr.error('DEBE INGRESAR UNA CANTIDAD A CAMBIAR','OPERACIÓN INCORRECTA');
                return;
            }
            const documento_id      =   @json($documento->id); 

            const producto_id       =   document.querySelector('#talla').getAttribute('data-producto-id');
            const color_id          =   document.querySelector('#talla').getAttribute('data-color-id');
            const talla_id          =   document.querySelector('#talla').value;
            const producto_nombre   =   document.querySelector('#talla').getAttribute('data-producto-nombre');
            const color_nombre      =   document.querySelector('#talla').getAttribute('data-color-nombre');
            const talla_nombre      =   $('#talla option:selected').text();

            const producto_reemplazante  =   getProductoReemplazante();

            //======= OBTENIENDO CANTIDAD A CAMBIAR =======
            const detalles  =   @json($detalles);
            const detalle   =   detalles.filter( d =>  d.id == producto_cambiado.detalle_id) ;

            if(detalle.length === 0){
                toastr.error('NO SE ENCONTRÓ EL PRODUCTO A CAMBIAR EN EL DETALLE DEL DOCUMENTO DE VENTA','ERROR');
                return;
            }

            const nuevo_cambio  =   {documento_id,producto_reemplazante,
                                    producto_cambiado:{...producto_cambiado},
                                    cantidad:parseInt(inputCantidadCambiar.value)};

            //====== VALIDAR CANTIDAD A CAMBIAR =======
            const res_validarCantCambiar = await validarCantCambiar(documento_id,producto_cambiado.detalle_id,inputCantidadCambiar.value);
            if(res_validarCantCambiar.success){
                await validarStock(nuevo_cambio);
            }else{
                document.querySelector('#btn-cambiar-talla').disabled   =   false;
                document.querySelector('#btn-cambiar-talla').innerHTML  =   `<i class="fas fa-check"></i> Cambiar`;
                toastr.error(res_validarCantCambiar.exception,res_validarCantCambiar.message,{timeOut:0});
                document.querySelector('#cantidad_cambio').focus();
            }

              
        })

        document.addEventListener('click',async (e)=>{
            if(e.target.classList.contains('btn-delete-cambio')){
                mostrarAnimacion();

                const id_cambio =   e.target.getAttribute('data-cambio-id');   
                const cambio    =   cambios[id_cambio];
                
                //======= DEVOLVIENDO STOCK LÓGICO DEL PRODUCTO REEMPLAZANTE =========
                const res   =   await devolverStockLogico([cambio]);

                if(res.success){
                    const cambios_devolver  =   [cambio];
                    cambios_devolver.forEach((cd)=>{
                        const indice    =   cambios.findIndex((c)=>{
                                return c == cd;
                        })
                        cambios.splice(indice,1);
                    })

                    pintarCambios();
                    toastr.success(res.message,'STOCK LÓGICO DEVUELTO');
                }

                ocultarAnimacion();
            }
        })
    }

    function removeCambio(producto_cambiado){
        const indiceCambio  =   cambios.findIndex((c)=>{
            return c.producto_cambiado.producto_id == producto_cambiado.producto_id 
            && c.producto_cambiado.color_id == producto_cambiado.color_id 
            && c.producto_cambiado.talla_id == producto_cambiado.talla_id;
        });

        if(indiceCambio !== -1){
            cambios.splice(indiceCambio,1);
        }
    }

    function getProductoReemplazante(){
        const producto_id   =   document.querySelector('#talla').getAttribute('data-producto-id');
        const color_id      =   document.querySelector('#talla').getAttribute('data-color-id');
        const talla_id      =   document.querySelector('#talla').value;
        const producto_nombre   =   document.querySelector('#talla').getAttribute('data-producto-nombre');
        const color_nombre      =   document.querySelector('#talla').getAttribute('data-color-nombre');
        const talla_nombre      =   $('#talla option:selected').text();

        const producto_reemplazante  =   {producto_id,color_id,talla_id,producto_nombre,color_nombre,talla_nombre};
        return producto_reemplazante;
    }

    async function getTallas(producto_id,color_id){
        try {
            const almacen_id    =   @json($documento->almacen_id);
            const res           =   await axios.get(route('venta.cambiarTallas.getTallas', { almacen_id, producto_id, color_id }));
            if(res.data.success){
                tallas  =   res.data.tallas;
                pintarTallas(res.data.tallas);
            }else{
                toastr.error(res.data.exception,res.data.message);
            }

        } catch (error) {
            toastr.error('NO SE OBTUVIERON LAS TALLAS','ERROR EN LA SOLICITUD');
        }
    }

    function pintarTallas(tallas){
        $('#talla').empty();

        //======= NO MOSTRAR SU MISMA TALLA ======
        tallas  =   tallas.filter((t)=> {return !(t.producto_id == producto_cambiado.producto_id 
                && t.color_id == producto_cambiado.color_id
                && t.talla_id == producto_cambiado.talla_id) 
        });

        //======= REVIZANDO SI EL PRODUCTO YA TIENE UN CAMBIO =====
        const id_cambio = cambios.findIndex((c)=>{
            return  c.producto_cambiado.detalle_id == producto_cambiado.detalle_id
                    && c.producto_cambiado.producto_id == producto_cambiado.producto_id 
                    && c.producto_cambiado.color_id == producto_cambiado.color_id 
                    && c.producto_cambiado.talla_id == producto_cambiado.talla_id;
        });
      
        if(id_cambio !== -1){
            toastr.warning('ESTE PRODUCTO YA TIENE UN CAMBIO AGREGADO','ADVERTENCIA');
            //====== NO MOSTRAR TALLA DEL CAMBIO QUE YA TIENE AGREGADO =======
            const producto_reemplazante =   cambios[id_cambio].producto_reemplazante;
            tallas  =   tallas.filter((t)=> {return !(t.producto_id == producto_reemplazante.producto_id 
                && t.color_id == producto_reemplazante.color_id
                && t.talla_id == producto_reemplazante.talla_id) 
            });

        }

        tallas.forEach(item => {
            const nuevaOpcion = new Option(item.talla_nombre, item.talla_id, false, false);
            $('#talla').append(nuevaOpcion);
        });

        $('#talla').trigger('change');
    }

    async function setStock(selectTalla){
        const almacen_id    =   selectTalla.getAttribute('data-almacen-id');
        const producto_id   =   selectTalla.getAttribute('data-producto-id');
        const color_id      =   selectTalla.getAttribute('data-color-id');
        const talla_id      =   selectTalla.value;

        if(!talla_id){
            document.querySelector('#btn-cambiar-talla').disabled   =   true;
            toastr.error('NO HAY TALLAS DISPONIBLES, NO SE MOSTRARÁ LA TALLA ELEGIDA','ADVERTENCIA');
            return;
        }
        const stock =   await getStock(almacen_id,producto_id,color_id,talla_id)
    }

    async function getStock(almacen_id,producto_id,color_id,talla_id){
        try {
            const res   =   await axios.get(route('venta.cambiarTallas.getStock',{almacen_id,producto_id,color_id,talla_id}));
            if(res.data.success){
                document.querySelector('#stock').value  =   res.data.stock[0].stock;
            }else{
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
            console.log(error);
            toastr.error(error.data.message,'ERROR AL OBTENER STOCK DE LA TALLA');
        }
    }

    //=========== VALIDAR CANT A CAMBIAR =========
    async function validarCantCambiar(documento_id,detalle_id,cantidad){
        document.querySelector('#btn-cambiar-talla').disabled   =   true;
        document.querySelector('#btn-cambiar-talla').innerHTML  =   `<i class="fas fa-spinner fa-spin"></i> Validando`;
        try {
            const res = await axios.get(route('venta.cambiarTallas.validarCantCambiar',{documento_id,detalle_id,cantidad}));
            return res.data;
        } catch (error) {
            return {success:false,message:"ERROR EN LA SOLICITUD VALIDAR CANTIDAD A CAMBIAR",exception:error.message};
        }
    }

    async function validarStock(nuevo_cambio){
        document.querySelector('#btn-cambiar-talla').disabled   =   true;
        document.querySelector('#btn-cambiar-talla').innerHTML  =   `<i class="fas fa-spinner fa-spin"></i> Validando`;
        try {
            const res   =   await axios.post(route('venta.cambiarTallas.validarStock'),{
                nuevo_cambio: JSON.stringify(nuevo_cambio)
            });
            
            if(res.data.success){
                //========= REVIZAR SI EL PRODUCTO YA TIENE UN CAMBIO REGISTRADO PREVIAMENTE =========
                //======= BUSCANDO SI EL PRODUCTO CAMBIADO YA TIENE UN REGISTRO EN ARRAY CAMBIOS =========
                const indiceCambio  =   cambios.findIndex((c)=>{
                    return c.producto_cambiado.detalle_id == nuevo_cambio.producto_cambiado.detalle_id
                    && c.producto_cambiado.producto_id == nuevo_cambio.producto_cambiado.producto_id 
                    && c.producto_cambiado.color_id == nuevo_cambio.producto_cambiado.color_id 
                    && c.producto_cambiado.talla_id == nuevo_cambio.producto_cambiado.talla_id;
                })

                //====== EN CASO TENGA UN CAMBIO YA REGISTRADO =======
                if(indiceCambio !== -1){
                    //===== DEVOLVER STOCK LÓGICO DEL CAMBIO QUE VAMOS A QUITAR =======
                    const cambio_existente  =   cambios[indiceCambio];
                    const res_dev           =   await devolverStockLogico([cambio_existente]);
                    //====== ELIMINANDO CAMBIO DEL ARRAY CAMBIOS =======
                    cambios.splice(indiceCambio,1);
                }

                //======== INGRESANDO NUEVO CAMBIO =======
                cambios.push(nuevo_cambio);
                //======== ASEGURAR CIERRE  ESTABLECEMOS EN 2 PARA QUE DEVUELVA ======
                closeSegurity   =   2;
                //====== ACTUALIZAR TABLA CAMBIOS ======
                pintarCambios();
                //===== ALERTA ======
                toastr.success(res.data.message,'STOCK LÓGICO VALIDADO',{timeOut:5000});
                //======== CERRAR MODAL ======
                $('#modal-cambio-talla').modal('hide');
            }else{
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
                toastr.error('ERROR AL VALIDAR STOCK LÓGICO DEL PRODUCTO','ERROR EN EL LA SOLICITUD');
        }finally{
            document.querySelector('#btn-cambiar-talla').disabled   =   false;
            document.querySelector('#btn-cambiar-talla').innerHTML  =   `<i class="fas fa-check"></i> Cambiar`;
        }
    }

    function pintarCambios() {
        const bodyTablaCambios  =   document.querySelector('#tabla-cambio-tallas tbody');
        let filas             =   ``;

        cambios.forEach((c,index)=>{
            filas   +=  `<tr>
                            <th><i class="fas fa-trash-alt btn btn-danger btn-delete-cambio" data-cambio-id=${index}></i></th>  
                            <th>${c.producto_cambiado.detalle_id}</th>                                     
                            <th>${c.producto_cambiado.producto_nombre}-${c.producto_cambiado.color_nombre}-${c.producto_cambiado.talla_nombre}</th>
                            <th scope="row">${c.producto_reemplazante.producto_nombre}-${c.producto_reemplazante.color_nombre}-${c.producto_reemplazante.talla_nombre}</th>
                            <th>${c.cantidad}</th>
                        </tr>`;
        })

        bodyTablaCambios.innerHTML  =   filas;
    }

    async function devolverStockLogico(cambios_devolver){

        try {
            const listCambios   =   JSON.stringify(cambios_devolver);
            const res           =   await axios.post(route('venta.cambiarTallas.devolverStockLogico'),{
                cambios_devolver:   listCambios,
                almacen_id      :   @json($documento->almacen_id)
            });

           return res.data;
        } catch (error) {
            const res  =    {success:false,message:'ERROR AL DEVOLVER STOCK LÓGICO',exception:error};
            return res;
        }
    }

</script>

