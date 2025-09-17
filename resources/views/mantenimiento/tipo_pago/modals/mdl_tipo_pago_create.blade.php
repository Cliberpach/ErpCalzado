<div class="modal fade" id="mdlCreateMetodoPago" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar Tipo de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('mantenimiento.tipo_pago.forms.form_create')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-success btnRegistrarCargo" type="submit" form="formRegistrarMetodoPago">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>

        </div>
    </div>
</div>


<script>
    function eventsMdlCreateMetodoPago() {
        document.querySelector('#formRegistrarMetodoPago').addEventListener('submit', (e) => {
            e.preventDefault();
            registrarMetodoPago();
        })

        $('#mdlCreateMetodoPago').on('hidden.bs.modal', function(e) {
            const formRegistrarMetodoPago = document.querySelector('#formRegistrarMetodoPago');
            formRegistrarMetodoPago.reset();
            limpiarErroresValidacion('msgError');
        });

        $('#mdlCreateMetodoPago').on('shown.bs.modal', function(e) {
            document.querySelector('#descripcion').focus();
        });

    }

    function openMdlNuevoMetodoPago() {
        $('#mdlCreateMetodoPago').modal('show');
    }

    function registrarMetodoPago() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA REGISTRAR EL MÉTODO DE PAGO?",
            text: "Se creará un nuevo método de pago!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, REGISTRAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                limpiarErroresValidacion('msgError');
                const token = document.querySelector('input[name="_token"]').value;
                const formRegistrarMetodoPago = document.querySelector('#formRegistrarMetodoPago');
                const formData = new FormData(formRegistrarMetodoPago);
                const urlRegistrarMetodoPago = @json(route('mantenimiento.tipo_pago.store'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando nuevo método de pago...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlRegistrarMetodoPago, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        dtMetodosPago.draw();
                        $('#mdlCreateMetodoPago').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR MÉTODO PAGO');
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
