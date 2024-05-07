<div class="modal inmodal" id="modal-bultos" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-xs">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fas fa-box-open modal-icon"></i>                              
                <h4 class="modal-title">N° BULTOS</h4>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')

               <form action="" id="form-pdf-bultos" method="post">
                    <div class="row">
                        <div class="col-12">
                            <input type="text" hidden id="documento_id">

                            <label for="inputNroBultos">NRO° BULTOS</label>
                            <input type="number" class="form-control" id="nro_bultos">
                        </div>
                    </div>
               </form>
                
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="form-pdf-bultos"><i
                        class="fas fa-file-pdf" ></i> GENERAR PDF </button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function eventsModalBultos(){
        document.querySelector('#form-pdf-bultos').addEventListener('submit',(e)=>{
            e.preventDefault();
            const documento_id  =   document.querySelector('#documento_id').value;
            const nro_bultos    =   document.querySelector('#nro_bultos').value;
            
            generarPdfBultos(documento_id,nro_bultos);

        })
    }

    function generarPdfBultos(documento_id,nro_bultos){
        
        const url = "{{ route('ventas.despachos.pdfBultos', [':documento_id', ':nro_bultos']) }}";
        const urlFinal = url.replace(':documento_id', documento_id).replace(':nro_bultos', nro_bultos);

        window.location.href = urlFinal;
    }
</script>     
@endpush
