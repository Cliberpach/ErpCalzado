<div class="modal inmodal" id="modal_editar_orden" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
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
                    <div class="form-group">
                        <label class="required">Producto</label>
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

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="required" for="amount">Precio</label>
                            <input type="text" id="precio_editar" class="form-control">
                            <div class="invalid-feedback"><b><span id="error-precio_editar"></span></b></div>
                        </div>

                        <div class="col-md-6">
                            <label class="required">Cantidad</label>
                            <input type="text" id="cantidad_editar" class="form-control">
                            <div class="invalid-feedback"><b><span id="error-cantidad_editar"></span></b></div>
                        </div>



                    </div>

            </div>


            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary btn-sm editarRegistro" style="color:white"><i class="fa fa-save"></i>
                        Guardar</a>
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
//Validacion al ingresar tablas
$(".editarRegistro").click(function() {
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
    var detalle = {
        producto_id: $('#producto_id_editar').val(),
        descripcion: descripcion_producto,
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

    $('#modal_editar_orden').modal('hide');
}

$('#modal_editar_orden').on('hidden.bs.modal', function(e) {
    limpiar()
});
</script>
@endpush