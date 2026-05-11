<div class="modal inmodal" id="mdlEditPromocion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>

                <i class="fa fa-tags modal-icon"></i>

                <h4 class="modal-title">Promoción</h4>
                <small class="font-bold">Editar promoción.</small>
            </div>

            <div class="modal-body">
                @include('mantenimiento.promociones.forms.form_edit')
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script>
        const paramsMdlEditPromocion = {
            id: null,
            row: null
        }

        function openMdlEditPromocion(id) {

            paramsMdlEditPromocion.id = id;
            paramsMdlEditPromocion.row = getRowById(dtPromociones, id);

            setFormEditPromocion();

            $('#mdlEditPromocion').modal('show');
        }

        function eventsMdlEditPromocion() {

            document.querySelector('#formEditPromocion').addEventListener('submit', (e) => {
                e.preventDefault();
                updatePromocion(e.target);
            });

            $('#mdlEditPromocion').on('hidden.bs.modal', function() {
                clearFormEditPromocion();
            });

        }

        function setFormEditPromocion() {

            const row = paramsMdlEditPromocion.row;


            document.querySelector('#nombre_edit').value =
                row.nombre ?? '';


            document.querySelector('#descripcion_edit').value =
                row.descripcion ?? '';


            document.querySelector('#tipo_promocion_edit').value =
                row.tipo_promocion ?? 'precio_total';


            document.querySelector('#valor_edit').value =
                row.valor ?? '';


            document.querySelector('#cantidad_minima_edit').value =
                row.cantidad_minima ?? 1;


            document.querySelector('#fecha_inicio_edit').value =
                row.fecha_inicio ?? '';


            document.querySelector('#fecha_fin_edit').value =
                row.fecha_fin ?? '';


            changePromotionEditUI();
        }
        async function updatePromocion(formEditPromocion) {

            toastr.clear();

            const nombre = document.querySelector('#nombre_edit').value || '';

            Swal.fire({
                title: '¿Desea actualizar la promoción?',
                html: `Promoción: <b>${nombre}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Sí',
                cancelButtonText: "No",
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: "Actualizando promoción...",
                        text: "Por favor, espere.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    try {

                        limpiarErroresValidacion('edit_error');

                        const formData = new FormData(formEditPromocion);
                        formData.append('_method', 'PUT');

                        const res = await axios.post(
                            route('mantenimiento.promociones.update', {
                                id: paramsMdlEditPromocion.id
                            }),
                            formData
                        );

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            dtPromociones.ajax.reload();
                            $('#mdlEditPromocion').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'Error en servidor');
                        }

                    } catch (error) {

                        if (error.response) {

                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'edit_error');
                                toastr.error('Errores de validación', 'Validación');
                            } else {
                                toastr.error(error.response.data.message, 'Error servidor');
                            }

                        } else if (error.request) {
                            toastr.error('Sin conexión con el servidor', 'Error');
                        } else {
                            toastr.error(error.message, 'Error desconocido');
                        }

                    } finally {
                        Swal.close();
                    }

                } else {
                    Swal.fire('Cancelado', 'No se realizaron cambios', 'info');
                }

            });

        }

        function clearFormEditPromocion() {

            const form = document.getElementById('formEditPromocion');
            form.reset();

            form.querySelectorAll('.msgError').forEach(el => {
                el.textContent = '';
            });

        }
    </script>
@endpush
