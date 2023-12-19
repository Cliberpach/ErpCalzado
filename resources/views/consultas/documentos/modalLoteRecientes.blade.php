<div class="modal inmodal" id="modal_lote_recientes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" onclick="limpiarModalloteReciente()" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title d-none"></h4>
                <small class="font-bold d-none"></small>
            </div>
            <div class="modal-body">
                <div class="form-group m-l">
                    <span><b>Instrucciones:</b> Doble click en detalle para regresar a edición.</span>
                </div>
                <div class="form-group">
                    <div class="table-responsive m-t">
                        <table class="table dataTables-lotes-recientes table-bordered" style="width:100%; text-transform:uppercase;" id="table_lotes_recientes">
                            <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">PRODUCTO</th>
                                <th class="text-center">UNIME</th>
                                <th class="text-center">LOTE</th>
                                <th class="text-center">FECHA VENCE.</th>
                                <th class="text-center">CANTID.</th>
                                <th class="text-center">COD. BARRA</th>
                                <th class="text-center">PREC.VENTA</th>
                                <th class="text-center">PREC.COMPRA</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Seleccionar el lote del producto a vender.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<!-- DataTable -->
<link href="{{asset('Inspinia/css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<style>

    #table_lotes_recientes div.dataTables_wrapper div.dataTables_filter{
        text-align: left !important;
    }
    #table_lotes_recientes tr[data-href] {
        cursor: pointer;
    }
    #table_lotes_recientes tbody .fila_lote_recientes.selected {
        /* color: #151515 !important;*/
        font-weight: 400;
        color: white !important;
        background-color: #18a689 !important;
        /* background-color: #CFCFCF !important; */
    }

    #modal_lote_recientes  div.dataTables_wrapper div.dataTables_filter{
        text-align:left !important;
    }


    @media only screen and (max-width: 992px) {

        #table_tabla_registro_filter{
            text-align:left;
        }

        #table_lotes_recientes_filter{
            text-align: left;
        }
        #table_lotes_recientes  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            float:left;
            margin: 10px 0;
            white-space: nowrap;
        }

    }

    @media only screen and (min-width: 428px) and (max-width: 1190px) {
        /* Para tables: */
        #modal_lote_recientes div.dataTables_filter input {
            width: 175% !important;
            display: inline-block !important;
        }
    }

    @media only screen and (max-width: 428px) {
        /* Para celular: */
        #modal_lote_recientes  div.dataTables_filter input {
            width: 100% !important;
            display: inline-block !important;
        }
    }

    @media only screen and (min-width: 1190px) {

        #modal_lote_recientes div.dataTables_filter input {
            width: 363% !important;
            display: inline-block !important;
        }
    }
</style>
@endpush

@push('scripts')
<!-- DataTable -->
<script src="{{asset('Inspinia/js/plugins/dataTables/datatables.min.js')}}"></script>
<script>


function obtenerLotesproductosRecientes() {
    //RUTA LOTES PRODUCTOS
    var url = '{{ route("consultas.ventas.documento.no.getLotRecientes", ":id")}}';
    url = url.replace(':id', '{{ $documento->id }}');
    //ELIMINAR EL DATATABLE PARA VOLVER A INSTANCIARLO
    $(".dataTables-lotes-recientes").dataTable().fnDestroy();
    //INSTANCIAR DATATABLE
    var lotes = $('.dataTables-lotes-recientes').DataTable({
        "dom":
                "<'row'<'col-sm-12 col-md-12 col-lg-12'f>>" +
                "<'row'<'col-sm-12'tr>>"+
                "<'row justify-content-between'<'col information-content p-0'i><''p>>",

        "bPaginate": true,
        "processing":true,
        "ajax": url,
        "columns": [
            {data: 'id', className: "text-center",visible: false, sWidth: '0%'},
            {data: 'nombre', className: "text-left", sWidth: '35%' },
            {data: 'unidad_producto', className: "text-center", sWidth: '5%' },
            {data: 'codigo_lote', className: "text-center",visible: false, sWidth: '15%' },
            {data: 'fecha_venci', className: "text-center",visible: false, sWidth: '5%' },
            {data: 'cantidad_logica', className: "text-center", sWidth: '10%' },
            {data: 'codigo_barra', className: "text-center", sWidth: '15%' },
            {data: 'monto', className: "text-center", sWidth: '10%' },
            {
                data: null,
                className: "text-center letrapequeña",
                sWidth: '5%',
                render: function(data) {
                    if (data.precio_soles == null) {
                        return '0.00';
                    }else{
                        return convertFloat(data.precio_soles).toFixed(2);
                    }
                }
            },
        ],
        "bLengthChange": true,
        "bFilter": true,
        "order": [],
        "bInfo": true,
        'bAutoWidth': false,
        "language": {
                    "url": "{{asset('Spanish.json')}}"
        },
        createdRow: function(row, data, dataIndex, cells) {
            $(row).addClass('fila_lote_recientes');
            $(row).attr('data-href', "");
        },


    });
}

$(document).ready(function() {

    $('buttons-html5').removeClass('.btn-default');
    $('#table_lotes_recientes_wrapper').removeClass('');

    $('.dataTables-lotes-recientes tbody').on( 'click', 'tr', function () {
            $('.dataTables-lotes-recientes').DataTable().$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
    } );

    //DOBLE CLICK EN LOTES
    $ ('.dataTables-lotes-recientes'). on ('dblclick', 'tbody td', function () {
        var lote =  $('.dataTables-lotes-recientes').DataTable();
        var data = lote.row(this).data();
        ingresarProductoRecientes(data)
    });

})

function ingresarProductoRecientes(producto) {
    var enviar = false;
    var existe = buscarProducto(producto.id)
    if (existe == true) {
        toastr.error('Producto ya se encuentra ingresado.', 'Error');
        enviar = true;
    }

    let detalle = {
        lote_id: producto.id,
        unidad: producto.unidad_producto,
        producto: producto.nombre,
        precio_unitario: producto.precio_unitario,
        valor_unitario: producto.valor_unitario,
        valor_venta: producto.valor_venta,
        cantidad: producto.cantidad_logica,
        precio_inicial: producto.precio_inicial,
        dinero: producto.dinero,
        descuento: producto.descuento,
        precio_nuevo: producto.precio_nuevo,
        detalle_id: producto.detalle_id,
    }
    if (enviar != true) {
        agregarTabla(detalle);
        sumaTotal();
        $('#asegurarCierre').val(1);
    }
    limpiarModalloteReciente()
}

function limpiarModalloteReciente() {
    //ACTUALIZAR DATATABLE
    obtenerLotesproductosRecientes();
    //CERRAR MODAL
    $('#modal_lote_recientes').modal('hide');
}
//AL ABRIR EL MODAL SE DEBE DE ACTUALIZAR EL DATATABLE
$('#modal_lote_recientes').on('show.bs.modal', function(e) {
    //ACTUALIZAR DATATABLE
    obtenerLotesproductosRecientes();
});


</script>
@endpush
