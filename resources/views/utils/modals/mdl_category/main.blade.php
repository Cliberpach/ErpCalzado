<div class="modal inmodal" id="mdlCreateCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Categoría</h4>
                <small class="font-bold">Crear nueva Categoría.</small>
            </div>
            <div class="modal-body">
                @include('utils.modals.mdl_category.forms.form_create')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const configMdlCategory = {
            categorySelect: null
        }

        function loadMdlCategory(config) {
            configMdlCategory.categorySelect = config.categorySelect;
            evenstMdlCreateCategory();
        }

        function openMdlCategory() {
            $('#mdlCreateCategoria').modal('show');
        }

        function evenstMdlCreateCategory() {
            document.querySelector('#formCreateCategoria').addEventListener('submit', (e) => {
                e.preventDefault();
                storeCategory(e.target);
            })

            $('#mdlCreateCategoria').on('hidden.bs.modal', function() {
                clearFormCreateCategory();
            });
        }

        async function storeCategory(formCreateAlmacen) {

            toastr.clear();

            const name = document.querySelector('#descripcion_mdlcategory').value || '';

            Swal.fire({
                title: 'Desea registrar la categoría?',
                html: `Categoría: <b>${name}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando categoría...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {

                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(formCreateAlmacen);

                        const res = await axios.post(route('almacenes.categorias.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            addCategory(res.data.data);
                            $('#mdlCreateCategoria').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'mdlcategory_error');
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

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La solicitud se ha cancelado.',
                        'error'
                    );
                }

            });
        }

        function clearFormCreateCategory() {
            const form = document.getElementById('formCreateCategoria');
            form.reset();
            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
        }

        function addCategory(instance) {
            const item = {
                id: instance.id,
                text: instance.descripcion
            };
            configMdlCategory.categorySelect.addOption(item);
            configMdlCategory.categorySelect.clear(true);
            configMdlCategory.categorySelect.refreshOptions(false);
            configMdlCategory.categorySelect.addItem(item.id)
        }
    </script>
@endpush
