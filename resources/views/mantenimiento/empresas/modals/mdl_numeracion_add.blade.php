<div class="modal fade" id="mdlNumeracionAdd" tabindex="-1" aria-labelledby="mdlNumeracionAddLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mdlNumeracionAddLabel">AGREGAR NUMERACION</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('mantenimiento.empresas.forms.form_add_numeracion')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button form="formAddNumeracionEmpresa" type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>

    function eventsMdlAddNumeracion() {

        document.querySelector('#formAddNumeracionEmpresa').addEventListener('submit', (e) => {
            e.preventDefault();
            guardarNumeracion(e.target);
        });

        $('#mdlNumeracionAdd').on('hidden.bs.modal', function() {
            document.querySelector('#parametro').value  = '';
            document.querySelector('#serie').value      = '';
            document.querySelector('#nro_inicio').value = '';
            $('#comprobante_id').val(null).trigger('change');
        });
    }

    async function guardarNumeracion(formNumeracionStore) {

        toastr.clear();

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "Agregar numeracion?",
            text: "Se agregara el tipo de comprobante a la sede principal.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Si!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Registrando...",
                    text: "Por favor, espere mientras procesamos la solicitud.",
                    icon: "info",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    toastr.clear();
                    limpiarErroresValidacion('msgError');
                    const formData = new FormData(formNumeracionStore);

                    const res = await axios.post(route('mantenimiento.empresas.numeracionStore'), formData);

                    if (res.data.success) {
                        const comprobante_id = res.data.comprobante_id;

                        $('#comprobante_id').find(`option[value="${comprobante_id}"]`).remove();
                        $('#comprobante_id').val(null).trigger('change');

                        tableNumeracionEmpresa.ajax.reload();
                        $('#mdlNumeracionAdd').modal('hide');
                        toastr.success(res.data.message, 'OPERACION COMPLETADA');
                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {

                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            toastr.error('Errores de validacion encontrados.', 'ERROR DE VALIDACION');
                        } else {
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        toastr.error('No se pudo contactar al servidor.', 'ERROR DE CONEXION');
                    } else {
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }

                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "Operacion cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                });
            }
        });
    }

    function cambiarTipoComprobante(selectTipoComprobante) {

        const comprobante_id     = selectTipoComprobante.value;

        if (!comprobante_id) {
            return;
        }

        const tipos_comprobantes = @json($tipos_comprobantes);
        const tipo_comprobante   = tipos_comprobantes.filter((tc) => { return tc.id == comprobante_id; });

        if (tipo_comprobante.length === 0) {
            toastr.error('TIPO COMPROBANTE NO ENCONTRADO!!!', 'ERROR');
            return;
        }

        const inputSerie     = document.querySelector('#serie');
        const inputParametro = document.querySelector('#parametro');

        inputParametro.value = tipo_comprobante[0].parametro;

        if (tipo_comprobante[0].parametro.length === 1) {
            inputSerie.value = '';
            document.querySelector('#pCodigoSerie').textContent = '3 caracteres permitidos';
            inputSerie.maxLength = 3;
        }
        if (tipo_comprobante[0].parametro.length === 2) {
            inputSerie.value = '';
            document.querySelector('#pCodigoSerie').textContent = '2 caracteres permitidos';
            inputSerie.maxLength = 2;
        }
    }

</script>
