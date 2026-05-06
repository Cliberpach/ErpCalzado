<div class="modal inmodal" id="mdlEditTalla" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Talla</h4>
                <small class="font-bold">Editar Talla.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.tallas.forms.form_edit')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const paramsmdlEditTalla = {
            id: null,
            row: null
        };

        function openMdlEditTalla(id) {
            paramsmdlEditTalla.id = id;

            const item = getRowById(dtTallas, id);
            paramsmdlEditTalla.row = item;

            setFormEditTalla();

            $('#mdlEditTalla').modal('show');
        }

        function eventsMdlEditTalla() {

            document.querySelector('#formEditTalla').addEventListener('submit', (e) => {
                e.preventDefault();
                updateTalla(e.target);
            });

            $('#mdlEditTalla').on('hidden.bs.modal', function() {
                clearFormEditTalla();
            });
        }

        function setFormEditTalla() {
            document.querySelector('#descripcion_edit').value = paramsmdlEditTalla.row.descripcion;
        }

        async function updateTalla(formEditTalla) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion_edit').value || '';

            Swal.fire({
                title: '¿Desea actualizar la talla?',
                html: `Talla: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Actualizando talla...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formEditTalla);
                        formData.append('_method', 'PUT');

                        const res = await axios.post(
                            route('almacenes.tallas.update', {
                                id: paramsmdlEditTalla.id
                            }),
                            formData
                        );

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            dtTallas.ajax.reload();
                            $('#mdlEditTalla').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'Error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'edit_error');
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
                    Swal.fire(
                        'Cancelado',
                        'La solicitud se ha cancelado.',
                        'error'
                    );
                }

            });
        }

        function clearFormEditTalla() {

            const form = document.getElementById('formEditTalla');

            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });

            paramsmdlEditTalla.id = null;
            paramsmdlEditTalla.row = null;
        }
    </script>
@endpush
