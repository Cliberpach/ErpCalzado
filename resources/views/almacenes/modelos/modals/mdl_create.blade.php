<div class="modal inmodal" id="mdlCreateModelo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Modelo</h4>
                <small class="font-bold">Crear nuevo Modelo.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.modelos.forms.form_create')
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function openMdlCreateModelo() {
            $('#mdlCreateModelo').modal('show');
        }

        function eventsMdlCreateModelo() {

            document.querySelector('#formCreateModelo').addEventListener('submit', (e) => {
                e.preventDefault();
                storeModelo(e.target);
            });

            $('#mdlCreateModelo').on('hidden.bs.modal', function() {
                clearFormCreateModelo();
            });
        }

        async function storeModelo(formCreateModelo) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion').value || '';

            Swal.fire({
                title: 'Desea registrar el modelo?',
                html: `Modelo: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando modelo...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formCreateModelo);

                        const res = await axios.post(route('almacenes.modelos.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            dtModelos.ajax.reload();
                            $('#mdlCreateModelo').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'error');
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

        function clearFormCreateModelo() {
            const form = document.getElementById('formCreateModelo');
            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
        }
    </script>
@endpush
