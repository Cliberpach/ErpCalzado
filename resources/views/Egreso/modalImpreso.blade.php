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
                <form action="" method="get" id="frm_imprimir">
                <div class="row">
                    <input type="hidden" name="egreso_id" id="egreso_id">
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <label for="">Formato a4</label>
                                <br>
                                <button type="button" class="btn btn-primary btn-ticket-N"><i class="fa fa-download"
                                        aria-hidden="true"></i></button>
                            </div>
                            <div class="col-md-6 text-center">
                                <label for="">Formato 80</label>
                                <br>
                                <button type="button" class="btn btn-primary btn-ticket-O"><i class="fa fa-download"
                                        aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                </form>
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
 var url = "{{ route('Egreso.recibo', ':size') }}"
        $(".btn-ticket-N").click(function (e) {
            e.preventDefault();
            $("#frm_imprimir").attr('action', url.replace(":size", "normal"))
            $("#frm_imprimir").submit()
        });
        $(".btn-ticket-O").click(function (e) {
            e.preventDefault();
        $("#frm_imprimir").attr('action', url.replace(":size", 80))
            $("#frm_imprimir").submit()
        });
    </script>
@endpush

