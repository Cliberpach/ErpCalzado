<template>
    <div class="modal inmodal" id="modal_pago" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" @click.prevent="Limpiar">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title pago-title">{{ pagoForm.numero_doc }}</h4>
                    <small class="font-bold pago-subtitle">{{ pagoForm.cliente }}</small>
                </div>
                <div class="modal-body">
                    <form @submit="Pagar" :action="RouteStore" id="pago_venta" method="POST"
                        enctype="multipart/form-data">
                        <input type="text" class="d-none" name="_token" :value="token">
                        <div class="row">
                            <div class="col-12 col-md-6 br ">
                                <div class="form-group d-none">
                                    <label class="col-form-label required">Venta</label>
                                    <input type="text" class="form-control" v-model="pagoForm.venta_id" id="venta_id"
                                        name="venta_id" readonly>
                                </div>
                                <div class="form-group d-none">
                                    <label class="col-form-label required">Tipo Pago</label>
                                    <input type="text" class="form-control" id="tipo_pago_id" name="tipo_pago_id"
                                        readonly v-model="pagoForm.tipo_pago_id">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Monto</label>
                                    <input type="text" class="form-control" id="monto_venta" name="monto_venta" readonly
                                        v-model="pagoForm.monto_venta">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Efectivo</label>
                                    <input type="text" class="form-control" id="efectivo" name="efectivo"
                                        @keyup="changeEfectivo()" v-model="pagoForm.efectivo"
                                        :readonly="ModoPagos =='EFECTIVO' ? true : false">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Modo de pago</label>
                                    <select name="modo_pago" id="modo_pago" v-model="modo_pago" class="custom-select"
                                        @change="changeModoPago()">
                                        <option v-for="(item,index) in modoPagos" :key="index"
                                            :value="item.id+'-'+item.descripcion">{{ item.descripcion }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Importe</label>
                                    <input type="text" class="form-control" id="importe" name="importe"
                                        @keyup="changeImporte()" v-model="pagoForm.importe"
                                        :readonly="ModoPagos == 'EFECTIVO' ? true : false">
                                </div>
                                <div class="form-group" id="div_cuentas" v-if="ModoPagos =='TRANSFERENCIA'">
                                    <label class="col-form-label">Cuentas</label>
                                    <select name="cuenta_id" id="cuenta_id" class="select2_form custom-select"
                                        v-model="cuentaId">
                                        <option value="">Seleccionar</option>
                                        <option v-for="(item,index) in cuentas" :key="index" :value="item.id">{{
                                        item.descripcion }} - {{ item.num_cuenta }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label id="imagen_label">Imagen:</label>

                                    <div class="custom-file">
                                        <input id="imagen" type="file" @change="changeImagen" name="imagen"
                                            class="custom-file-input" accept="image/*">

                                        <label for="imagen" id="imagen_txt"
                                            class="custom-file-label selected">Seleccionar</label>

                                        <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>

                                    </div>
                                    <div class="custom-file">
                                        <input id="imagen2" type="file" @change="changeImagen2" name="imagen2"
                                            class="custom-file-input" accept="image/*">

                                        <label for="imagen2" id="imagen_txt2"
                                            class="custom-file-label selected">Seleccionar</label>

                                        <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>

                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <div class="col-6 align-content-center">
                                        <div class="row justify-content-end">
                                            <a href="javascript:void(0);" id="limpiar_imagen"
                                                @click.prevent="LimpiarImgen">
                                                <span class="badge badge-danger">x</span>
                                            </a>
                                        </div>
                                        <div class="row justify-content-center">
                                            <p>
                                                <img class="imagen modalPago" :src="imgDefault" alt="">
                                                <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6 align-content-center">
                                        <div class="row justify-content-end">
                                            <a href="javascript:void(0);" id="limpiar_imagen2"
                                                @click.prevent="LimpiarImgen2">
                                                <span class="badge badge-danger">x</span>
                                            </a>
                                        </div>
                                        <div class="row justify-content-center">
                                            <p>
                                                <img width="200px" height="200px" class="imagen2 modalPago" :src="imgDefault" alt="">
                                                <input id="url_imagen2" name="url_imagen2" type="hidden" value="">
                                            </p>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                            campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm" form="pago_venta"><i
                                class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"
                            @click.prevent="Limpiar"><i class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "ModalPago",
    props: {
        modoPagos: [],
        imgDefault: "",
        imgDefault2: "/img/default.png",
        cuentas: [],
        pagos: null
    },
    data() {
        return {
            pagoForm: {
                cliente: "",
                condicion: "",
                convertir: null,
                correlativo: 0,
                cotizacion_venta: null,
                dias: 0,
                efectivo: "",
                empresa: "",
                empresa_id: 1,
                estado: "",
                fecha_documento: "",
                id: 0,
                notas: 0,
                numero_doc: "",
                otros: "",
                serie: "",
                sunat: "0",
                tipo_pago: null,
                tipo_venta: "",
                tipo_venta_id: "",
                total: "",
                transferencia: "",
                importe: "",
                venta_id: "",
                monto_venta: "",
                tipo_pago_id: "",

            },
            token: "",
            modo_pago: "",
            cuentaId: ""
        }

    },
    computed: {
        ModoPagos() {
            let mod = this.modo_pago.split("-");
            return mod[1];
        },
        RouteStore() {
            return route('ventas.documento.storePago');
        }
    },
    watch: {
        pagos(value) {

            if (value != null) {
                this.pagoForm.cliente = value.cliente;
                this.pagoForm.numero_doc = value.numero_doc;
                this.pagoForm.efectivo = "0.00";
                this.pagoForm.importe = value.total;
                this.pagoForm.venta_id = value.id;
                this.pagoForm.monto_venta = value.total;
                this.pagoForm.tipo_pago_id = "";
                this.pagoForm.venta_id = value.id;
                this.modo_pago = "1-EFECTIVO";
            }
        },
        modo_pago(value) {
            let mod = value.split("-");
            this.pagoForm.tipo_pago_id = mod[0];
        }
    },
    methods: {
        Pagar(e) {
            try {
                let importe = !isNaN(Number(this.pagoForm.importe)) ? Number(this.pagoForm.importe) : 0;
                let efectivo = !isNaN(Number(this.pagoForm.efectivo)) ? Number(this.pagoForm.efectivo) : 0;

                if (importe == 0 && efectivo == 0)
                    throw "Ingrese al menos un monto";

                if (this.ModoPagos == "TRANSFERENCIA" && this.cuentaId == "")
                    throw "Seleccione una cuenta";

                return true;
            } catch (ex) {
                e.preventDefault();
                toastr.error(ex, 'Error');
            }
        },
        changeModoPago() {
            try {
                if (this.ModoPagos == "EFECTIVO") {
                    this.pagoForm.efectivo = "0.00";
                    this.pagoForm.importe = this.pagoForm.monto_venta;
                }
            } catch (ex) {

            }
        },
        changeEfectivo() {
            try {

                let monto = convertFloat(this.pagoForm.monto_venta);
                let efectivo = convertFloat(this.pagoForm.efectivo);

                if (this.ModoPagos != 'EFECTIVO') {
                    let diferencia = monto - efectivo;
                    this.pagoForm.importe = diferencia.toFixed(2);
                }
            } catch (ex) {
                alert(ex);
            }
        },
        changeImporte() {
            try {

                let monto = convertFloat(this.pagoForm.monto_venta);
                let importe = convertFloat(this.pagoForm.importe);

                if (this.ModoPagos != 'EFECTIVO') {
                    let diferencia = monto - importe;
                    this.pagoForm.efectivo = diferencia.toFixed(2);
                }
            } catch (ex) {

            }
        },
        changeImagen() {
            try {
                var fileInput               = document.getElementById('imagen');
                var filePath                = fileInput.value;
                var allowedExtensions       = /(.jpg|.jpeg|.png)$/i;
                let $imagenPrevisualizacion = document.querySelector(".imagen");

                if (allowedExtensions.exec(filePath)) {
                    var userFile                = document.getElementById('imagen');
                    userFile.src                = URL.createObjectURL(event.target.files[0]);
                    var data                    = userFile.src;
                    $imagenPrevisualizacion.src = data;
                    let fileName = $(this).val().split('\\').pop();
                    console.log(fileName);
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                } else {
                    toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                    $('.imagen').attr("src", this.imgDefault);
                }
            } catch (ex) {

            }
        },
        changeImagen2() {
            try {
                var fileInput               = document.getElementById('imagen2');
                var filePath                = fileInput.value;
                var allowedExtensions       = /(.jpg|.jpeg|.png)$/i;
                let $imagenPrevisualizacion = document.querySelector(".imagen2");

                if (allowedExtensions.exec(filePath)) {
                    var userFile                = document.getElementById('imagen2');
                    userFile.src                = URL.createObjectURL(event.target.files[0]);
                    var data                    = userFile.src;
                    $imagenPrevisualizacion.src = data;
                    let fileName = $(this).val().split('\\').pop();
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                } else {
                    toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                    $('.imagen2').attr("src", this.imgDefault);
                }
            } catch (ex) {

            }
        },
        LimpiarImgen() {
            $('.imagen').attr("src", this.imgDefault)
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen').val('');
        },
        LimpiarImgen2() {
            $('.imagen2').attr("src", this.imgDefault)
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen2').val('');
        },
        Limpiar() {
            this.LimpiarImgen();
            this.pagoForm = {
                cliente: "",
                condicion: "",
                convertir: null,
                correlativo: 0,
                cotizacion_venta: null,
                dias: 0,
                efectivo: "",
                empresa: "",
                empresa_id: 1,
                estado: "",
                fecha_documento: "",
                id: 0,
                notas: 0,
                numero_doc: "",
                otros: "",
                serie: "",
                sunat: "0",
                tipo_pago: null,
                tipo_venta: "",
                tipo_venta_id: "",
                total: "",
                transferencia: "",
                importe: "",
                venta_id: "",
                monto_venta: "",
                tipo_pago_id: "",
            }
        }
    },
    mounted() {
        this.token = $('meta[name=csrf-token]').attr("content");
    }
}
</script>