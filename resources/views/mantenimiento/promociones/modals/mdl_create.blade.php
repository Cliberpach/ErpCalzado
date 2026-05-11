<div class="modal inmodal" id="mdlCreatePromocion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>

                <i class="fa fa-tags modal-icon"></i>

                <h4 class="modal-title">Promoción</h4>
                <small class="font-bold">Crear nueva promoción.</small>
            </div>

            <div class="modal-body">
                @include('mantenimiento.promociones.forms.form_create')
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script>
        const paramsMdlCreatePromocion = {}

        function openMdlCreatePromocion() {
            $('#mdlCreatePromocion').modal('show');
        }

        function eventsMdlCreatePromocion() {

            document.querySelector('#formCreatePromocion').addEventListener('submit', (e) => {
                e.preventDefault();
                storePromocion(e.target);
            });

            $('#mdlCreatePromocion').on('hidden.bs.modal', function() {
                clearFormCreatePromocion();
            });

        }

        async function storePromocion(formCreatePromocion) {

            toastr.clear();

            const nombre = document.querySelector('#nombre').value || '';

            Swal.fire({
                title: '¿Desea registrar la promoción?',
                html: `Promoción: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#0d6efd",
                confirmButtonText: 'Sí',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando promoción...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formCreatePromocion);

                        const res = await axios.post(
                            '{{ route('mantenimiento.promociones.store') }}',
                            formData
                        );

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            dtPromociones.ajax.reload();
                            $('#mdlCreatePromocion').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'Error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'error');
                                toastr.error('Errores de validación encontrados', 'Error de validación');
                            } else {
                                toastr.error(error.response.data.message, 'Error en el servidor');
                            }

                        } else if (error.request) {
                            toastr.error('No se pudo contactar al servidor', 'Error de conexión');
                        } else {
                            toastr.error(error.message, 'Error desconocido');
                        }

                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La solicitud se ha cancelado.',
                        'error'
                    );
                }

            });

        }

        function clearFormCreatePromocion() {

            const form = document.getElementById(
                'formCreatePromocion'
            );

            form.reset();

            form.querySelectorAll('.msgError')
                .forEach(el => {

                    el.textContent = '';
                });

            document.getElementById('tipo_promocion').value =
                'descuento_fijo';

            changePromotionUI();
        }
    </script>
@endpush
