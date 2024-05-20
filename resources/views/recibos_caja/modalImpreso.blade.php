<div class="modal inmodal" id="modal_imprimir" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="recibo_caja_id" id="recibo_caja_id">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <label for="">Formato a4</label>
                                <br>
                                <a type="button" class="btn btn-primary btn-ticket-N"><i class="fa fa-download"
                                        aria-hidden="true"></i></a>
                            </div>
                            <div class="col-md-6 text-center">
                                <label for="">Formato 80</label>
                                <br>
                                <a href="javascript:void(0);" type="button" target="_blank" class="btn btn-primary btn-ticket-O">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
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

@push('scripts')
    <script>
        $(".btn-ticket-N").click(function (e) {
            e.preventDefault();
            const recibo_caja_id = document.querySelector('#recibo_caja_id').value;
            const size = "normal"; 
            let url = "{{ route('recibos_caja.pdf', ['size' => ':size', 'recibo_caja_id' => ':recibo_caja_id']) }}";
            url = url.replace(':size', size).replace(':recibo_caja_id', recibo_caja_id);
            
            window.open(url, '_blank');
        });
        $(".btn-ticket-O").click(function (e) {
            e.preventDefault();
            const recibo_caja_id = document.querySelector('#recibo_caja_id').value;
            const size = 80; 
            let url = "{{ route('recibos_caja.pdf', ['size' => ':size', 'recibo_caja_id' => ':recibo_caja_id']) }}";
            url = url.replace(':size', size).replace(':recibo_caja_id', recibo_caja_id);
            
            window.open(url, '_blank');
        });
    </script>
@endpush

