<div class="modal inmodal" id="modal_editar_orden" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiar()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Producto</h4>
                <small class="font-bold">Modificar Producto.</small>
            </div>
            <div class="modal-body">
                <form role="form" id="">
                    <input type="hidden" id="editar_id_producto">
                    <input type="hidden" id="indice">
                    <input type="hidden" id="editable_lote">
                    @if (!empty($orden))

                        <div class="form-group row">
                            <div class="col-lg-4 col-12">

                                <div class="form-group">
                                    <label class="">Producto</label>
                                    <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                                        name="producto_editar_id" id="producto_id_editar" disabled>
                                        <option></option>
                                        @foreach ($productos as $producto)
                                        <option value="{{$producto->id}}">{{$producto->nombre}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"><b><span id="error-producto_id_editar"></span></b></div>

                                </div>

                            </div>
                                <div class="col-md-4 col-xs-12">
                                    <label class="required" for="amount">Precio</label>
                                    <input type="text" id="precio_editar" class="form-control" readonly>
                                    <div class="invalid-feedback"><b><span id="error-precio_editar"></span></b></div>
                                </div>

                                <div class="col-md-4 col-xs-12">
                                    <label class="required">Costo Flete</label>
                                    <input type="text" id="costo_flete_editar" name="costo_flete_editar" class="form-control">
                                    <div class="invalid-feedback"><b><span id="error-costo_flete_editar"></span></b></div>
                                </div>


                        </div>
                        <hr>
                        <p><b>Ingreso del lote del Producto</b></p>
                        <div class="form-group row">

                            <div class="col-md-4 col-xs-12" id="fecha_vencimiento_campo_editar">
                                <label class="required">Fecha de Vencimiento</label>
                                <div class="input-group date">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input type="text" id="fecha_vencimiento_editar" autocomplete="off" readonly class="form-control">
                                    <div class="invalid-feedback"><b><span id="error-fecha_vencimiento_editar"></span></b></div>
                                </div>

                            </div>

                            <div class="col-md-4 col-xs-12">
                                <label class="required" for="amount">Lote</label>
                                <input type="text" id="lote_editar" class="form-control" onkeypress="return mayus(this);" >
                                <div class="invalid-feedback"><b><span id="error-lote_editar"></span></b></div>
                            </div>

                            <div class="col-md-4 col-xs-12">
                                <label class="required">Cantidad</label>
                                <input type="text" id="cantidad_editar" class="form-control">
                                <div class="invalid-feedback"><b><span id="error-cantidad_editar"></span></b></div>
                            </div>

                        </div>

                        <div id="modalLote">
                            @include('compras.documentos.table-lotProduct')
                        </div>

                    @else

                    <div class="form-group row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="">Producto</label>
                                <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                                    name="producto_editar_id" id="producto_id_editar" disabled>
                                    <option></option>
                                    @foreach ($productos as $producto)
                                    <option value="{{$producto->id}}">{{$producto->nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"><b><span id="error-producto_id_editar"></span></b></div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="required">Costo Flete</label>
                            <input type="text" id="costo_flete_editar" name="costo_flete_editar" class="form-control">
                            <div class="invalid-feedback"><b><span id="error-costo_flete_editar"></span></b></div>
                        </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-md-6">
                            <label class="required">Cantidad</label>
                            <input type="text" id="cantidad_editar" onkeypress="return filterFloat(event, this, false);" class="form-control" @if (!empty($orden)) {{'disabled'}} @endif>
                            <div class="invalid-feedback"><b><span id="error-cantidad_editar"></span></b></div>
                        </div>


                        <div class="col-md-6">
                            <label class="required" for="amount">Precio</label>
                            <input type="text" id="precio_editar" class="form-control" @if (!empty($orden)) {{'disabled'}} @endif>
                            <div class="invalid-feedback"><b><span id="error-precio_editar"></span></b></div>
                        </div>

                    </div>

                    <div class="form-group row d-none">

                        <div class="col-md-6" id="fecha_vencimiento_campo_editar">
                            <label class="required">Fecha de Vencimiento</label>
                            <div class="input-group date">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="text" id="fecha_vencimiento_editar" autocomplete="off" readonly class="form-control">
                                <div class="invalid-feedback"><b><span id="error-fecha_vencimiento_editar"></span></b></div>
                            </div>

                        </div>


                        <div class="col-md-6">
                            <label class="required" for="amount">Lote</label>
                            <input type="text" id="lote_editar" class="form-control" onkeypress="return mayus(this);" >
                            <div class="invalid-feedback"><b><span id="error-lote_editar"></span></b></div>
                        </div>

                    </div>
                    @endif

            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">

                    <a class="btn btn-primary btn-sm" onclick="editarLote()" id='editarLote' style="color:white"><i class="fa fa-save"></i> Guardar</a>
                    <a class="btn btn-primary btn-sm editarRegistro" id='editarRegistro' style="color:white"><i class="fa fa-save"></i> Guardar</a>
                    <button type="button" onclick="limpiar()" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i>
                        Cancelar</button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
$('#cantidad_editar').on('input', function() {
    //this.value = this.value.replace(/[^0-9]/g, '');
    let max = convertFloat(this.max);
    let valor = convertFloat(this.value);
    if(valor>max){
        toastr.error('La cantidad ingresada supera al stock del producto Max('+max+').', 'Error');
        this.value = max;
    }
});
//Validacion al ingresar tablas
$(".editarRegistro").click(function() {
    // limpiarErrores()
    var enviar = false;

    if ($('#precio_editar').val() == '') {

        toastr.error('Ingrese el precio del producto.', 'Error');
        enviar = true;

        $("#precio_editar").addClass("is-invalid");
        $('#error-precio_editar').text('El campo Precio es obligatorio.')
    }

    if ($('#cantidad_editar').val() == '') {
        toastr.error('Ingrese cantidad del producto.', 'Error');
        enviar = true;

        $("#cantidad_editar").addClass("is-invalid");
        $('#error-cantidad_editar').text('El campo Cantidad es obligatorio.')
    }

    if ($('#costo_flete_editar').val() == '') {
        toastr.error('Ingrese el Costo de Flete del producto.', 'Error');
        enviar = true;

        $("#costo_flete_editar").addClass("is-invalid");
        $('#error-costo_flete_editar').text('El campo Costo de Flete es obligatorio.')
    }

    if ($('#fecha_vencimiento_editar').val() == '') {
        toastr.error('Ingrese la Fecha de Vencimiento del Producto.', 'Error');
        enviar = true;

        $("#fecha_vencimiento_editar").addClass("is-invalid");
        $('#error-fecha_vencimiento_editar').text('El campo Fecha de Vencimiento es obligatorio.')
    }

    if ($('#lote_editar').val() == '') {
        toastr.error('Ingrese el Lote del Producto.', 'Error');
        enviar = true;

        $("#lote_editar").addClass("is-invalid");
        $('#error-lote_editar').text('El campo Lote es obligatorio.')
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
                actualizarTabla($('#indice').val())
                sumaTotal()
                limpiar()

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })

    }
})

function actualizarTabla(i) {
    var table = $('.dataTables-orden-detalle').DataTable();
    table.row(i).remove().draw();
    var descripcion_producto = obtenerproducto($('#producto_id_editar').val())
    var presentacion_producto = obtenerPresentacion($('#presentacion_editar').val())
    var detalle = {
        producto_id: $('#producto_id_editar').val(),
        descripcion: descripcion_producto.nombre,//+' - '+$('#lote_editar').val()
        costo_flete : $('#costo_flete_editar').val(),
        precio: $('#precio_editar').val(),
        cantidad: $('#cantidad_editar').val(),
        lote: $('#lote_editar').val(),
        fecha_vencimiento: $('#fecha_vencimiento_editar').val(),
        editable : '1'
    }
    agregarTabla(detalle);

}

function limpiar() {

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

    $('#modal_editar_orden').modal('hide');

}

$('#modal_editar_orden').on('hidden.bs.modal', function(e) {
    limpiar()
});



function registrosproductos() {
    var registros = table.rows().data().length;
    return registros
}



//TABLA AL EXISTIR ORDEN DE COMPRA
function editarLote() {
    var enviar = false;
    if ($('#costo_flete_editar').val() == '') {
        toastr.error('Ingrese el Costo de Flete del Producto.', 'Error');
        enviar = true;

        $("#costo_flete_editar").addClass("is-invalid");
        $('#error-costo_flete_editar').text('El campo Costo de Flete es obligatorio.')
    }

    if (!$('.dataTables-lotes-productos').DataTable().rows().data().length && $('#editable_lote').val()=='') {
        toastr.error('Ingrese Lote , Fecha de Vencimiento y Cantidad del Producto.', 'Error');
        enviar = true;
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
                tablaPrincipal()
                sumaTotal()
                //LIMPIAR LOTES DE LA TABLA LOTES PRODUCTOS
                $('.dataTables-lotes-productos').DataTable().rows().remove().draw();
                //CERRAR DATATABLE
                limpiar()
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

    }
}

function tablaPrincipal() {
    //ELIMINAR EL PRODUCTO QUE LLEGA DE LA ORDEN
    var table = $('.dataTables-orden-detalle').DataTable();
    table.row($('#indice').val()).remove().draw();
    //AGREGAR LOS LOTES DEL producto
    var loteDetalle = $('.dataTables-lotes-productos').DataTable();

    loteDetalle.rows().data().each(function(el, index) {
        table.row.add([
               el[0],
                '',
                el[2],
                el[3],
                el[4],
                el[5],
                el[6],
                el[7],
                el[8],
                el[9],
        ]).draw(false);
    });
}



</script>
@endpush
