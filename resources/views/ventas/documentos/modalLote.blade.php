<div class="modal inmodal" id="modal_lote" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" onclick="limpiarModallote()" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title d-none"></h4>
                <small class="font-bold d-none"></small>
            </div>
            <div class="modal-body">
                <div class="form-group m-l">
                    <span><b>Instrucciones:</b> Doble click en el registro del producto a vender.</span>
                </div>
                <div class="form-group">
                    <div class="table-responsive m-t">
                        <table class="table dataTables-lotes table-bordered" style="width:100%; text-transform:uppercase;" id="table_lotes">
                            <thead>
                            <tr>
                                <th class="text-center" hidden></th>
                                <th class="text-center">PRODUCTO</th>
                                <th class="text-center">UM</th>
                                <th class="text-center">CATEGORIA</th>
                                <th class="text-center">MARCA</th>
                                <th class="text-center">CANTID.</th>
                                <th class="text-center">COD. BARRA</th>
                                <th class="text-center">P. NORMAL</th>
                                <th class="text-center">P. DISTRI.</th>
                                @if ($fullaccess)
                                <th class="text-center">P.COMPRA</th>
                                @endif

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

    @media (min-width: 992px){
        .modal-lg {
            max-width: 1200px;
        }
    }

    #table_lotes div.dataTables_wrapper div.dataTables_filter{
        text-align: left !important;
    }
    #table_lotes tr[data-href] {
        cursor: pointer;
    }
    #table_lotes tbody .fila_lote.selected {
        /* color: #151515 !important;*/
        font-weight: 400;
        color: white !important;
        background-color: #18a689 !important;
        /* background-color: #CFCFCF !important; */
    }

    #modal_lote  div.dataTables_wrapper div.dataTables_filter{
        text-align:left !important;
    }


    @media only screen and (max-width: 992px) {

        #table_tabla_registro_filter{
            text-align:left;
        }

        #table_lotes_filter{
            text-align: left;
        }
        #table_lotes  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            float:left;
            margin: 10px 0;
            white-space: nowrap;
        }

    }

    @media only screen and (min-width: 428px) and (max-width: 1190px) {
        /* Para tables: */
        #modal_lote div.dataTables_filter input {
            width: 175% !important;
            display: inline-block !important;
        }
    }

    @media only screen and (max-width: 428px) {
        /* Para celular: */
        #modal_lote  div.dataTables_filter input {
            width: 100% !important;
            display: inline-block !important;
        }
    }

    @media only screen and (min-width: 1190px) {

        #modal_lote div.dataTables_filter input {
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

    function obtenerLotesproductos(tipo_cliente) {
        try{

            if( $("#tipo_venta").val()=="")
                throw "Elige un tipo de comprobante";
            //RUTA LOTES PRODUCTOS
            var urlInit = `{{ route("ventas.getLot", ['id','tipocomprobante'])}}`;
            urlInit = urlInit.replace('id', tipo_cliente);
            var url = urlInit.replace('tipocomprobante', $("#tipo_venta").val());
        
           console.log(url);
        //ELIMINAR EL DATATABLE PARA VOLVER A INSTANCIARLO
        $(".dataTables-lotes").dataTable().fnDestroy();
        //INSTANCIAR DATATABLE
        var lotes = $('.dataTables-lotes').DataTable({
            "dom":
                    "<'row'<'col-sm-12 col-md-12 col-lg-12'f>>" +
                    "<'row'<'col-sm-12'tr>>"+
                    "<'row justify-content-between'<'col information-content p-0'i><''p>>",

            "bPaginate": true,
            "serverSide":true,
            "processing":true,
            "ajax": url,
            "columns": [
                {data: 'id', className: "text-center letrapequeña", name:"lote_productos.id" ,visible: false, sWidth: '0%'},
                {data: 'nombre', className: "text-left letrapequeña", name:"productos.nombre", sWidth: '35%' },
                {data: 'unidad_producto', className: "text-center letrapequeña", name:"tabladetalles.simbolo", sWidth: '5%' },
                {data: 'categoria', className: "text-center letrapequeña", name:"categorias.descripcion", sWidth: '15%' },
                {data: 'marca', className: "text-center letrapequeña", name:"marcas.marca", sWidth: '5%' },
                {data: 'cantidad_logica', className: "text-center letrapequeña", name:"lote_productos.cantidad_logica", sWidth: '10%' },
                {data: 'codigo_barra', className: "text-center letrapequeña", name:"productos.codigo_barra", sWidth: '15%' },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    sWidth: '10%',
                    searchable: false,
                    render: function(data) {
                        if (data.precio_compra == null) {
                            let cambio = convertFloat(data.dolar_ingreso);
                            let precio = 0;
                            var precio_ = data.precio_ingreso;
                            let porcentaje = 0;
                            let porcentaje_ = convertFloat(data.porcentaje_normal);
                            let precio_nuevo = 0;
                            if(data.moneda_ingreso == 'DOLARES')
                            {
                                precio = precio_ * cambio;
                                precio_nuevo = precio * (1 + (porcentaje_ / 100))
                            }
                            else
                            {
                                precio = precio_;
                                precio_nuevo = precio * (1 + (porcentaje_ / 100))
                            }
                            return convertFloat(precio_nuevo).toFixed(2);
                        }else{
                            let cambio = convertFloat(data.dolar_compra);
                            let precio = 0;
                            var precio_ = data.precio_compra;
                            let porcentaje = 0;
                            let porcentaje_ = data.porcentaje_normal;
                            let precio_nuevo = 0;
                            if(data.moneda_compra == 'DOLARES')
                            {
                                if(data.igv_compra == 1)
                                {
                                    precio = precio_ * cambio;
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                                else
                                {
                                    precio = (precio_ * cambio * 1.18)
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                            }
                            else
                            {
                                if(data.igv_compra == 1)
                                {
                                    precio = precio_;
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                                else
                                {
                                    precio = (precio_ * 1.18)
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                            }
                            return convertFloat(precio_nuevo).toFixed(2);
                        }
                    }
                },
                {
                    data: null,
                    className: "text-center letrapequeña",
                    sWidth: '10%',
                    searchable: false,
                    render: function(data) {
                        if (data.precio_compra == null) {
                            let cambio = convertFloat(data.dolar_ingreso);
                            let precio = 0;
                            var precio_ = data.precio_ingreso;
                            let porcentaje_ = data.porcentaje_distribuidor;
                            let precio_nuevo = 0;
                            if(data.moneda_ingreso == 'DOLARES')
                            {
                                precio = precio_ * cambio;
                                precio_nuevo = precio * (1 + (porcentaje_ / 100))
                            }
                            else
                            {
                                precio = precio_;
                                precio_nuevo = precio * (1 + (porcentaje_ / 100))
                            }
                            return convertFloat(precio_nuevo).toFixed(2);
                        }else{
                            let cambio = convertFloat(data.dolar_compra);
                            let precio = 0;
                            var precio_ = data.precio_compra;
                            let porcentaje = 0;
                            let porcentaje_ = data.porcentaje_distribuidor;
                            let precio_nuevo = 0;
                            if(data.moneda_compra == 'DOLARES')
                            {
                                if(data.igv_compra == 1)
                                {
                                    precio = precio_ * cambio;
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                                else
                                {
                                    precio = (precio_ * cambio * 1.18)
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                            }
                            else
                            {
                                if(data.igv_compra == 1)
                                {
                                    precio = precio_;
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                                else
                                {
                                    precio = (precio_ * 1.18)
                                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                                }
                            }
                            return convertFloat(precio_nuevo).toFixed(2);
                        }
                    }
                },
                @if ($fullaccess)
                {
                    data: null,
                    className: "text-center letrapequeña",
                    searchable: false,
                    sWidth: '5%',
                    render: function(data) {
                        if (data.precio_mas_igv_soles == null) {
                            return data.precio_ingreso_soles;
                        }else{
                            return convertFloat(data.precio_mas_igv_soles).toFixed(2);
                        }
                    }
                },
                @endif
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
                $(row).addClass('fila_lote');
                $(row).attr('data-href', "");
            },


        });
        }catch(ex){
            toastr.error(ex);
        }
    }

    $(document).ready(function() {

        $('buttons-html5').removeClass('.btn-default');
        $('#table_lotes_wrapper').removeClass('');

        $('.dataTables-lotes tbody').on( 'click', 'tr', function () {
                $('.dataTables-lotes').DataTable().$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
        } );

        //DOBLE CLICK EN LOTES
        $ ('.dataTables-lotes'). on ('dblclick', 'tbody td', function () {
            var lote =  $('.dataTables-lotes').DataTable();
            var data = lote.row(this).data();
            ingresarProducto(data)
        });

    })

    function ingresarProducto(producto) {
        
        //LIMPIAR ERRORES AL INGRESAR PRODUCTO LOTE
        limpiarErrores()
        //HABILITAR CAMPOS DEL PRODUCTO
        $('#precio').prop('disabled' , false)
        $('#cantidad').prop('disabled' , false)
        $('#btn_agregar_detalle').prop('disabled' , false)
        //INGRESAR DATOS DEL PRODUCTO A LOS CAMPOS
        $('#precio').val(evaluarPrecioigv(producto))
        //$('#cantidad').val(producto.cantidad_logica)
        $('#producto_unidad').val(producto.unidad_producto)
        $('#producto_json').val(JSON.stringify(producto))
        $('#producto_id').val(producto.id)
        $('#producto_lote').val(producto.nombre+' - '+ producto.codigo_lote)
        //AGREGAR LIMITE A LA CANTIDAD SEGUN EL LOTE SELECCIONADO
        $("#cantidad").attr({
            "max" : producto.cantidad_logica,
            "min" : 1,
        });
        $("#precio").attr({
            "min" : 1,
        });
        setTimeout(function() { $('input[name="cantidad"]').focus() }, 10);
        //LIMPIAR MODAL
        limpiarModallote()
    }

    function evaluarPrecioigv(producto) {
        if (producto.precio_compra == null) {
            let cambio = convertFloat(producto.dolar_ingreso);
            let precio = 0;
            var precio_ = producto.precio_ingreso;
            let porcentaje_ = producto.porcentaje;
            let precio_nuevo = 0;
            if(producto.moneda_ingreso == 'DOLARES')
            {
                precio = precio_ * cambio;
                precio_nuevo = precio * (1 + (porcentaje_ / 100))
            }
            else
            {
                precio = precio_;
                precio_nuevo = precio * (1 + (porcentaje_ / 100))
            }
            return convertFloat(precio_nuevo).toFixed(2);
        }else{
            let cambio = convertFloat(producto.dolar_compra);
            let precio = 0;
            let precio_ = producto.precio_compra;
            let porcentaje_ = producto.porcentaje;
            let precio_nuevo = 0;
            if(producto.moneda_compra == 'DOLARES')
            {
                if(producto.igv_compra == 1)
                {
                    precio = precio_ * cambio;
                    precio_nuevo  = precio * (1 + porcentaje_ / 100)
                }
                else
                {
                    precio = (precio_ * cambio * 1.18)
                    precio_nuevo  = precio * (1 + porcentaje_ / 100)
                }
            }
            else
            {
                if(producto.igv_compra == 1)
                {
                    precio = precio_;
                    precio_nuevo  = precio * (1 + porcentaje_ / 100)
                }
                else
                {
                    precio = (precio_ * 1.18)
                    precio_nuevo  = precio * (1 + porcentaje_ / 100)
                }
            }
            return convertFloat(precio_nuevo).toFixed(2);
        }
    }

    function limpiarModallote() {
        //ACTUALIZAR DATATABLE
        let cliente_id = $("#cliente_id option:selected").attr('tabladetalle')
        obtenerLotesproductos(cliente_id)
        setTimeout(function() {
            $('#modal_lote div.dataTables_filter input').focus();
        }, 10);
        //7$('.dataTables-lotes').DataTable().ajax.reload();
        //CERRAR MODAL
        $('#modal_lote').modal('hide');
    }
    //AL ABRIR EL MODAL SE DEBE DE ACTUALIZAR EL DATATABLE
    $('#modal_lote').on('show.bs.modal', function(e) {
        //ACTUALIZAR DATATABLE
        let cliente_id = $("#cliente_id option:selected").attr('tabladetalle')
        obtenerLotesproductos(cliente_id)
        setTimeout(function() {
            $('#modal_lote div.dataTables_filter input').focus();
        }, 10);
        //$('.dataTables-lotes').DataTable().ajax.reload();
    });


</script>
@endpush
