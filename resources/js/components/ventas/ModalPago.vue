<template>
    <div class="modal inmodal" id="modal_pago" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" @click.prevent="Limpiar">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title pago-title">{{ pagoForm.numero_doc }} - {{ cliente_id }}</h4>
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
                                <div class="form-group row">
                                    <div class="col-4">
                                        <label class="col-form-label">Saldo del cliente</label>
                                        <input readonly class="form-control" type="text" v-model="saldoRecibosCaja">
                                    </div>
                                    <div class="col-3" :class="{ 'd-none': !mostrarRecibosCaja }"
                                        style="display: flex; align-items: flex-end; justify-content: flex-end;">
                                        <button @click="verRecibosCaja" type="button" class="btn btn-success">
                                            {{ txtBtnVerRecibos }}
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Monto</label>
                                    <input type="text" class="form-control" id="monto_venta" name="monto_venta" readonly
                                        v-model="pagoForm.monto_venta">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Efectivo</label>
                                    <input type="text" class="form-control" id="efectivo" name="efectivo"
                                        v-model="pagoForm.efectivo" @input="changeEfectivo($event.target.value)"
                                        :readonly="ModoPagos == 'EFECTIVO' ? true : false">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Modo de pago</label>
                                    <select name="modo_pago" id="modo_pago" v-model="modo_pago" class="custom-select"
                                        @change="changeModoPago()">
                                        <option v-for="(item, index) in modoPagosFiltrados" :key="index"
                                            :value="item.id + '-' + item.descripcion">{{ item.descripcion }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Importe</label>
                                    <input type="text" class="form-control" id="importe" name="importe"
                                        v-model="pagoForm.importe" @input="changeImporte($event.target.value)"
                                        :readonly="ModoPagos == 'EFECTIVO' ? true : false">
                                </div>
                                <div class="row" v-if="pagoForm.tipo_pago_id != 1">
                                    <div class="col-12">
                                        <label class="col-form-label">Cuentas</label>
                                        <v-select @input="onCuentaSeleccionada" name="cuenta_id" ref="selectCuenta"
                                            v-model="pagoForm.cuenta_id" :options="cuentasFiltradas"
                                            :reduce="item => item.cuenta_id" label="cuentaLabel"
                                            placeholder="Seleccionar"></v-select>
                                        <input type="hidden" name="cuenta_id" :value="pagoForm.cuenta_id">
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-form-label required">N° operacion</label>
                                        <input maxlength="100" type="text" class="form-control"
                                            v-model="pagoForm.nro_operacion" id="nro_operacion" name="nro_operacion">
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-form-label required">Fecha Operación</label>
                                        <input type="date" class="form-control" v-model="pagoForm.fecha_pago"
                                            id="fecha_pago" name="fecha_pago">
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label class="col-form-label required">Hora Operación</label>
                                        <input type="time" class="form-control" v-model="pagoForm.hora_pago"
                                            id="hora_pago" name="hora_pago">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-imagenes-pago" :class="{ 'd-none': !mostrarColImgPago }">
                                <div class="form-group">
                                    <label id="imagen_label">Imagen:</label>

                                    <div class="custom-file">
                                        <input id="imagen" type="file" @change="changeImagen()" name="imagen"
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
                                                <img width="200px" height="200px" class="imagen2 modalPago"
                                                    :src="imgDefault" alt="">
                                                <input id="url_imagen2" name="url_imagen2" type="hidden" value="">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-recibos-cliente"
                                :class="{ 'd-none': !mostrarColRecibosCliente }">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">FECHA</th>
                                                <th scope="col">USUARIO</th>
                                                <th scope="col">MET PAGO</th>
                                                <th scope="col">MONTO</th>
                                                <th scope="col">SALDO</th>
                                                <th scope="col">ESTADO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(recibo, index) in recibos_caja" :key="index">
                                                <th scope="row">{{ recibo.created_at }}</th>
                                                <td>{{ recibo.user_nombre }}</td>
                                                <td>{{ recibo.metodo_pago }}</td>
                                                <td>{{ recibo.monto }}</td>
                                                <td>{{ recibo.saldo }}</td>
                                                <td>{{ recibo.estado_servicio }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        <button type="submit" class="btn btn-success btn-sm" form="pago_venta"><i
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
        pagos: null,
        cliente_id: null,
        recibos_caja: [],
        saldoRecibosCaja: 0,
    },
    data() {
        return {
            txtBtnVerRecibos: "VER RECIBOS",
            mostrarColImgPago: true,
            mostrarColRecibosCliente: false,
            mostrarRecibosCaja: false,
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
                tipo_pago_id: 1,
                cuenta_id: null,
                nro_operacion: null,
                fecha_pago: null,
                hora_pago: null
            },
            token: "",
            modo_pago: "",
            cuentasFiltradas: []
        }

    },
    computed: {
        ModoPagos() {
            let mod = this.modo_pago.split("-");
            return mod[1];
        },
        RouteStore() {
            return route('ventas.documento.storePago');
        },
        modoPagosFiltrados() {
            if (this.saldoRecibosCaja === 0) {
                this.mostrarRecibosCaja = false;
                this.mostrarColRecibosCliente = false;
                this.mostrarColImgPago = true;
                return this.modoPagos.filter(item => item.descripcion !== "RECIBO DE CAJA");
            } else {
                return this.modoPagos;
            }
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
                this.pagoForm.tipo_pago_id = 1;
                this.pagoForm.venta_id = value.id;
                this.modo_pago = "1-EFECTIVO";
                this.pagoForm.cuenta_id = null;
            }
        },
        modo_pago(value) {
            this.pagoForm.cuenta_id = null;
            let mod = value.split("-");
            this.pagoForm.tipo_pago_id = mod[0];
            this.pagoForm.nro_operacion = null;
            this.cuentasFiltradas = this.cuentas.filter(c => c.tipo_pago_id == mod[0]);
        }
    },
    methods: {
        onCuentaSeleccionada(val) {
        },
        verRecibosCaja() {
            this.mostrarColImgPago = !this.mostrarColImgPago;
            this.mostrarColRecibosCliente = !this.mostrarColRecibosCliente;

            if (this.mostrarColRecibosCliente) {
                this.txtBtnVerRecibos = "OCULTAR RECIBOS";
            } else {
                this.txtBtnVerRecibos = "VER RECIBOS";
            }

        },
        async Pagar(e) {
            try {
                e.preventDefault();

                toastr.clear();
                let importe = !isNaN(Number(this.pagoForm.importe)) ? Number(this.pagoForm.importe) : 0;
                let efectivo = !isNaN(Number(this.pagoForm.efectivo)) ? Number(this.pagoForm.efectivo) : 0;
                let monto = parseFloat(this.pagoForm.monto_venta);


                if (importe == 0 && efectivo == 0) {
                    throw "Ingrese al menos un monto";
                }

                if (this.ModoPagos !== "EFECTIVO" && !this.pagoForm.cuenta_id) {
                    this.$refs.selectCuenta.$el.querySelector('input').focus();
                    throw "Seleccione una cuenta";
                }

                if (this.ModoPagos !== "EFECTIVO"
                ) {
                    if (!this.pagoForm.nro_operacion) {
                        throw "EL N° OPERACIÓN ES OBLIGATORIO";
                    }
                    if (!this.pagoForm.fecha_pago) {
                        throw "LA FECHA DE PAGO ES OBLIGATORIA";
                    }
                    if (!this.pagoForm.hora_pago) {
                        throw "LA HORA DE PAGO ES OBLIGATORIA";
                    }
                }

                if ((importe + efectivo) < monto) {
                    throw "DEBE CUBRIR EL MONTO TOTAL DEL DOCUMENTO DE VENTA";
                }

                Swal.fire({
                    title: 'Registrando pago...',
                    text: 'Por favor, espera un momento',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData  = new FormData(e.target);
                const res       = await this.axios.post(route('ventas.documento.storePago'), formData);

                if (res.data.success) {
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    $("#modal_pago").modal("hide");
                    $("#modal_ventas").modal("hide");
                    this.$emit('pago-registrado');
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }

            } catch (ex) {
                e.preventDefault();
                toastr.error(ex, 'Error');
            }finally{
                Swal.close();
            }
        },
        changeModoPago() {
            try {
                if (this.ModoPagos !== "RECIBO DE CAJA") {
                    this.mostrarRecibosCaja = false;
                    this.pagoForm.efectivo = 0;
                    this.pagoForm.importe = this.pagoForm.monto_venta;
                    this.mostrarColRecibosCliente = false;
                    this.mostrarColImgPago = true;
                    this.txtBtnVerRecibos = 'VER RECIBOS';
                }

                if (this.ModoPagos === "RECIBO DE CAJA") {
                    this.pagoForm.efectivo = 0;
                    this.mostrarRecibosCaja = true;

                    if (this.saldoRecibosCaja >= this.pagoForm.monto_venta) {
                        this.pagoForm.importe = this.pagoForm.monto_venta;
                    } else {
                        this.pagoForm.importe = this.saldoRecibosCaja;
                        this.pagoForm.efectivo = parseFloat(this.pagoForm.monto_venta) - parseFloat(this.saldoRecibosCaja);
                    }
                }

                if (this.ModoPagos == "EFECTIVO") {
                    this.pagoForm.efectivo = "0.00";
                    this.pagoForm.importe = this.pagoForm.monto_venta;
                    this.pagoForm.fecha_pago = null;
                    this.pagoForm.hora_pago = null;
                }

                if (this.modoPagos !== 'EFECTIVO') {
                    const ahora = new Date();

                    const year = ahora.getFullYear();
                    const month = String(ahora.getMonth() + 1).padStart(2, '0');
                    const day = String(ahora.getDate()).padStart(2, '0');
                    const hours = String(ahora.getHours()).padStart(2, '0');
                    const minutes = String(ahora.getMinutes()).padStart(2, '0');

                    this.pagoForm.fecha_pago = `${year}-${month}-${day}`;
                    this.pagoForm.hora_pago = `${hours}:${minutes}`;
                }

            } catch (ex) {

            }
        },
        changeEfectivo(value) {
            try {
                //========= ELIMINAMOS CARACTERES NO NUMÉRICOS =======
                const valorFiltrado = value.replace(/[^0-9.]/g, '');
                this.pagoForm.efectivo = valorFiltrado;

                //======= SI EL VALOR FILTRADO TIENE ALGO =====
                if (valorFiltrado) {

                    //======= EVITAR QUE EL VALOR FILTRADO SEA MAYOR AL MONTO DE VENTA =======
                    if (parseFloat(valorFiltrado) > parseFloat(this.pagoForm.monto_venta)) {
                        const cleanedValue = valorFiltrado.slice(0, -1);
                        this.pagoForm.efectivo = cleanedValue;
                        return;
                    }
                    //======== CALCULAMOS AUTOMÁTICAMENTE EL VALOR DE IMPORTE ======
                    let monto = parseFloat(this.pagoForm.monto_venta);
                    let efectivo = parseFloat(valorFiltrado);

                    if (this.ModoPagos !== 'EFECTIVO') {
                        let diferencia = monto - efectivo;
                        this.pagoForm.importe = parseFloat(diferencia);
                    }

                } else {
                    //======= SI EL VALOR FILTRADO NO TIENE NADA =======
                    this.pagoForm.importe = this.pagoForm.monto_venta;
                }

                if (this.ModoPagos === "RECIBO DE CAJA") {
                    //======= SI EL IMPORTE ES MAYOR AL SALDO DE CAJA
                    if (parseFloat(this.pagoForm.importe) > parseFloat(this.saldoRecibosCaja)) {
                        this.pagoForm.importe = this.saldoRecibosCaja;
                    }
                }

            } catch (ex) {
                alert(ex);
            }
        },
        changeImporte(value) {
            try {
                //========= ELIMINAMOS CARACTERES NO NUMÉRICOS =======
                const valorFiltrado = value.replace(/[^0-9.]/g, '');
                this.pagoForm.importe = valorFiltrado;

                //======= SI EL VALOR FILTRADO TIENE ALGO =====
                if (valorFiltrado) {

                    //======= EVITAR QUE EL VALOR FILTRADO SEA MAYOR AL MONTO DE VENTA =======
                    if (parseFloat(valorFiltrado) > parseFloat(this.pagoForm.monto_venta)) {
                        const cleanedValue = valorFiltrado.slice(0, -1);
                        this.pagoForm.importe = cleanedValue;
                        return;
                    }
                    //======== CALCULAMOS AUTOMÁTICAMENTE EL VALOR DE IMPORTE ======
                    let monto = parseFloat(this.pagoForm.monto_venta);
                    let importe = parseFloat(valorFiltrado);

                    if (this.ModoPagos !== 'EFECTIVO') {
                        let diferencia = monto - importe;
                        this.pagoForm.efectivo = parseFloat(diferencia);
                    }
                } else {
                    //======= SI EL VALOR FILTRADO NO TIENE NADA =======
                    this.pagoForm.efectivo = this.pagoForm.monto_venta;
                }

                if (this.ModoPagos === "RECIBO DE CAJA") {
                    //======= SI EL IMPORTE ES MAYOR AL SALDO DE CAJA
                    if (parseFloat(this.pagoForm.importe) > parseFloat(this.saldoRecibosCaja)) {
                        this.pagoForm.importe = this.saldoRecibosCaja;
                        this.pagoForm.efectivo = parseFloat(this.pagoForm.monto_venta) - parseFloat(this.pagoForm.importe);
                    }
                }

            } catch (ex) {
                alert(ex);
            }
        },
        changeImagen() {

            try {
                var fileInput = document.getElementById('imagen');
                var filePath = fileInput.value;
                var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
                let $imagenPrevisualizacion = document.querySelector(".imagen");

                if (allowedExtensions.exec(filePath)) {

                    var userFile = document.getElementById('imagen');
                    userFile.src = URL.createObjectURL(event.target.files[0]);
                    var data = userFile.src;
                    $imagenPrevisualizacion.src = data;
                    //======= OBTENIENDO NAME DE LA IMG CARGADA EL INPUT FILE =========
                    const inputImagen = document.querySelector('#imagen');
                    const fileName = inputImagen.files[0].name;
                    document.querySelector('#imagen_txt').textContent = fileName;
                } else {
                    toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                    $('.imagen').attr("src", this.imgDefault);
                }
            } catch (ex) {

            }
        },
        changeImagen2() {
            try {
                var fileInput = document.getElementById('imagen2');
                var filePath = fileInput.value;
                var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
                let $imagenPrevisualizacion = document.querySelector(".imagen2");

                if (allowedExtensions.exec(filePath)) {
                    var userFile = document.getElementById('imagen2');
                    userFile.src = URL.createObjectURL(event.target.files[0]);
                    var data = userFile.src;
                    $imagenPrevisualizacion.src = data;
                    //======= OBTENIENDO NAME DE LA IMG CARGADA EL INPUT FILE =========
                    const inputImagen = document.querySelector('#imagen2');
                    const fileName = inputImagen.files[0].name;
                    document.querySelector('#imagen_txt2').textContent = fileName;
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
            $('#imagen_txt').addClass("selected").html(fileName);
            $('#imagen').val('');
        },
        LimpiarImgen2() {
            $('.imagen2').attr("src", this.imgDefault)
            var fileName = "Seleccionar"
            $('#imagen_txt2').addClass("selected").html(fileName);
            $('#imagen2').val('');
        },
        Limpiar() {
            this.LimpiarImgen();
            this.LimpiarImgen2();
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
                tipo_pago_id: 1,
            }
            console.log('limpio');
        }
    },
    mounted() {
        this.token = $('meta[name=csrf-token]').attr("content");
    },
    created() {
    }
}
</script>
