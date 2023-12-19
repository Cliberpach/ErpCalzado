<div class="modal inmodal" id="modal_file" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Subir el Excel</h4>
                <form action="{{ route('ModeloExcel.categoria') }}" method="get" style="display: inline-block;">
                    <input type="submit" class="btn btn-primary" value="Descargar el modelo de excel" />
                </form>
                <br>
                <span class="required">Nota: Si la categoria esta agregada en el sistema, no se registrará de nuevo</span>
            </div>
            <div class="modal-body">
                <div id="drag-drop-area" name="fotografije[]"></div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link href="https://releases.transloadit.com/uppy/v1.28.0/uppy.min.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="https://releases.transloadit.com/uppy/v1.28.0/uppy.min.js"></script>
    <script src="https://releases.transloadit.com/uppy/locales/v1.19.0/es_ES.min.js"></script>
    <script>
        var url = "{{ route('ImportExcel.uploadcategoria') }}";
        const XHRUpload = Uppy.XHRUpload;
        var uppy = Uppy.Core({
                debug: true,
                locale: Uppy.locales.es_ES,
                restrictions: {
                    maxNumberOfFiles: 1,
                    allowedFileTypes: ['.xlsx']
                }
            }).use(Uppy.Dashboard, {
                inline: true,
                target: '#drag-drop-area',
                height: 300,
                note: 'Solo archivos de tipo Excel',
            })
            .use(XHRUpload, {
                endpoint: url,
                method: 'post'
            });
        uppy.on('upload-success', (file, response) => {
            var resultado = response.body;
        });
        uppy.on('complete', (result) => {
            console.log('Upload complete! We’ve uploaded these files:', result)
            location.reload();
        });
    </script>
@endpush
