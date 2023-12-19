<template>
    <div class="modal inmodal" id="modal_descargas_pdf" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" @click.prevent="cerrar">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title descarga-title">{{formPdf.title}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 text-center">
                            <div class="form-group">
                                <button class="btn btn-info file-pdf" @click.prevent="DownlaodPdf"><i class="fa fa-file-pdf-o"></i></button><br>
                                <b>Descargar A4</b>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 text-center mb-4">
                            <div class="form-group">
                                <button class="btn btn-info file-ticket" @click.prevent="DownlaodTicket"><i class="fa fa-file-o"></i></button><br>
                                <b>Descargar Ticket</b>
                            </div>
                        </div>
                        <br>
                        <div class="col-12 mt-4">
                            <form id="frm_envio" @submit.prevent="EnviarEmail">

                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="hidden" id="id" name="id" v-model="formPdf.id" placeholder="Id" class="form-control"
                                            required>
                                        <input type="email" id="correo" v-model="formPdf.correo" name="correo" placeholder="Correo electrónico"
                                            class="form-control" required>
                                        <span class="input-group-append"><button type="submit"
                                                class="btn btn-default"><i class="fa fa-envelope"></i>
                                                <span>Enviar</span></button></span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"
                                @click.prevent="cerrar"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "ModalPdfDownload",
    props: ["pdfData"],
    data() {
        return {
            formPdf: {
                _token: $('meta[name=csrf-token]').attr("content"),
                id: 0,
                correo: "",
                title:""
            }
        }
    },
    watch: {
        pdfData(value) {
            if (value != null) {
                this.formPdf.id = value.id;
                this.formPdf.correo = value.correo;
                this.formPdf.title = value.serie+"-"+value.correlativo;
                $("#modal_descargas_pdf").modal("show");
            }
        },

    },
    methods: {
        cerrar() {
            this.$emit("update:pdfData", null);
        },
        EnviarEmail() {
            try {
                let me=this;
                let timerInterval = 9999;
                Swal.fire({
                    title: 'Enviando email...',
                    icon: 'info',
                    timer: 10,
                    customClass: {
                        container: 'my-swal'
                    },
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        Swal.stopTimer();
                        me.$http.post(route('ventas.documento.envio'), me.formPdf).then((value) => {
                            let response = value.data;
                            if (response.success) {
                                toastr.success(response.message);
                                timerInterval = 0;
                                $('#correo').val('');
                                Swal.resumeTimer();
                            } else {
                                toastr.error(response.message);
                                timerInterval = 0;
                                Swal.resumeTimer();
                            }
                        });
                    },
                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                });
            } catch (ex) {

            }
        },
        DownlaodPdf(){
            var url = route('ventas.documento.comprobante', {id:this.formPdf.id+"-100"});
            window.open(url, "Comprobante SISCOM", "width=900, height=600")
        },
        DownlaodTicket(){
            var url = route('ventas.documento.comprobante', {id:this.formPdf.id+"-80"});
            window.open(url, "Comprobante SISCOM", "width=900, height=600");
        }
    }
}
</script>