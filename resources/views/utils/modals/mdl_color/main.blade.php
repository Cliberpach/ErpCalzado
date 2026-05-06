<div class="modal inmodal" id="mdlCreateColor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Color</h4>
                <small class="font-bold">Crear nuevo Color.</small>
            </div>
            <div class="modal-body">
                @include('utils.modals.mdl_color.forms.form_create')
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        const configMdlColor = {
            dtColores: null
        }

        function loadMdlColor(config) {
            configMdlColor.dtColores = config.dtColores;
            eventsMdlColor();
        }

        function openMdlColor() {
            $('#mdlCreateColor').modal('show');
        }

        function eventsMdlColor() {
            document.querySelector('#formCreateColor').addEventListener('submit', (e) => {
                e.preventDefault();
                storeColor(e.target);
            });

            $('#mdlCreateColor').on('hidden.bs.modal', function() {
                clearFormCreateColor();
            });
        }

        async function storeColor(formCreateColor) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion_color').value || '';

            Swal.fire({
                title: 'Desea registrar el color?',
                html: `Color: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando color...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formCreateColor);

                        const res = await axios.post(route('almacenes.colores.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            addDtColor(res.data.data);
                            $('#mdlCreateColor').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'color_error');
                                toastr.error('errores de validación encontrados', 'error de validación');
                            } else {
                                toastr.error(error.response.data.message, 'error en el servidor');
                            }

                        } else if (error.request) {
                            toastr.error('no se pudo contactar al servidor', 'error de conexión');
                        } else {
                            toastr.error(error.message, 'error desconocido');
                        }

                    } finally {
                        Swal.close();
                    }

                } else {
                    Swal.fire('Cancelado', 'La solicitud se ha cancelado.', 'error');
                }

            });
        }

        function clearFormCreateColor() {
            const form = document.getElementById('formCreateColor');

            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
        }

        function addDtColor(instance) {
            configMdlColor.dtColores.row.add(
                [`<div style="text-align: left;font-weight:bold;">${instance.id}</div>`,
                    `
                    <div class="form-check">
                        <input class="form-check-input checkColor" type="checkbox" value="" id="checkColor_${instance.id}"
                        data-color-id="${instance.id}">
                        <label class="form-check-label" for="checkColor_${instance.id}">
                            ${instance.descripcion}
                        </label>
                    </div>
                 `
                ]
            ).draw();
        }
    </script>
@endpush
