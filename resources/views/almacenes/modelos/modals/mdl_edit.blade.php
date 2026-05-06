<div class="modal inmodal" id="mdlEditModelo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Modelo</h4>
                <small class="font-bold">Editar Modelo.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.modelos.forms.form_edit')
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        const paramsmdlEditModelo = {
            id: null,
            row: null
        };

        function openMdlEditModelo(id) {
            paramsmdlEditModelo.id = id;

            const item = getRowById(dtModelos, id);
            paramsmdlEditModelo.row = item;

            setFormEditModelo();

            $('#mdlEditModelo').modal('show');
        }

        function eventsMdlEditModelo() {

            document.querySelector('#formEditModelo').addEventListener('submit', (e) => {
                e.preventDefault();
                updateModelo(e.target);
            });

            $('#mdlEditModelo').on('hidden.bs.modal', function() {
                clearFormEditModelo();
            });
        }

        function setFormEditModelo() {
            document.querySelector('#descripcion_edit').value = paramsmdlEditModelo.row.descripcion;
        }

        async function updateModelo(formEditModelo) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion_edit').value || '';

            Swal.fire({
                title: '¿Desea actualizar el modelo?',
                html: `Modelo: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Actualizando modelo...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formEditModelo);
                        formData.append('_method', 'PUT');

                        const res = await axios.post(
                            route('almacenes.modelos.update', {
                                id: paramsmdlEditModelo.id
                            }),
                            formData
                        );

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            dtModelos.ajax.reload();
                            $('#mdlEditModelo').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'edit_error');
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
                    Swal.fire(
                        'Cancelado',
                        'La solicitud se ha cancelado.',
                        'error'
                    );
                }

            });
        }

        // 🔥 limpiar form
        function clearFormEditModelo() {

            const form = document.getElementById('formEditModelo');

            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });

            paramsmdlEditModelo.id = null;
            paramsmdlEditModelo.row = null;
        }
    </script>
@endpush
