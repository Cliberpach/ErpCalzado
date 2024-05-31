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
        document.querySelector('#btn-cambiar-talla').addEventListener('click',(e)=>{

            const producto_id   =   document.querySelector('#talla').getAttribute('data-producto-id');
            const color_id      =   document.querySelector('#talla').getAttribute('data-color-id');
            const talla_id      =   document.querySelector('#talla').value;
            const producto_nombre   =   document.querySelector('#talla').getAttribute('data-producto-nombre');
            const color_nombre      =   document.querySelector('#talla').getAttribute('data-color-nombre');
            const talla_nombre      =   $('#talla option:selected').text();

            const producto_remplazante  =   {producto_id,color_id,talla_id,producto_nombre,color_nombre,talla_nombre};

            const nuevo_cambio  =   {producto_remplazante,producto_cambiado};
            cambios.push(nuevo_cambio);
        })
    }

    async function getTallas(producto_id,color_id){
        try {
            const res = await axios.get(route('venta.cambiarTallas.getTallas', { producto_id: producto_id, color_id: color_id }));
         
            if(res.data.success){
                tallas  =   res.data.tallas;
                pintarTallas(res.data.tallas);
            }else{
                toastr.error(res.data.exception,res.data.message);
            }

        } catch (error) {
            
        }
    }

    function pintarTallas(tallas){
        $('#talla').empty();

        tallas.forEach(item => {
            const nuevaOpcion = new Option(item.talla_nombre, item.talla_id, false, false);
            $('#talla').append(nuevaOpcion);
        });

        $('#talla').trigger('change');
    }

    async function setStock(selectTalla){
        const producto_id   =   selectTalla.getAttribute('data-producto-id');
        const color_id      =   selectTalla.getAttribute('data-color-id');
        const talla_id      =   selectTalla.value;

        console.log(producto_id+'-'+color_id+'-'+talla_id);
        const stock =   await getStock(producto_id,color_id,talla_id)

    }

    async function getStock(producto_id,color_id,talla_id){
        try {
            const res   =   await axios.get(route('venta.cambiarTallas.getStock',{producto_id,color_id,talla_id}));
            if(res.data.success){
                document.querySelector('#stock').value  =   res.data.stock[0].stock;
            }else{
                toastr.error(res.data.exception,res.data.message);
            }
        } catch (error) {
            toastr.error(error,'ERROR AL OBTENER STOCK DE LA TALLA');
        }
    }

</script>

