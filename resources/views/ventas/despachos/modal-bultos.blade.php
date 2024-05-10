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

               <form action="" id="form-pdf-bultos" method="post" target="_blank" onsubmit="window.open('about:blank', 'popup', 'width=800,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=100,screeny=100');">
                    <div class="row">
                        <div class="col-12">
                            <input type="text" hidden id="documento_id">
                            <input type="text" hidden id="despacho_id">

                            <label for="inputNroBultos">NRO° BULTOS</label>
                            <input type="text" class="form-control" id="nro_bultos">
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
        document.getElementById("nro_bultos").addEventListener("input", function() {
            var inputValue = this.value;
            var valorNumerico = inputValue.replace(/[^0-9]/g, '');
            this.value = valorNumerico;
        });

        document.querySelector('#form-pdf-bultos').addEventListener('submit',(e)=>{
            e.preventDefault();
            const documento_id  =   document.querySelector('#documento_id').value;
            const despacho_id   =   document.querySelector('#despacho_id').value;

            const nro_bultos    =   document.querySelector('#nro_bultos').value;
            
            if(nro_bultos.length > 0){
                generarPdfBultos(documento_id,despacho_id,nro_bultos);
            }else{
                toastr.error('INGRESE UN N° DE BULTOS','ERROR');
            }

        })
    }

    function generarPdfBultos(documento_id,despacho_id,nro_bultos){
        
        const url = "{{ route('ventas.despachos.pdfBultos', [':documento_id', ':despacho_id' , ':nro_bultos']) }}";
        const urlFinal = url.replace(':documento_id', documento_id).replace(':despacho_id', despacho_id)
        .replace(':nro_bultos', nro_bultos);

        window.location.href = urlFinal;
        document.querySelector('#nro_bultos').value  =   '';
    }
</script>     
@endpush
