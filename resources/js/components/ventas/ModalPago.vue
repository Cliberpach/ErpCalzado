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

            const ahora     = new Date();
            const hora_pago = `${String(ahora.getHours()).padStart(2,'0')}:${String(ahora.getMinutes()).padStart(2,'0')}`;

            // Serializar lstPagos sin el campo imgPago (los archivos van por separado)
            const pagosData = lstPagos.map(p => ({
                metodoPagoId:       p.metodoPagoId,
                cuentaPagoId:       p.cuentaPagoId,
                montoPago:          p.montoPago,
                fechaOperacionPago: p.fechaOperacionPago,
                nroOperacionPago:   p.nroOperacionPago,
            }));

            const formData = new FormData();
            formData.append('venta_id',    this.ventaId);
            formData.append('monto_venta', this.total);
            formData.append('hora_pago',   hora_pago);
            formData.append('lstPagos',    JSON.stringify(pagosData));

            // Imágenes por separado indexadas (imagen_0, imagen_1)
            lstPagos.forEach((p, i) => {
                if (p.imgPago) formData.append(`imagen_${i}`, p.imgPago);
            });

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
