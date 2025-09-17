<div class="modal fade" id="mdlEditMetodoPago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar método de pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('mantenimiento.tipo_pago.forms.form_edit')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success btnActualizar" type="submit" form="formActualizarMetodoPago">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    let rowEditar = null;

    function eventsMdlEditMetodoPago() {
        document.querySelector('#formActualizarMetodoPago').addEventListener('submit', (e) => {
            e.preventDefault();
            actualizarMetodoPago();
        })

        $('#mdlEditMetodoPago').on('hidden.bs.modal', function(e) {
            const formActualizarMetodoPago = document.querySelector('#formActualizarMetodoPago');
            formActualizarMetodoPago.reset();
            limpiarErroresValidacion('msgError_edit');
        });

        $('#mdlEditMetodoPago').on('shown.bs.modal', function(e) {
            document.querySelector('#descripcion_edit').focus();
        });
    }

    function openMdlEditMetodoPago(id) {
        rowEditar = getRowById(dtMetodosPago, id);

        if (!rowEditar) {
            toastr.error('NO SE ENCONTRÓ EL MÉTODO PAGO EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#descripcion_edit').value   = rowEditar.nombre;

        $('#mdlEditMetodoPago').modal('show');
    }

    function actualizarMetodoPago() {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA ACTUALIZAR EL MÉTODO PAGO?",
            text: `Método pago: ${rowEditar.nombre}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, ACTUALIZAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                limpiarErroresValidacion('msgError_edit');
                const token = document.querySelector('input[name="_token"]').value;
                const formActualizarMetodoPago = document.querySelector('#formActualizarMetodoPago');
                const formData = new FormData(formActualizarMetodoPago);
                let urlUpdate = `{{ route('mantenimiento.tipo_pago.update', ['id' => ':id']) }}`;
                urlUpdate = urlUpdate.replace(':id', rowEditar.id);

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Actualizando método pago...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlUpdate, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: formData
                    });

                    const res = await response.json();

                    console.log(res);

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors,'edit_error')
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        dtMetodosPago.draw();
                        $('#mdlEditMetodoPago').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR MÉTODO DE PAGO');
                    Swal.close();
                }


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "OPERACIÓN CANCELADA",
                    text: "NO SE REALIZARON ACCIONES",
                    icon: "error"
                });
            }
        });
    }
</script>
