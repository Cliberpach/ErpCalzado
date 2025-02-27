@extends('layout') 
@section('content')

@section('ventas-active', 'active')
@section('guias-remision-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">

    <div class="col-lg-12">
       <h2  style="text-transform:uppercase"><b>REGISTRAR NUEVA GUIA DE REMISION</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('home')}}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('ventas.guiasremision.index')}}">Guias de Remision</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>

        </ol>
    </div>



</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-bottom: 0px;">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    @include('ventas.guias.forms.form_guia_create')
                </div>
            </div>
        </div>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight" style="padding-top:0px;">

    <div class="row">
        <div class="col-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h3 class="font-weight-bold text-primary">
                                <i class="fas fa-box-open"></i> PRODUCTOS
                            </h3>
                            <hr>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-3">
                            <label class="required" style="font-weight: bold;">CATEGORÍA - MARCA - MODELO - PRODUCTO</label>
                            <select 
                                id="producto"
                                class=""
                                onchange="getColoresTallas()" >
                                <option value=""></option>
                            </select>
                        </div>
                       
                        <div class="col-12 mb-5">
                            @include('ventas.guias.table-stocks')
                        </div>           
                        <div class="col-lg-2 col-xs-12">
                            <button  type="button" id="btn_agregar_detalle" class="btn btn-warning btn-block">
                                <i class="fa fa-plus"></i> AGREGAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>





@stop

@push('styles')
<link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')

<!-- Select2 -->
<script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
let dtGuiaStocks    =   null

document.addEventListener('DOMContentLoaded',()=>{
    loadSelect2();
    $('#modalidad_traslado').val('02').trigger('change');
    dtGuiaStocks    =   iniciarDataTable('table-stocks');
    events();
})

function events(){

    document.addEventListener('click',(e)=>{
        if (e.target.classList.contains('chkTipoVehiculo')) { 
            const marcado   =   e.target.checked;
            if(marcado){
                document.querySelector('#divTransportista').style.display = 'none';
            }else{
                document.querySelector('#divTransportista').style.display   = 'flex';
            }
        }
    })
}

function loadSelect2(){

    $(".select2_form").select2({
        placeholder: "SELECCIONAR", 
        allowClear: true,          
        width: '100%',            
    });

    $('#producto').select2({
        width:'100%',
        placeholder: "Buscar producto...",
        allowClear: true,
        language: {
            inputTooShort: function(args) {
                var min = args.minimum;
                return "Por favor, ingrese " + min + " o más caracteres";
            },
            searching: function() {
                return "BUSCANDO...";
            },
            noResults: function() {
                return "No se encontraron productos";
            }
        },
        ajax: {
            url: '{{route("ventas.guiasremision.getProductos")}}', 
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                return {
                    search: params.term,
                    almacen_id: $('#almacen').val(),
                    page: params.page || 1  
                };
            },
            processResults: function(data,params) {
                if(data.success){
                    params.page     =   params.page || 1;
                    const productos =   data.productos;
                    return {
                         results: productos.map(item => ({
                            id: item.producto_id,
                            text: item.producto_completo 
                        })),
                        pagination: {
                            more: data.more 
                        }
                    };
                }else{
                    toastr.error(data.message,'ERROR EN EL SERVIDOR');
                    return {
                        results:[]
                    }
                }    
            },
            cache: true
        },
        minimumInputLength: 2,
        templateResult: function(data) {
            if (data.loading) {
                return $('<span><i style="color:blue;" class="fa fa-spinner fa-spin"></i> Buscando...</span>');
            }
            return data.text;
        },
    });

}

function cambiarModalidadTraslado(modalidad){

    const conductores       =   @json($conductores);
    let   conductoresNew    =   [];
    let   selectConductor   =   $('#conductor');


    if(modalidad === '02'){
        conductoresNew  =   conductores.filter((c)=>{
            return c.modalidad_transporte   === 'PRIVADO';
        })
        document.querySelector('#divCategoriaML').style.display     =   'flex';
        document.querySelector('.chkTipoVehiculo').checked          =   false;
    }

    if(modalidad === '01'){
        conductoresNew  =   conductores.filter((c)=>{
            return c.modalidad_transporte   === 'PUBLICO';
        })
        document.querySelector('#divTransportista').style.display   = 'flex';
        document.querySelector('#divCategoriaML').style.display     = 'none';
    }

    selectConductor.val(null).trigger('change'); 
    selectConductor.select2('destroy');
    selectConductor.empty();

    conductoresNew.forEach(opcion => {
        const texto =   `${opcion.tipo_documento_nombre}:${opcion.nro_documento} - ${opcion.nombres}`;
        let newOption = new Option(texto, opcion.id, false, false);
        selectConductor.append(newOption);
    });

    selectConductor.select2({
        placeholder: 'Seleccione un conductor',
        allowClear: true,
        width:'100%'
    });

}

    //======= OBTENER COLORES Y TALLAS POR PRODUCTO =======
    async function getColoresTallas(){
        mostrarAnimacion();
        const producto_id   =   $('#producto').val();
        const almacen_id    =   $('#almacen').val();

        if(producto_id && almacen_id){
            try {
                const res   =   await   axios.get(route('pedidos.pedido.getColoresTallas',{almacen_id,producto_id}));
                if(res.data.success){

                    destruirDataTable(dtGuiaStocks);
                    limpiarTabla('table-stocks');
                    pintarTableStocks(res.data.producto_color_tallas);
                    dtGuiaStocks    =   iniciarDataTable('table-stocks');
                    //pintarPreciosVenta(res.data.producto_color_tallas);
                    //loadCarrito();
                    //loadPrecioVentaProductoCarrito(producto_id);
                }else{
                    toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error,'ERROR EN LA PETICIÓN OBTENER COLORES Y TALLAS');
            }finally{
                ocultarAnimacion();
            }
        }else{
            // destruirDataTableStocks();
            // limpiarTableStocks();
            // loadDataTableStocksPedido();
            // limpiarSelectPreciosVenta();
            // ocultarAnimacion();
        }
    }


    const pintarTableStocks = (producto)=>{

        let filas = ``;
        const   tableStocksBody     =   document.querySelector('#table-stocks tbody');
        const   btnAgregarDetalle   =   document.querySelector('#btn_agregar_detalle')


        producto.colores.forEach((color)=>{
            filas   +=  `  <tr>
                            <th scope="row" data-producto=${producto.id} data-color=${color.id} >
                                <div style="width:200px;">${producto.nombre}</div>
                            </th>
                            <th scope="row">${color.nombre}</th>
                        `;

            color.tallas.forEach((talla)=>{
                filas   +=  `<td style="background-color: rgb(210, 242, 242);">
                                        <p style="margin:0;width:20px;text-align:center;${talla.stock != 0?'font-weight:bold':''};">${talla.stock}</p>
                            </td>
                            <td width="8%">
                                <input style="width:50px;text-align:center;" type="text" class="form-control inputCantidad"
                                id="inputCantidad_${producto.id}_${color.id}_${talla.id}" 
                                data-producto-id="${producto.id}"
                                data-producto-nombre="${producto.nombre}"
                                data-color-nombre="${color.nombre}"
                                data-talla-nombre="${talla.nombre}"
                                data-color-id="${color.id}" data-talla-id="${talla.id}" 
                                data-producto-codigo="${producto.codigo}"></input>    
                            </td>`;
            })

            filas   +=  `</tr>`;
           
        })

        tableStocksBody.innerHTML = filas;
        btnAgregarDetalle.disabled = false;

    }

      
</script>
@endpush