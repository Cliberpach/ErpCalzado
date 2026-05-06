<div class="modal inmodal" id="mdlCreateAlmacen" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Almacen</h4>
                <small class="font-bold">Crear nuevo Almacen.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.almacen.forms.form_almacen_create')
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        const paramsMdlCreateAlmacen = {

        }

        function openMdlCreateAlmacen() {
            $('#mdlCreateAlmacen').modal('show');
        }

        function eventsMdlCreateAlmacen() {
            document.querySelector('#formCreateAlmacen').addEventListener('submit', (e) => {
                e.preventDefault();
                storeAlmacen(e.target);
            })

            $('#mdlCreateAlmacen').on('hidden.bs.modal', function() {
                clearFormCreateAlmacen();
            });
        }

        async function storeAlmacen(formCreateAlmacen) {

            toastr.clear();

            const nombreAlmacen = document.querySelector('#descripcion').value || '';

            Swal.fire({
                title: 'Desea registrar el almacén?',
                html: `Almacén: <b>${nombreAlmacen}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Registrando almacén...",
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
                        formData.append('sede_id', @json($sede_id));

                        const res = await axios.post('{{ route('almacenes.almacen.store') }}', formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'operación completada');
                            dtAlmacenes.ajax.reload();
                            $('#mdlCreateAlmacen').modal('hide');
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

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire(
                        'Cancelado',
                        'La solicitud se ha cancelado.',
                        'error'
                    );
                }

            });
        }

        function clearFormCreateAlmacen() {
            const form = document.getElementById('formCreateAlmacen');
            form.reset();
            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });
            document.getElementById('tipo_almacen_edit').value = 'SECUNDARIO';
        }
    </script>
@endpush
