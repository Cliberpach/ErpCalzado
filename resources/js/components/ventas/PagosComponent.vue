<template>
    <div class="row">
        <div class="col-12">
            <div class="panel panel-success">

                <div class="panel-heading d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card text-light mr-2"></i>
                        <b class="text-white">Datos de Pago</b>
                    </h4>
                    <button type="button" class="btn btn-light btn-sm" @click="agregarPago"
                        v-if="lstPagos.length < 2 && isPay">
                        <i class="fas fa-plus"></i> Agregar Pago
                    </button>
                </div>

                <div class="bg-light border-bottom px-3 py-2 d-flex align-items-center">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="chkPagado" v-model="isPay"
                            style="transform: scale(1.3); cursor:pointer;">
                        <label class="form-check-label font-weight-bold ml-2 px-3 py-1 rounded"
                            :class="isPay ? 'bg-success text-white' : 'bg-danger text-white'"
                            for="chkPagado" style="cursor:pointer;">
                            {{ isPay ? 'PAGAR' : 'PENDIENTE' }}
                        </label>
                    </div>
                </div>

                <div class="panel-body ibox-content" :class="{ 'd-none': !isPay }">

                    <div v-for="(pago, index) in lstPagos" :key="index"
                        class="border rounded p-3 mb-3 shadow-sm">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong class="text-dark">
                                <i class="fas fa-receipt text-info mr-1"></i>
                                Pago {{ index + 1 }}
                            </strong>
                            <button class="btn btn-danger btn-sm" @click="eliminarPago(index)"
                                v-if="lstPagos.length > 1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="row">

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">MÉTODO</label>
                                <v-select v-model="pago.metodoPagoId" :options="lstMetodosPago"
                                    :reduce="a => a.id" label="descripcion"
                                    placeholder="Seleccionar método"
                                    @input="onChangeMetodoPago(pago, index)">
                                </v-select>
                                <span :class="`msgError text-danger pagos_${index}_metodoPagoId_error`"></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">CUENTA</label>
                                <v-select v-model="pago.cuentaPagoId" :options="pago.lstCuentas || []"
                                    :reduce="a => a.cuenta_id" label="cuentaLabel"
                                    placeholder="Seleccionar cuenta">
                                </v-select>
                                <span :class="`msgError text-danger pagos_${index}_cuentaPagoId_error`"></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">MONTO</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-coins text-warning"></i>
                                        </span>
                                    </div>
                                    <input v-model="pago.montoPago" type="text" class="form-control"
                                        placeholder="Ingrese monto" @input="onMontoPagoInput($event, index)">
                                </div>
                                <span :class="`msgError text-danger pagos_${index}_montoPago_error`"></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">FECHA</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-calendar-alt text-warning"></i>
                                        </span>
                                    </div>
                                    <input :max="new Date().toISOString().substr(0, 10)"
                                        v-model="pago.fechaOperacionPago" type="date" class="form-control">
                                </div>
                                <span :class="`msgError text-danger pagos_${index}_fechaOperacionPago_error`"></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">NRO OPERACIÓN</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-hashtag text-secondary"></i>
                                        </span>
                                    </div>
                                    <input v-model="pago.nroOperacionPago" type="text" class="form-control"
                                        placeholder="Ingrese nro de operación">
                                </div>
                                <span :class="`msgError text-danger pagos_${index}_nroOperacionPago_error`"></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                <label class="font-weight-bold">IMAGEN</label>
                                <input type="file" class="img-filepond" :data-index="index">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style>
@import "../../../css/filepond.css";
</style>

<script>
import FilePond from '../../libs/global/filepond';

export default {
    name: 'PagosComponent',
    props: {
        lstMetodosPago: {
            type: Array,
            default: () => [],
        },
        montoTotal: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            lstPagos: [this.getNuevoPago(true)],
            isPay: true,
        };
    },
    watch: {
        lstPagos: {
            handler(val) {
                this.$emit('update-pagos', val);
            },
            deep: true,
        },
        montoTotal(val) {
            if (this.lstPagos.length === 1) {
                this.lstPagos[0].montoPago = val ? parseFloat(val).toFixed(2) : '';
            }
        },
        isPay(val) {
            this.$emit('update-isPay', val);
        },
    },
    mounted() {
        this.initFilePond();
        this.lstPagos.forEach((pago, index) => {
            if (pago.metodoPagoId) {
                this.onChangeMetodoPago(pago, index);
            }
        });
    },
    methods: {
        getNuevoPago(isFirst = false) {
            return {
                metodoPagoId:      isFirst ? 3 : null,
                cuentaPagoId:      null,
                montoPago:         '',
                fechaOperacionPago: new Date().toISOString().substr(0, 10),
                nroOperacionPago:  null,
                lstCuentas:        [],
                imgPago:           null,
            };
        },
        agregarPago() {
            if (this.lstPagos.length < 2) {
                this.lstPagos.push(this.getNuevoPago());
                this.$nextTick(() => this.initFilePond());
            }
        },
        eliminarPago(index) {
            if (this.lstPagos.length > 1) {
                this.lstPagos.splice(index, 1);
                this.$nextTick(() => {
                    document.querySelectorAll('.filepond').forEach(input => {
                        const inst = FilePond.find(input);
                        if (inst) inst.destroy();
                    });
                    this.initFilePond();
                });
            }
        },
        onChangeMetodoPago(pago, index) {
            if (pago.metodoPagoId) {
                pago.cuentaPagoId = null;
                this.getCuentasPorMetodoPago(pago.metodoPagoId, index);
            }
        },
        onMontoPagoInput(e, index) {
            let valor = e.target.value;
            valor = valor.replace(/[^0-9.]/g, '');
            valor = valor.replace(/(\..*?)\./g, '$1');
            valor = valor.replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2');
            e.target.value = valor;
            this.lstPagos[index].montoPago = valor;
        },
        initFilePond() {
            this.$nextTick(() => {
                this.$el.querySelectorAll('.img-filepond').forEach(input => {
                    if (!FilePond.find(input)) {
                        const pond = FilePond.create(input, {
                            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
                            fileValidateTypeLabelExpectedTypes: 'Solo JPG, PNG o WEBP',
                            maxFileSize: '5MB',
                            labelMaxFileSizeExceeded: 'Imagen muy grande',
                            labelMaxFileSize: 'Máximo {filesize}',
                        });
                        pond.on('updatefiles', (files) => {
                            const idx = input.dataset.index;
                            this.lstPagos[idx].imgPago = files.length ? files[0].file : null;
                        });
                    }
                });
            });
        },
        async getCuentasPorMetodoPago(metodoPagoId, index) {
            try {
                this.$emit('show-spinner');
                const res = await this.axios.get(
                    route('utilidades.getCuentasPorMetodoPago', metodoPagoId)
                );
                if (res.data.success) {
                    this.$set(this.lstPagos[index], 'lstCuentas', res.data.data);
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error, 'ERROR AL OBTENER CUENTAS');
            } finally {
                this.$emit('hide-spinner');
            }
        },
        getData() {
            return {
                lstPagos: this.lstPagos,
                isPay:    this.isPay,
            };
        },
        validar(montoTotalVenta) {
            if (!this.isPay) return true;

            const totalPagos = this.lstPagos.reduce((sum, p) => sum + (parseFloat(p.montoPago) || 0), 0);
            const total      = Math.round(parseFloat(montoTotalVenta) * 100) / 100;

            if (Math.round(totalPagos * 100) / 100 !== total) {
                toastr.error('LA SUMA DE LOS PAGOS NO COINCIDE CON EL TOTAL DE LA VENTA');
                return false;
            }

            for (let i = 0; i < this.lstPagos.length; i++) {
                const p = this.lstPagos[i];
                if (!p.metodoPagoId) {
                    toastr.error(`PAGO ${i + 1}: MÉTODO DE PAGO OBLIGATORIO`);
                    return false;
                }
                if (p.metodoPagoId != 1 && !p.cuentaPagoId) {
                    toastr.error(`PAGO ${i + 1}: CUENTA OBLIGATORIA PARA ESTE MÉTODO DE PAGO`);
                    return false;
                }
                if (!p.montoPago || parseFloat(p.montoPago) <= 0) {
                    toastr.error(`PAGO ${i + 1}: MONTO DEBE SER MAYOR A 0`);
                    return false;
                }
                if (p.metodoPagoId != 1 && !p.nroOperacionPago) {
                    toastr.error(`PAGO ${i + 1}: N° OPERACIÓN OBLIGATORIO`);
                    return false;
                }
                if (!p.fechaOperacionPago) {
                    toastr.error(`PAGO ${i + 1}: FECHA OBLIGATORIA`);
                    return false;
                }
                if (p.imgPago) {
                    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!allowed.includes(p.imgPago.type)) {
                        toastr.error(`PAGO ${i + 1}: IMAGEN DEBE SER JPG, PNG O WEBP`);
                        return false;
                    }
                    if (p.imgPago.size > 5 * 1024 * 1024) {
                        toastr.error(`PAGO ${i + 1}: IMAGEN NO DEBE SUPERAR LOS 5 MB`);
                        return false;
                    }
                }
            }

            return true;
        },
    },
};
</script>
