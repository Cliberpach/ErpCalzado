<!-- Modal -->
<div class="modal fade" id="mdl_edit_categoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Categoría</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('almacenes.categorias.forms.form_edit')
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

                    <button type="submit" form="form_edit_categoria" class="btn btn-primary">
                        Actualizar
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const paramsMdlEditCategoria = {
        id: null,
        row: null,
        fpImg: null
    };

    function openMdlEdit(id) {
        if (!id) {
            toastr.error('FALTA EL PARÁMETRO ID CATEGORY');
            return;
        }

        paramsMdlEditCategoria.id = id;
        setFormEdit();
        $('#mdl_edit_categoria').modal('show');
    }

    function setFormEdit() {
        const row = getRowById(dtCategoria, paramsMdlEditCategoria.id);
        if (!row) {
            toastr.error('Categoría no encontrada');
            return;
        }
        paramsMdlEditCategoria.row = row;

        document.querySelector('#nombre_edit').value = row.descripcion;

        if (!paramsMdlEditCategoria.fpImg) return;
        paramsMdlEditCategoria.fpImg.removeFiles();

        if (row.img_ruta) {
            paramsMdlEditCategoria.fpImg.addFile(
                @json(asset('storage')) + '/' + row.img_ruta
            );
        }
    }

    function eventsMdlEdit() {
        loadFpMdlEditCategoria();
        document.querySelector('#form_edit_categoria').addEventListener('submit', (e) => {
            e.preventDefault();
            updateCategoria(e.target);
        })

        $('#mdl_edit_categoria').on('hidden.bs.modal', function(e) {
            const formEdit = document.querySelector('#form_edit_categoria');
            formEdit.reset();
            limpiarErroresValidacion('msgError');
            paramsMdlEditCategoria.id = null;
            paramsMdlEditCategoria.row = null;
        });
    }

    function updateCategoria(form) {

        Swal.fire({
            title: "Desea actualizar la categoría?",
            text: `Categoría: ${paramsMdlEditCategoria.row.descripcion}`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ!",
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
                    let url = `{{ route('almacenes.categorias.update', ['id' => ':id']) }}`;
                    url = url.replace(':id', paramsMdlEditCategoria.id);

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
                        dtCategoria.draw();
                        $('#mdl_edit_categoria').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }

                } catch (error) {
                    toastr.error(error, 'Error en la petición editar tipo cliente');
                    Swal.close();
                } finally {
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

    function loadFpMdlEditCategoria() {
        const inputImg = document.querySelector('#imagen_edit');

        paramsMdlEditCategoria.fpImg = FilePond.create(inputImg, {
            allowImagePreview: true,
            imagePreviewHeight: 120,
            imageCropAspectRatio: '1:1',
            styleLayout: 'compact',
            stylePanelAspectRatio: 0.5,
            storeAsFile: true,

            allowFileTypeValidation: true,
            acceptedFileTypes: [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/avif'
            ],

            allowFileSizeValidation: true,
            maxFileSize: '2MB',

            labelIdle: 'Arrastra una imagen o <span class="filepond--label-action">Buscar</span>',

            labelFileTypeNotAllowed: 'Solo se permiten imágenes PNG, JPG, WEBP o AVIF',
            fileValidateTypeLabelExpectedTypes: 'Formatos válidos: PNG, JPG, WEBP, AVIF',

            labelMaxFileSizeExceeded: 'El archivo es demasiado grande',
            labelMaxFileSize: 'El tamaño máximo permitido es 2 MB'
        });
    }
</script>
