<div class="modal inmodal" id="modal_crear_color" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Color</h4>
                <small class="font-bold">Crear nuevo color.</small>
            </div>
            <div class="modal-body">
                @include('almacenes.colores.forms.form_create_color')
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" form="form_create_color" class="btn btn-primary btn-sm"><i
                            class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    const paramsMdlCreateColor = {
        fpImg: null
    }

    function openMdlCreateColor() {
        $('#modal_crear_color').modal('show');
    }

    function eventsMdlCreateColor() {
        loadFpMdlCreateColor();
        document.querySelector('#form_create_color').addEventListener('submit', (e) => {
            e.preventDefault();
            registrarColor(e.target);
        })

        $('#modal_edit_color').on('hidden.bs.modal', function(e) {
            const formCreate = document.querySelector('#form_create_color');
            formCreate.reset();
            limpiarErroresValidacion('msgError');
            paramsMdlCreateColor.id = null;
            paramsMdlCreateColor.row = null;

            if (paramsMdlCreateColor.fpImg) {
                paramsMdlCreateColor.fpImg.removeFiles();
            }
        });
    }

    function registrarColor(formCreateColor) {

        const colorNombre = document.querySelector('#descripcion').value;

        Swal.fire({
            title: "Desea registrar el color?",
            html: `
            <div style="text-align: center; margin-top: 10px;">
                <p style="font-size: 16px; margin-bottom: 10px;">
                    <strong>Nombre:</strong> ${colorNombre}
                </p>
            </div>
        `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí!",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Registrando color...",
                    text: "Por favor, espere",
                    icon: "info",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    toastr.clear();

                    const formData = new FormData(formCreateColor);
                    const res = await axios.post(route('almacenes.colores.store'), formData);

                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERCIÓN COMPLETADA');
                        $('#modal_crear_color').modal('hide');
                        dtColores.ajax.reload();
                    } else {
                        Swal.close();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }

                } catch (error) {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            pintarErroresValidacion(errors, 'error');
                            Swal.close();
                            toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                        } else {
                            Swal.close();
                            toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } else if (error.request) {
                        Swal.close();
                        toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.',
                            'ERROR DE CONEXIÓN');
                    } else {
                        Swal.close();
                        toastr.error(error.message, 'ERROR DESCONOCIDO');
                    }
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "Operación cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                });
            }
        });
    }

    function loadFpMdlCreateColor() {
        const inputImg = document.querySelector('#imagen');

        paramsMdlCreateColor.fpImg = FilePond.create(inputImg, {
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
