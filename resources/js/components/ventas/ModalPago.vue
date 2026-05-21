<template>
    <div>
        <SpinnerOverlay :visible="spinnerVisible" />

        <div class="modal inmodal fade" id="modal_pago" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <button type="button" class="close text-white" data-dismiss="modal" @click="limpiar">
                            <span>&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fas fa-file-invoice-dollar mr-1"></i>
                            {{ numDoc }}
                        </h4>
                        <small class="font-bold">{{ clienteNombre }}</small>
                    </div>

                    <div class="modal-body">

                        <div class="alert alert-info py-2 mb-3">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Total a pagar: <strong>S/ {{ fmtTotal }}</strong>
                        </div>

                        <PagosComponentVue
                            ref="pagosRef"
                            :lstMetodosPago="lstMetodosPago"
                            :montoTotal="total"
                            @show-spinner="spinnerVisible = true"
                            @hide-spinner="spinnerVisible = false"
                        />

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="pagar">
                            <i class="fa fa-save"></i> Pagar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" @click="limpiar">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PagosComponentVue from './PagosComponent.vue';
import SpinnerOverlay    from '../shared/SpinnerOverlay.vue';

export default {
    name: 'ModalPago',
    components: { PagosComponentVue, SpinnerOverlay },

    props: {
        lstMetodosPago: {
            type:    Array,
            default: () => [],
        },
    },

    data() {
        return {
            ventaId:        null,
            numDoc:         '',
            clienteNombre:  '',
            total:          0,
            spinnerVisible: false,
        };
    },

    computed: {
        fmtTotal() {
            return parseFloat(this.total || 0).toFixed(2);
        },
    },

    methods: {
        abrir(row) {
            this.ventaId      = row.id;
            this.numDoc       = row.numero_doc;
            this.clienteNombre = row.cliente;
            this.total        = parseFloat(row.total_pagar ?? row.total ?? 0);
            $('#modal_pago').modal('show');
        },

        limpiar() {
            this.ventaId       = null;
            this.numDoc        = '';
            this.clienteNombre = '';
            this.total         = 0;
        },

        async pagar() {
            const { lstPagos } = this.$refs.pagosRef.getData();

            if (!this.$refs.pagosRef.validar(this.total)) return;

            const p1 = lstPagos[0];
            const p2 = lstPagos[1] ?? null;

            // Mapear al formato esperado por storePago
            let tipo_pago_id = p1.metodoPagoId;
            let cuenta_id    = p1.cuentaPagoId ?? '';
            let nro_op       = p1.nroOperacionPago ?? '';
            let fecha_pago   = p1.fechaOperacionPago ?? '';
            let importe      = parseFloat(p1.montoPago);
            let efectivo     = 0;

            if (p2) {
                const cashPago    = lstPagos.find(p => p.metodoPagoId == 1);
                const nonCashPago = lstPagos.find(p => p.metodoPagoId != 1);
                efectivo     = parseFloat(cashPago?.montoPago ?? 0);
                importe      = parseFloat(nonCashPago?.montoPago ?? 0);
                tipo_pago_id = nonCashPago?.metodoPagoId ?? p1.metodoPagoId;
                cuenta_id    = nonCashPago?.cuentaPagoId ?? '';
                nro_op       = nonCashPago?.nroOperacionPago ?? '';
                fecha_pago   = nonCashPago?.fechaOperacionPago ?? p1.fechaOperacionPago ?? '';
            } else if (p1.metodoPagoId == 1) {
                efectivo = importe;
                importe  = 0;
            }

            const ahora     = new Date();
            const hora_pago = `${String(ahora.getHours()).padStart(2,'0')}:${String(ahora.getMinutes()).padStart(2,'0')}`;

            const formData = new FormData();
            formData.append('venta_id',      this.ventaId);
            formData.append('monto_venta',   this.total);
            formData.append('tipo_pago_id',  tipo_pago_id);
            formData.append('importe',       importe);
            formData.append('efectivo',      efectivo);
            formData.append('cuenta_id',     cuenta_id);
            formData.append('nro_operacion', nro_op);
            formData.append('fecha_pago',    fecha_pago);
            formData.append('hora_pago',     hora_pago);
            formData.append('modo_pago',     tipo_pago_id);

            if (p1.imgPago)  formData.append('imagen',  p1.imgPago);
            if (p2?.imgPago) formData.append('imagen2', p2.imgPago);

            Swal.fire({
                title: 'Registrando pago...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const res = await axios.post(route('ventas.documento.storePago'), formData);
                if (res.data.success) {
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    $('#modal_pago').modal('hide');
                    this.$emit('pago-registrado');
                    this.limpiar();
                } else {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN');
            } finally {
                Swal.close();
            }
        },
    },
};
</script>
