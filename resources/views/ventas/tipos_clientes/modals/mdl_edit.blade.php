<!-- Modal -->
<div class="modal fade" id="mdl_edit_tipocliente" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Tipo Cliente</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('ventas.tipos_clientes.forms.form_edit')
            </div>

            <div class="modal-footer d-flex justify-content-between">

                <div class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    <small>Los campos marcados con asterisco (*) son obligatorios.</small>
                </div>

                <div>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        Cerrar
                    </button>

                    <button type="submit" form="form_edit_tipocliente" class="btn btn-primary">
                        Guardar
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const parameters = {
        id: null,
        row: null
    };

    function openMdlEdit(id) {
        if (!id) {
            toastr.error('FALTA EL PARÁMETRO ID CATEGORY');
            return;
        }
        const row = getRowById(dtTipoCliente, id);
        if (!row) {
            toastr.error('Tipo cliente no encontrado');
            return;
        }
        parameters.id = id;
        parameters.row = row;
        document.querySelector('#nombre_edit').value = row.nombre;
        $('#mdl_edit_tipocliente').modal('show');
    }

    function eventsMdlEdit() {
        document.querySelector('#form_edit_tipocliente').addEventListener('submit', (e) => {
            e.preventDefault();
            updateTipoCliente(e.target);
        })

        $('#editCategoryForm').on('hidden.bs.modal', function(e) {
            const formEdit = document.querySelector('#form_edit_tipocliente');
            formEdit.reset();
            limpiarErroresValidacion('msgError');
            parameters.id = null;
            parameters.row = null;
        });
    }

    function updateTipoCliente(form) {

        Swal.fire({
            title: "Desea actualizar el tipo cliente?",
            text: `Categoría: ${parameters.row.name}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, ACTUALIZAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {


                Swal.fire({
                    title: 'Cargando...',
                    html: 'Actualizando categoría...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    limpiarErroresValidacion('msgError_edit');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formData = new FormData(form);
                    let url = `{{ route('ventas.tipo_cliente.update', ['id' => ':id']) }}`;
                    url = url.replace(':id', parameters.id);

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: formData
                    });

                    const res = await response.json();

                    console.log(res);

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        dtTipoCliente.draw();
                        $('#mdl_edit_tipocliente').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'Error en la petición editar tipo cliente');
                    Swal.close();
                }


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "OPERACIÓN CANCELADA",
                    text: "NO SE REALIZARON ACCIONES",
                    icon: "error"
                });
            }
        });
    }
</script>
