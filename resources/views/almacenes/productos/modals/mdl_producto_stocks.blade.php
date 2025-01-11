<div class="modal fade" id="mdl_producto_stocks" tabindex="-1" aria-labelledby="mdl_producto_stocks" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 160vh;width:160vh;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mdl_producto_stocks"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 mb-3">
                    <label for="almacen" style="font-weight: bold;">ALMACÉN</label>
                    <select onchange="getColores();" name="almacen" id="almacen" class="select2_form">
                        <option value=""></option>
                        @foreach ($almacenes as $almacen)
                            <option value="{{$almacen->id}}">{{$almacen->descripcion}}</option>    
                        @endforeach
                    </select>
                </div>
                <div class="col-12"></div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <h5 style="font-weight: bold;">COLORES</h5>
                    <div class="table-responsive">
                        @include('almacenes.productos.tables.tbl_producto_colores')
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <h5 style="font-weight: bold;">TALLAS</h5>
                    <div class="table-responsive">
                        @include('almacenes.productos.tables.tbl_producto_tallas')
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Salir</button>
        </div>
      </div>
    </div>
</div>

<script>

    const parametrosMdlStocks   =   {producto_id:null};

    function eventsMdlStocks(){

        document.querySelector('#tbl_producto_colores tbody').addEventListener('click', function (event) {
            
            // Verificar si el clic ocurrió en una fila
            const clickedRow = event.target.closest('tr');
            if (!clickedRow) return;

            // Obtener los datos de la fila seleccionada
            const color_id = clickedRow.getAttribute('data-color-id');

            if (color_id) {

                getTallas(color_id);
              
            }

        });
    }

    async function openMdlStocks(producto_id){

        parametrosMdlStocks.producto_id =   producto_id;

        $('#mdl_producto_stocks').modal('show');
    }

    async function getColores(){
        try {
            toastr.clear();
            const almacen_id    =   document.querySelector('#almacen').value;
            const producto_id   =   parametrosMdlStocks.producto_id;

            if(!almacen_id || !producto_id){
                return;
            }

            const res   =   await axios.get(route('almacenes.producto.getColores',{almacen_id,producto_id}));

            if(res.data.success){
                destruirDataTable(dtProductoColores);
                limpiarTabla('tbl_producto_colores');
                pintarTablaColores(res.data.data);
                dtProductoColores = iniciarDataTable('tbl_producto_colores');
                toastr.info(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.data.message,'ERROR AL OBTENER COLORES');
            }
            console.log(res);
        } catch (error) {
            toastr.error(error.message,'ERROR EN LA PETICIÓN OBTENER COLORES');
        }
    }

    async function getTallas(color_id){
        try {
            toastr.clear();
            const almacen_id    =   document.querySelector('#almacen').value;
            const producto_id   =   parametrosMdlStocks.producto_id;

            if(!almacen_id || !producto_id || !color_id){
                return;
            }

            const res   =   await axios.get(route('almacenes.producto.getTallas',{almacen_id,producto_id,color_id}));

            if(res.data.success){
                destruirDataTable(dtProductoTallas);
                limpiarTabla('tbl_producto_tallas');
                pintarTablaTallas(res.data.data);
                dtProductoTallas = iniciarDataTable('tbl_producto_tallas');
                toastr.info(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.data.message,'ERROR AL OBTENER TALLAS');
            }
            console.log(res);
        } catch (error) {
            toastr.error(error.message,'ERROR EN LA PETICIÓN OBTENER TALLAS');
        }
    }

    function pintarTablaColores(lstColores){
        const tbody =   document.querySelector('#tbl_producto_colores tbody');
        let filas   =   ``;
        lstColores.forEach((c)=>{
            filas   +=  `<tr data-color-id="${c.color_id}">
                            <td>${c.color_nombre}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

    function pintarTablaTallas(lstTallas){
        const tbody =   document.querySelector('#tbl_producto_tallas tbody');
        let filas   =   ``;
        lstTallas.forEach((t)=>{
            filas   +=  `<tr>
                            <td>${t.talla_nombre}</td>
                            <td>${t.stock}</td>
                        </tr>`;
        })

        tbody.innerHTML =   filas;
    }

</script>