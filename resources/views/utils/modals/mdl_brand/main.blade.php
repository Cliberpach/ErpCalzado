<div class="modal inmodal" id="mdlCreateBrand" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Marca</h4>
                <small class="font-bold">Crear Marca.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.marcas.forms.form_create')
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const configMdlBrand = {
            brandSelect: null
        }

        function loadMdlBrand(config) {
            configMdlBrand.brandSelect = config.brandSelect;
            evenstMdlCreateBrand();
        }

        function openMdlBrand() {
            $('#mdlCreateBrand').modal('show');
        }

        function evenstMdlCreateBrand() {
            document.querySelector('#formCreateBrand').addEventListener('submit', (e) => {
                e.preventDefault();
                storeBrand(e.target);
            })

            $('#mdlCreateBrand').on('hidden.bs.modal', function() {
                clearFormCreateBrand();
            });
        }

        async function storeBrand(formCreateAlmacen) {

            toastr.clear();

            const name = document.querySelector('#descripcion').value || '';

            Swal.fire({
                title: 'Desea registrar la marca?',
                html: `Marca: <b>${name}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando marca...",
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

                        const res = await axios.post(route('almacenes.marcas.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            addBrand(res.data.data);
                            $('#mdlCreateBrand').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'error en el servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'brand_error');
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

        function clearFormCreateBrand() {
            const form = document.getElementById('formCreateBrand');
            form.reset();
            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
        }

        function addBrand(instance) {
            const item = {
                id: instance.id,
                text: instance.marca
            };
            configMdlBrand.brandSelect.addOption(item);
            configMdlBrand.brandSelect.clear(true);
            configMdlBrand.brandSelect.refreshOptions(false);
            configMdlBrand.brandSelect.addItem(item.id)
        }
    </script>
@endpush
