<div class="modal inmodal" id="modal_file" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Subir el Excel</h4>
                            <form action="{{route('almacenes.nota_ingreso.downloadexcel')}}" method="get" style="display: inline-block;">
                                <input type="submit" class="btn btn-primary" value="Descargar el modelo de excel" />
                            </form>
                            <form action="{{route('almacenes.nota_ingreso.downloadproductosexcel')}}" method="get" style="display: inline-block;">
                                <input type="submit" class="btn btn-primary" value="Descargar Productos terminados" />
                            </form>
            </div>
            <div class="modal-body">
                <div id="drag-drop-area"  name="fotografije[]"></div>
                <div style="display: none" id="tablaerrores">
                    <table class="table dataTables-errores table-striped table-bordered table-hover"
                    style="text-transform:uppercase;">
                    <thead>
                        <tr>
                            <th class="text-center">Fila</th>
                            <th class="text-center">Atributo</th>
                            <th class="text-center">Error</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                <div id="imprimir" name="imprimir" style="display: none" >
                    <form action="{{route('almacenes.nota_ingreso.error_excel')}}" method="get" >
                        @csrf
                        <input type="hidden" id="arregloerrores" name="arregloerrores">
                        <input type="submit" class="btn btn-primary" value="Imprimir Excel con correciones" />
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button"  class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
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
    var url="{{route('almacenes.nota_ingreso.uploadnotaingreso')}}";
    const XHRUpload =Uppy.XHRUpload;
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
        endpoint:url,
        method: 'post'
    });
    uppy.on('upload-success', (file, response) => {
        var resultado = response.body;
        console.log(resultado);
        if(resultado.length == 0)
        {
            toastr.success('Exito','Success');
            location.reload();
        }
        else
        {
            var t = $('.dataTables-errores').DataTable();
            var dataE = [];
            for (let i = 0; i < resultado.length - 1; i++) {
                t.row.add([
                    resultado[i].fila,
                    resultado[i].atributo,
                    resultado[i].error[0],
                ]).draw(false);
                let fila = {
                        fila: resultado[i].fila,
                        atributo: resultado[i].atributo,
                        error: resultado[i].error[0],
                    };
                    dataE.push(fila);
            }
            let fila={excel: resultado[resultado.length - 1].excel};
            dataE.push(fila);
            $("#tablaerrores").css("display","block");
            $("#imprimir").css("display","block");
            $("#arregloerrores").val(JSON.stringify(dataE));
        }
    });
    uppy.on('complete', (result) => {
      console.log('Upload complete! We’ve uploaded these files:', result)
    });
    $(document).on('click','.uppy-u-reset',function(){
        var table =$('.dataTables-errores').DataTable();
        table.clear().draw();
        $("#tablaerrores").css("display","none");
        $("#imprimir").css("display","none");
    });
  </script>
@endpush
