<div class="modal inmodal" id="mdlEditBrand" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Marca</h4>
                <small class="font-bold">Editar Marca.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.marcas.forms.form_edit')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const paramsmdlEditBrand = {
            id: null,
            row: null
        };

        function openMdlEditBrand(id) {
            paramsmdlEditBrand.id = id;

            const item = getRowById(dtMarcas, id);
            paramsmdlEditBrand.row = item;

            setFormEditBrand();

            $('#mdlEditBrand').modal('show');
        }

        function eventsMdlEditBrand() {
            document.querySelector('#formEditBrand').addEventListener('submit', (e) => {
                e.preventDefault();
                updateBrand(e.target);
            });

            $('#mdlEditBrand').on('hidden.bs.modal', function() {
                clearFormEditBrand();
            });
        }

        function setFormEditBrand() {
            document.querySelector('#descripcion_edit').value = paramsmdlEditBrand.row.marca;
            document.querySelector('#procedencia_edit').value = paramsmdlEditBrand.row.procedencia;
        }

        async function updateBrand(formEditBrand) {

            toastr.clear();

            const nombre = document.querySelector('#descripcion_edit').value || '';

            Swal.fire({
                title: '¿Desea actualizar la marca?',
                html: `Marca: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Actualizando marca...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formEditBrand);
                        formData.append('_method', 'PUT');

                        const res = await axios.post(
                            route('almacenes.marcas.update', {
                                id: paramsmdlEditBrand.id
                            }),
                            formData
                        );

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            dtMarcas.ajax.reload();
                            $('#mdlEditBrand').modal('hide');
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

        function clearFormEditBrand() {
            const form = document.getElementById('formEditBrand');

            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });

            paramsmdlEditBrand.id = null;
            paramsmdlEditBrand.row = null;
        }
    </script>
@endpush
