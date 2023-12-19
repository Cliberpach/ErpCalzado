<div class="form-group">
    <a class="btn btn-block btn-warning" onclick="agregarLote()" style='color:white;'> <i class="fa fa-plus"></i> AGREGAR</a>
</div>

<div class="form-group">
    <div class="table-responsive m-t">
        <table class="table dataTables-lotes-productos table-bordered" style="width:100%; text-transform:uppercase;" id="table_lotes">
            <thead>
                <tr>
                    <th></th>
                    <th class="text-center">ACCIONES</th>
                    <!-- CANTIDAD -->
                    <th class="text-center">CANTIDAD</th>
                    <!-- PRODUCTO -->
                    <th></th>
                    <!-- FECHA DE VENCIMIENTO -->
                    <th class="text-center">FECHA. VENC</th>
                    <!-- COSTO DE FLETE -->
                    <th></th>
                    <!-- PRECIO -->
                    <th></th>
                    <!-- TOTAL -->
                    <th></th>
                    <!-- LOTES -->
                    <th class="text-center">LOTE</th>
                    <!-- EDITABLE LOTE -->
                    <th></th>

                </tr>
            </thead>
            <tbody>

            </tbody>

        </table>
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
<script src="{{asset('Inspinia/js/plugins/dataTables/dataTables.bootstrap4.min.js')}}"></script>

<script>
    //INICIAR DATATABLE LOTE
    $(document).ready(function() {
        loteDetalle = $('.dataTables-lotes-productos').DataTable({
            "bAutoWidth": false,
            "bInfo": false,
            "bAutoWidth": false,
            "bPaginate": false,
            "bFilter": false,
            "bLengthChange": false,
            "language": {
                "url": "{{asset('Spanish.json')}}"
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                },

                {
                    "targets": [1],
                    className: "text-center",
                    render: function(data, type, row) {
                            return "<div class='btn-group'>" +
                                "<a class='btn btn-danger btn-sm borrar_lote' style='color:white;' title='Eliminar'><i class='fa fa-trash'></i></a>" +
                                "</div>";
                            }
                },
                // CANTIDAD
                {
                    "targets": [2],
                    className: "text-center",
                },
                //PRODUCTO
                {
                    "targets": [3],
                    "visible": false,
                    "searchable": false
                },
                //FECHA DE VENCIMIENTO
                {
                    "targets": [4],
                    className: "text-center",
                },
                //COSTO DE FLETE
                {
                    "targets": [5],
                    className: "text-center",
                    "visible": false,
                    "searchable": false
                },
                //PRECIO
                {
                    "targets": [6],
                    className: "text-center",
                    "visible": false,
                    "searchable": false
                },
                //TOTAL
                {
                    "targets": [7],
                    className: "text-center",
                    "visible": false,
                    "searchable": false
                },
                //LOTE
                {
                    "targets": [8],
                    className: "text-center",
                },
                //LOTE EDITABLE
                {
                    "targets": [9],
                    className: "text-center",
                    "visible": false,
                    "searchable": false
                },

            ]
        });
    })
    //VALIDAR CAMPOS PARA NO LOGRAR QUE SEAN NULOS 
    function agregarLote() {
        limpiarLote()
        var enviar = false;
        if ($('#fecha_vencimiento_editar').val() == '') {
            toastr.error('Ingrese la Fecha de Vencimiento del Artículo.', 'Error');
            enviar = true;

            $("#fecha_vencimiento_editar").addClass("is-invalid");
            $('#error-fecha_vencimiento_editar').text('El campo Fecha de Vencimiento es obligatorio.')
        }

        if ($('#lote_editar').val() == '') {
            toastr.error('Ingrese el Lote del Artículo.', 'Error');
            enviar = true;

            $("#lote_editar").addClass("is-invalid");
            $('#error-lote_editar').text('El campo Lote es obligatorio.')
        }

        if ($('#cantidad_editar').val() == '') {
            toastr.error('Ingrese cantidad del Artículo.', 'Error');
            enviar = true;

            $("#cantidad_editar").addClass("is-invalid");
            $('#error-cantidad_editar').text('El campo Cantidad es obligatorio.')
        }

        if ($('#costo_flete_editar').val() == '') {
            toastr.error('Ingrese el Costo de Flete del Artículo.', 'Error');
            enviar = true;

            $("#costo_flete_editar").addClass("is-invalid");
            $('#error-costo_flete_editar').text('El campo Costo de Flete es obligatorio.')
        
        }

        if (enviar != true) {
            Swal.fire({
                customClass: {
                    container: 'my-swal'
                },
                title: 'Opción Agregar',
                text: "¿Seguro que desea agregar Producto?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {

                    var descripcion_producto = obtenerproducto($('#producto_id_editar').val())
                    var detalle = {
                        producto_id: $('#producto_id_editar').val(),
                        descripcion: descripcion_producto.nombre+' - '+$('#lote_editar').val(),
                        costo_flete : $('#costo_flete_editar').val(), 
                        precio: $('#precio_editar').val(),
                        cantidad: $('#cantidad_editar').val(),
                        lote: $('#lote_editar').val(),
                        fecha_vencimiento: $('#fecha_vencimiento_editar').val(),
                    }
                    
                    validarLote(detalle)
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire({
                        customClass: {
                            container: 'my-swal'
                            },
                    
                        title: 'Cancelado',
                        text: 'La Solicitud se ha cancelado.',
                        icon: 'error',
                        confirmButtonColor: "#1a7bb9",
                        })
                }
            })

        }


    }
    //TABLA VACIO INGRESO DEL PRIMERO LOTE SINO INGRESOS DE LOTES
    function validarLote(detalle) {
        var ingreso =  true;
        var loteDetalle = $('.dataTables-lotes-productos').DataTable();
        if ( ! loteDetalle.data().any() ) {
            insertarLote(detalle)
            ingreso = false;
        }else{
            loteDetalle.rows().data().each(function(el, index) {
                if(el[2] == detalle.cantidad && el[5] == detalle.fecha_vencimiento && el[9] == detalle.lote ){
                    toastr.error('Registro Existente.', 'Error'); 
                    ingreso = false;
                }
            });
        }
        //LOTES UNICOS
        if(ingreso == true) {insertarLote(detalle)}
    }
    //ELIMINAR LOTE DE TABLA
    $(document).on('click', '.borrar_lote', function(event) {
        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Eliminar',
            text: "¿Seguro que desea eliminar Lote del Artículo?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $('.dataTables-lotes-productos').DataTable().row($(this).parents('tr')).remove().draw();
            } else if (
                result.dismiss === Swal.DismissReason.cancel
            ) {
                Swal.fire(
                    {
                        customClass: {
                            container: 'my-swal'
                            },
                    
                        title: 'Cancelado',
                        text: 'La Solicitud se ha cancelado.',
                        icon: 'error',
                        confirmButtonColor: "#1a7bb9",
                    })
            }
        })
    });
    //INGRESO DE LOTE A TABLA
    function insertarLote(detalle) {
        var loteDetalle = $('.dataTables-lotes-productos').DataTable();
        loteDetalle.row.add([
                detalle.producto_id,
                '',
                detalle.cantidad,
                detalle.descripcion,
                detalle.fecha_vencimiento,
                detalle.costo_flete,
                detalle.precio,
                (detalle.cantidad * detalle.precio).toFixed(2),
                detalle.lote,
                '1',
            ]).draw(false);
    }
    // LIMPIAR ERRORES FORMULARIO LOTE
    function limpiarLote() {
        $('#cantidad_editar').removeClass("is-invalid")
        $('#error-cantidad_editar').text('')

        $("#precio_editar").removeClass("is-invalid");
        $('#error-precio_editar').text('')

        $("#costo_flete_editar").removeClass("is-invalid");
        $('#error-costo_flete_editar').text('')

        $('#lote_editar').removeClass("is-invalid")
        $('#error-lote_editar').text('')

        $("#fecha_vencimiento_editar").removeClass("is-invalid");
        $('#error-fecha_vencimiento_editar').text('')
    }

</script>
@endpush