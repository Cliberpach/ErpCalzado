<div class="modal inmodal" id="modal_editar_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  onclick="limpiar()" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Detalle del documento de venta</h4>
                <small class="font-bold">Editar detalle</small>
            </div>
            <div class="modal-body">
                <form id="edit_detalle_venta">
                    <input type="hidden" id="id_editar" name="id_editar">
                    <input type="hidden" id="presentacion_producto_editar" name="presentacion_producto_editar">
                    <input type="hidden" id="indice" name="indice">
                    <input type="hidden" id="codigo_nombre_producto_editar" name="codigo_nombre_producto_editar">

                    <div class="form-group">
                        <label class="col-form-label required">Producto-lote:</label>
                        <input type="text" class="form-control" id="producto_lote_editar" name="producto_lote_editar" readonly>
                        <input type="hidden" class="form-control" id="producto_editar" name="producto_editar">
                    </div>
                    <div class="form-group">
                        <label class="">Unidad de Medida</label>
                        <input type="text" id="medida_editar" name="medida_editar" class="form-control" disabled>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-6 col-xs-12">
                            <label class="required">Cantidad</label>
                            <input type="text" id="cantidad_editar" name="cantidad_editar" class="form-control" min="0" onkeypress="return filterFloat(event, this, false);" required>
                            <input type="hidden" id="cantidad_editar_actual" name="cantidad_editar_actual" class="form-control">
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <label class="required">Precio</label>
                            <input type="text" id="precio_editar" name="precio_editar" class="form-control" maxlength="15" onkeypress="return filterFloat(event, this, true);" required>
                            <input type="hidden" id="precio_minimo" name="precio_minimo" class="form-control" maxlength="15" onkeypress="return filterFloat(event, this, true);" required>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" id="btn_editar_detalle" form="edit_detalle_venta" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button"  onclick="limpiar()" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>

$('#cantidad_editar').on('input', function() {
    let max= convertFloat(this.max);
    let valor = convertFloat(this.value);
    if(valor>max){
        toastr.error('La cantidad ingresada supera al stock del producto Max('+max+').', 'Error');
        this.value = max;
    }
});

//Validacion al ingresar tablas
$('#edit_detalle_venta').submit(function(e) {    
    e.preventDefault();
    agregarDetalleEditar('VISTA');
})

function agregarDetalleEditar(condicion)
{
    var enviar = true;

    // if ($('#precio_editar').val() == '') {

    //     toastr.error('Ingrese el precio del Producto.', 'Error');
    //     enviar = true;

    //     $("#precio_editar").addClass("is-invalid");
    //     $('#error-precio_editar').text('El campo Precio es obligatorio.')
    // }
    var codigo = JSON.parse($('#codigo_precio_menor_json').val());
    if ($('#precio_editar').val() == '') {
            toastr.error('Ingrese el precio del producto.', 'Error');
            enviar = false;
            $("#precio_editar").addClass("is-invalid");
            $('#error-precio_editar').text('El campo Precio es obligatorio.')
    } else {
        if ($('#precio_editar').val() == 0) {
            toastr.error('Ingrese el precio del producto superior a 0.0.', 'Error');
            enviar = false;
            $("#precio_editar").addClass("is-invalid");
            $('#error-precio_editar').text('El campo precio debe ser mayor a 0.')
        }

        if(convertFloat($('#precio_editar').val()) < convertFloat($('#precio_minimo').val()))
        {
            if(codigo.estado_precio_menor == '1')
            {
                if($('#codigo_precio_menor_editar').val() != codigo.codigo_precio_menor)
                {
                    if(condicion == 'MODAL')
                    {
                        toastr.error('El codigo para poder vender a un precio menor a lo establecido es incorrecto.', 'Error');
                    }
                    else {
                        $('#codigo_precio_menor_editar').val('');
                        $('#modal-codigo-precio-editar').modal('show')
                    }
                    enviar = false;
                } else {
                    $('#modal-codigo-precio-editar').modal('hide');
                }
            }
            else{
                toastr.error('No puedes vender a un precio menor a lo establecido.', 'Error');
                enviar = false;
            }
        }
    }

    if ($('#cantidad_editar').val() == '') {
        toastr.error('Ingrese cantidad del Producto.', 'Error');
        enviar = false;

        $("#cantidad_editar").addClass("is-invalid");
        $('#error-cantidad_editar').text('El campo Cantidad es obligatorio.')
    }

    if ($('#cantidad_editar').val() == 0) {
        toastr.error('Ingrese cantidad del Producto mayor a 0.', 'Error');
        enviar = false;

        $("#cantidad_editar").addClass("is-invalid");
        $('#error-cantidad_editar').text('Cantidad mayor a 0.')
    }

    let cantidad_res =  $('#cantidad_editar').val();
    let cantidad_sum =  $('#cantidad_editar_actual').val();
    let lote_id = $('#producto_editar').val();

    if(cantidad_res != '' && cantidad_sum != '' && lote_id != '')
    {
        $.ajax({
            type : 'POST',
            url : '{{ route('ventas.documento.update.lote') }}',
            data : {
                '_token' : $('input[name=_token]').val(),
                'lote_id' : lote_id,
                'cantidad_res' : cantidad_res,
                'cantidad_sum' : cantidad_sum,
            }
        }).done(function (response){
            if(!response.success)
            {
                enviar = false;
                toastr.warning('Ocurrió un error porfavor recargar la pagina.')
            }
        });
    }else{
        toastr.error('Cerrar ventana y volver a editar producto.', 'Error');
        enviar = false;
    }

    if (enviar) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
                container: 'my-swal',
            },
            buttonsStyling: false
        })

        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Modificar',
            text: "¿Seguro que desea Modificar Producto?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $('#codigo_precio_menor_editar').val('');
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
}

function actualizarTabla(i) {
    var table = $('.dataTables-detalle-documento').DataTable();
    table.row(i).remove().draw();

    let pdescuento = 0;
    let precio_inicial = convertFloat($('#precio_editar').val());
    let igv = convertFloat($('#igv').val());
    let igv_calculado = convertFloat(igv / 100);

    let valor_unitario = 0.00;
    let precio_unitario = 0.00;
    let dinero = 0.00;
    let precio_nuevo = 0.00;
    let valor_venta = 0.00;
    let cantidad = convertFloat($('#cantidad_editar').val());

    precio_unitario = precio_inicial;
    valor_unitario = precio_unitario / (1 + igv_calculado);
    dinero = precio_unitario * (pdescuento / 100);
    precio_nuevo = precio_unitario - dinero;
    valor_venta = precio_nuevo * cantidad;

    let detalle = {
                producto_id: $('#producto_editar').val(),
                unidad: $('#medida_editar').val(),
                producto: $('#codigo_nombre_producto_editar').val(),
                precio_unitario: precio_unitario,
                valor_unitario: valor_unitario,
                valor_venta: valor_venta,
                cantidad: cantidad,
                precio_inicial: precio_inicial,
                dinero: dinero,
                descuento: pdescuento,
                precio_nuevo: precio_nuevo,
                precio_minimo: $('#precio_minimo').val()
            }
    agregarTabla(detalle);
    sumaTotal()
}

function limpiar() {

    $('#cantidad_editar').removeClass("is-invalid")
    $('#error-cantidad_editar').text('')

    $("#precio_editar").removeClass("is-invalid");
    $('#error-precio_editar').text('')

    $('#modal_editar_detalle').modal('hide');
}

$('#modal_editar_detalle').on('hidden.bs.modal', function(e) {
    limpiar()
});
</script>
@endpush
