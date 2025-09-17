<div class="modal fade" id="mdlEditCuenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Cuenta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('mantenimiento.cuentas.forms.form_edit')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success" type="submit" form="formActualizarCuenta">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    let rowEditar = null;

    function eventsMdlEditCuenta() {
        document.querySelector('#formActualizarCuenta').addEventListener('submit', (e) => {
            e.preventDefault();
            actualizarCuenta();
        })

        $('#mdlEditCuenta').on('hidden.bs.modal', function(e) {
            const formActualizarCuenta = document.querySelector('#formActualizarCuenta');
            formActualizarCuenta.reset();
            limpiarErroresValidacion('msgError_edit');
        });

        $('#mdlEditCuenta').on('shown.bs.modal', function(e) {
            document.querySelector('#titular_edit').focus();
        });
    }

    function openMdlEditCuenta(id) {
        rowEditar = getRowById(dtCuentas, id);

        if (!rowEditar) {
            toastr.error('NO SE ENCONTRÓ EL MÉTODO PAGO EN EL DATATABLE');
            return;
        }

        //======== SETTEANDO DATA ========
        document.querySelector('#titular_edit').value       = rowEditar.titular;
        $('#moneda_edit').val(rowEditar.moneda).trigger('change');
        document.querySelector('#nro_cuenta_edit').value    = rowEditar.nro_cuenta;
        document.querySelector('#cci_edit').value           = rowEditar.cci;
        document.querySelector('#celular_edit').value       = rowEditar.celular;
        $('#banco_id_edit').val(rowEditar.banco_id).trigger('change');

        $('#mdlEditCuenta').modal('show');
    }

    function actualizarCuenta() {

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA LA CUENTA BANCARIA?",
            text: `Cuenta: ${rowEditar.nro_cuenta}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, ACTUALIZAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                limpiarErroresValidacion('msgError_edit');
                const token = document.querySelector('input[name="_token"]').value;
                const formActualizarCuenta = document.querySelector('#formActualizarCuenta');
                const formData = new FormData(formActualizarCuenta);
                let urlUpdate = `{{ route('mantenimiento.cuentas.update', ['id' => ':id']) }}`;
                urlUpdate = urlUpdate.replace(':id', rowEditar.id);

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Actualizando cuenta bancaria...',
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
                        dtCuentas.draw();
                        $('#mdlEditCuenta').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR CUENTA BANCARIA');
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
