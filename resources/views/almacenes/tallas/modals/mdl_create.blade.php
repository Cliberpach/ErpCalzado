<div class="modal inmodal" id="mdlCreateTalla" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Talla</h4>
                <small class="font-bold">Crear nueva Talla.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.tallas.forms.form_create')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function openMdlCreateTalla() {
            $('#mdlCreateTalla').modal('show');
        }

        function eventsMdlCreateTalla() {

            document.querySelector('#formCreateTalla').addEventListener('submit', (e) => {
                e.preventDefault();
                storeTalla(e.target);
            });

            $('#mdlCreateTalla').on('hidden.bs.modal', function() {
                clearFormCreateTalla();
            });
        }

        async function storeTalla(formCreateTalla) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion').value || '';

            Swal.fire({
                title: '¿Desea registrar la talla?',
                html: `Talla: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando talla...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formCreateTalla);

                        const res = await axios.post(route('almacenes.tallas.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            dtTallas.ajax.reload();
                            $('#mdlCreateTalla').modal('hide');
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

                } else {
                    Swal.fire('Cancelado', 'La solicitud se ha cancelado.', 'error');
                }

            });
        }

        function clearFormCreateTalla() {
            const form = document.getElementById('formCreateTalla');

            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
        }
    </script>
@endpush
