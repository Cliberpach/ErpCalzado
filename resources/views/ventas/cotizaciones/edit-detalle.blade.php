<div class="modal inmodal" id="modal_editar_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  onclick="limpiar()" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Detalle de la cotización</h4>
                <small class="font-bold">Editar detalle</small>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_editar" name="id_editar">
                <input type="hidden" id="medida_editar" name="medida_editar">
                <input type="hidden" id="indice" name="indice">
                <input type="hidden" id="codigo_nombre_producto_editar" name="codigo_nombre_producto_editar">

                <div class="form-group">
                    <label class="">Producto</label>
                    <select id="producto_editar" name="producto_editar" class="select2_form form-control" disabled>
                        <option></option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}">{{ $lote->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="">Unidad de Medida</label>
                    <input type="text" id="presentacion_producto_editar" name="presentacion_producto_editar" class="form-control" disabled>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6 col-xs-12">
                        <label class="required">Cantidad</label>
                        <input type="text" id="cantidad_editar" name="cantidad_editar" class="form-control" maxlength="10" onkeypress="return isNumber(event);" required>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <label class="required">Precio</label>
                        <input type="text" id="precio_editar" name="precio_editar" class="form-control" maxlength="15" onkeypress="return filterFloat(event, this, true);" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" id="btn_editar_detalle" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button"  onclick="limpiar()" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
//Validacion al ingresar tablas
$("#btn_editar_detalle").click(function() {
    // limpiarErrores()
    var enviar = false;

    if ($('#precio_editar').val() == '') {

        toastr.error('Ingrese el precio del Producto.', 'Error');
        enviar = true;

        $("#precio_editar").addClass("is-invalid");
        $('#error-precio_editar').text('El campo Precio es obligatorio.')
    }

    if ($('#cantidad_editar').val() == '') {
        toastr.error('Ingrese cantidad del Producto.', 'Error');
        enviar = true;

        $("#cantidad_editar").addClass("is-invalid");
        $('#error-cantidad_editar').text('El campo Cantidad es obligatorio.')
    }

    if (enviar != true) {
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
    var table = $('.dataTables-detalle-cotizacion').DataTable();
    table.row(i).remove().draw();

    var detalle = {
        producto_id: $('#producto_editar').val(),
        producto: $('#codigo_nombre_producto_editar').val(),
        presentacion:  $('#medida_editar').val(),
        precio: $('#precio_editar').val(),
        cantidad: $('#cantidad_editar').val(),
    }

    agregarTabla(detalle);

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
