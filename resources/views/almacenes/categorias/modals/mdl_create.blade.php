<!-- Modal -->
<div class="modal fade" id="mdl_create_tipocliente" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Registrar Categoría</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('almacenes.categorias.forms.form_create')
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

                    <button form="form_create_categoria" type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const paramsMdlCreateCategoria = {
        fpImg: null
    }

    function openMdlCreate() {
        $('#mdl_create_tipocliente').modal('show');
    }

    function eventsMdlCreate() {
        loadFpMdlCreateCategoria();

        document.querySelector('#form_create_categoria').addEventListener('submit', (e) => {
            e.preventDefault();
            storeCategoria(e.target);
        })

        $('#mdl_create_tipocliente').on('hidden.bs.modal', function(e) {
            const createCategoryForm = document.querySelector('#form_create_categoria');
            createCategoryForm.reset();
            limpiarErroresValidacion('msgError');
        });

    }

    function loadFpMdlCreateCategoria() {
        const inputImg = document.querySelector('#imagen');

        paramsMdlCreateCategoria.fpImg = FilePond.create(inputImg, {
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

    function storeCategoria(form) {

        Swal.fire({
            title: "Desea registrar la categoría?",
            text: "Se crearán nuevos registros!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ!",
            cancelButtonText: "NO!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Registrando categoría...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    limpiarErroresValidacion('msgError');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formData = new FormData(form);
                    const urlstoreCategoria = @json(route('almacenes.categorias.store'));

                    const response = await fetch(urlstoreCategoria, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
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
                        dtCategoria.ajax.reload();
                        $('#mdl_create_tipocliente').modal('hide');
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        Swal.close();
                    } else {
                        toastr.error(res.message, 'Error en el servidor');
                        Swal.close();
                    }


                } catch (error) {
                    toastr.error(error, 'Error en la petición registrar categoría');
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
</script>
