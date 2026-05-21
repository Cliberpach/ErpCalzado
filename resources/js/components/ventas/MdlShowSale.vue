<template>
    <div>
        <SpinnerOverlay :visible="loading" />

        <div class="modal fade" id="mdlShowSale" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Detalle de Venta
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body mdl-show-sale-body">

                        <div v-if="!loading && !venta" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>Sin datos.</p>
                        </div>

                        <div v-if="venta">

                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#mdl-tab-doc">
                                        <i class="fas fa-receipt"></i> Documento
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#mdl-tab-detalle">
                                        <i class="fas fa-list"></i> Detalle
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#mdl-tab-pagos">
                                        <i class="fas fa-credit-card"></i> Pagos
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">

                                <!-- TAB: DOCUMENTO -->
                                <div class="tab-pane fade show active" id="mdl-tab-doc">

                                    <div class="row mb-3">
                                        <div class="col-md-3"><strong>Serie:</strong> {{ venta.serie }}</div>
                                        <div class="col-md-3"><strong>Correlativo:</strong> {{ venta.correlativo }}</div>
                                        <div class="col-md-3"><strong>Fecha:</strong> {{ venta.fecha_documento }}</div>
                                        <div class="col-md-3"><strong>Condición:</strong> {{ venta.condicion_pago_nombre }}</div>
                                    </div>

                                    <hr>

                                    <h6 class="font-weight-bold">
                                        <i class="fas fa-building text-primary"></i> Empresa
                                    </h6>
                                    <div class="row mb-3">
                                        <div class="col-md-4">RUC: {{ venta.ruc_empresa }}</div>
                                        <div class="col-md-4">Empresa: {{ venta.empresa }}</div>
                                        <div class="col-md-4">Almacén: {{ venta.almacen_nombre }}</div>
                                        <div class="col-12 mt-1">Dirección: {{ venta.direccion_fiscal_empresa }}</div>
                                    </div>

                                    <hr>

                                    <h6 class="font-weight-bold">
                                        <i class="fas fa-user text-success"></i> Cliente
                                    </h6>
                                    <div class="row mb-3">
                                        <div class="col-md-4">Tipo Doc: {{ venta.tipo_documento_cliente }}</div>
                                        <div class="col-md-4">N° Doc: {{ venta.documento_cliente }}</div>
                                        <div class="col-md-4">Cliente: {{ venta.cliente }}</div>
                                        <div class="col-12 mt-1">Dirección: {{ venta.direccion_cliente }}</div>
                                    </div>

                                    <hr>

                                    <h6 class="font-weight-bold">
                                        <i class="fas fa-coins text-warning"></i> Totales
                                    </h6>
                                    <div class="row mb-3">
                                        <div class="col-md-3 mb-2">
                                            <div class="card shadow-sm border-left-secondary h-100">
                                                <div class="card-body py-3 text-center">
                                                    <i class="fas fa-calculator fa-lg text-secondary mb-1"></i>
                                                    <div class="text-muted font-weight-bold small">Sub Total</div>
                                                    <h5 class="font-weight-bold mb-0">S/ {{ fmt(venta.sub_total) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="card shadow-sm border-left-warning h-100">
                                                <div class="card-body py-3 text-center">
                                                    <i class="fas fa-percentage fa-lg text-warning mb-1"></i>
                                                    <div class="text-muted font-weight-bold small">IGV</div>
                                                    <h5 class="font-weight-bold mb-0">S/ {{ fmt(venta.total_igv) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="card shadow-sm border-left-primary h-100">
                                                <div class="card-body py-3 text-center">
                                                    <i class="fas fa-coins fa-lg text-primary mb-1"></i>
                                                    <div class="text-muted font-weight-bold small">Total</div>
                                                    <h5 class="font-weight-bold mb-0">S/ {{ fmt(venta.total) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="card shadow border-left-success h-100">
                                                <div class="card-body py-3 text-center">
                                                    <i class="fas fa-money-bill-wave fa-lg text-success mb-1"></i>
                                                    <div class="text-muted font-weight-bold small">Total a Pagar</div>
                                                    <h4 class="font-weight-bold text-success mb-0">S/ {{ fmt(venta.total_pagar) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-3">
                                            Estado:
                                            <span :class="badgeEstado(venta.estado)">{{ venta.estado }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            Pago:
                                            <span :class="badgePago(venta.estado_pago)">{{ venta.estado_pago }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            Despacho:
                                            <span :class="badgeDespacho(venta.estado_despacho)">{{ venta.estado_despacho }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            Registrador: <strong>{{ venta.registrador_nombre }}</strong>
                                        </div>
                                    </div>

                                    <div class="row mt-2" v-if="venta.observacion">
                                        <div class="col-12">
                                            Observación: <em>{{ venta.observacion }}</em>
                                        </div>
                                    </div>

                                </div>

                                <!-- TAB: DETALLE -->
                                <div class="tab-pane fade" id="mdl-tab-detalle">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Producto</th>
                                                    <th>Color</th>
                                                    <th>Talla</th>
                                                    <th>Modelo</th>
                                                    <th class="text-right">Cant.</th>
                                                    <th class="text-right">P. Unit.</th>
                                                    <th class="text-right">Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(d, i) in detalles" :key="d.id">
                                                    <td>{{ i + 1 }}</td>
                                                    <td>{{ d.nombre_producto }}</td>
                                                    <td>{{ d.nombre_color }}</td>
                                                    <td>{{ d.nombre_talla }}</td>
                                                    <td>{{ d.nombre_modelo || '-' }}</td>
                                                    <td class="text-right">{{ d.cantidad }}</td>
                                                    <td class="text-right">{{ fmt(d.precio_unitario_nuevo) }}</td>
                                                    <td class="text-right">{{ fmt(d.importe_nuevo) }}</td>
                                                </tr>
                                                <tr v-if="!detalles.length">
                                                    <td colspan="8" class="text-center text-muted">Sin detalles</td>
                                                </tr>
                                            </tbody>
                                            <tfoot v-if="detalles.length">
                                                <tr class="font-weight-bold">
                                                    <td colspan="7" class="text-right">Total:</td>
                                                    <td class="text-right">S/ {{ fmt(totalDetalles) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB: PAGOS -->
                                <div class="tab-pane fade" id="mdl-tab-pagos">
                                    <div class="row">

                                        <!-- PAGO 1 -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-left-primary shadow-sm h-100">
                                                <div class="card-body">
                                                    <h6 class="text-primary font-weight-bold mb-3">
                                                        <i class="fas fa-credit-card"></i> Pago 1
                                                    </h6>
                                                    <div class="row">
                                                        <div :class="venta.pago_1_img_ruta ? 'col-md-8' : 'col-12'">
                                                            <div class="mb-2">
                                                                <small class="text-muted">Método</small><br>
                                                                <strong>{{ venta.pago_1_tipo_pago_nombre || '-' }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">Monto</small><br>
                                                                <strong class="text-success">S/ {{ fmt(venta.pago_1_monto) }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">N° Operación</small><br>
                                                                <strong>{{ venta.pago_1_nro_operacion || '-' }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">Fecha</small><br>
                                                                <strong>{{ venta.pago_1_fecha_operacion || '-' }}</strong>
                                                            </div>
                                                            <div v-if="venta.pago_1_banco_nombre">
                                                                <small class="text-muted">Banco / Cuenta</small><br>
                                                                <strong>{{ venta.pago_1_banco_nombre }} {{ venta.pago_1_nro_cuenta ? '· ' + venta.pago_1_nro_cuenta : '' }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 text-center" v-if="venta.pago_1_img_ruta">
                                                            <small class="text-muted d-block mb-1">Imagen</small>
                                                            <a :href="imgUrl(venta.pago_1_img_ruta)" target="_blank">
                                                                <img :src="imgUrl(venta.pago_1_img_ruta)"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 120px; cursor: pointer;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PAGO 2 -->
                                        <div class="col-md-6 mb-3" v-if="venta.pago_2_monto">
                                            <div class="card border-left-success shadow-sm h-100">
                                                <div class="card-body">
                                                    <h6 class="text-success font-weight-bold mb-3">
                                                        <i class="fas fa-credit-card"></i> Pago 2
                                                    </h6>
                                                    <div class="row">
                                                        <div :class="venta.pago_2_img_ruta ? 'col-md-8' : 'col-12'">
                                                            <div class="mb-2">
                                                                <small class="text-muted">Método</small><br>
                                                                <strong>{{ venta.pago_2_tipo_pago_nombre || '-' }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">Monto</small><br>
                                                                <strong class="text-success">S/ {{ fmt(venta.pago_2_monto) }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">N° Operación</small><br>
                                                                <strong>{{ venta.pago_2_nro_operacion || '-' }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <small class="text-muted">Fecha</small><br>
                                                                <strong>{{ venta.pago_2_fecha_operacion || '-' }}</strong>
                                                            </div>
                                                            <div v-if="venta.pago_2_banco_nombre">
                                                                <small class="text-muted">Banco / Cuenta</small><br>
                                                                <strong>{{ venta.pago_2_banco_nombre }} {{ venta.pago_2_nro_cuenta ? '· ' + venta.pago_2_nro_cuenta : '' }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 text-center" v-if="venta.pago_2_img_ruta">
                                                            <small class="text-muted d-block mb-1">Imagen</small>
                                                            <a :href="imgUrl(venta.pago_2_img_ruta)" target="_blank">
                                                                <img :src="imgUrl(venta.pago_2_img_ruta)"
                                                                    class="img-fluid rounded shadow-sm"
                                                                    style="max-height: 120px; cursor: pointer;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12" v-if="!venta.pago_1_monto && !venta.pago_2_monto">
                                            <p class="text-muted text-center">Sin información de pagos.</p>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SpinnerOverlay from '../shared/SpinnerOverlay.vue';

export default {
    name: 'MdlShowSale',
    components: { SpinnerOverlay },

    data() {
        return {
            venta:    null,
            detalles: [],
            loading:  false,
        };
    },

    computed: {
        totalDetalles() {
            return this.detalles.reduce((sum, d) => sum + (parseFloat(d.importe_nuevo) || 0), 0);
        },
    },

    methods: {
        abrir(id) {
            this.venta    = null;
            this.detalles = [];
            $('#mdlShowSale').modal('show');
            if (id) this.getVenta(id);
        },

        async getVenta(id) {
            this.loading = true;
            try {
                const res = await axios.get(route('ventas.documento.getShow', { id }));
                if (res.data.success) {
                    this.venta    = res.data.data.sale;
                    this.detalles = res.data.data.detalles;
                } else {
                    toastr.error(res.data.message, 'Error en el servidor');
                }
            } catch (error) {
                toastr.error(error, 'ERROR AL OBTENER VENTA');
            } finally {
                this.loading = false;
            }
        },

        fmt(val) {
            const n = parseFloat(val);
            return isNaN(n) ? '0.00' : n.toFixed(2);
        },

        imgUrl(ruta) {
            return ruta ? `/storage/${ruta}` : null;
        },

        badgeEstado(v) {
            if (v === 'ANULADO') return 'badge badge-danger';
            return 'badge badge-success';
        },

        badgePago(v) {
            if (v === 'PENDIENTE') return 'badge badge-danger';
            if (v === 'PAGADA')    return 'badge badge-success';
            return 'badge badge-secondary';
        },

        badgeDespacho(v) {
            if (v === 'PENDIENTE')  return 'badge badge-danger';
            if (v === 'EMBALADO')   return 'badge badge-warning';
            if (v === 'DESPACHADO') return 'badge badge-success';
            return 'badge badge-secondary';
        },
    },
};
</script>

<style>
.mdl-show-sale-body {
    font-size: 15px;
}
.mdl-show-sale-body small {
    font-size: 13px;
}
.mdl-show-sale-body strong {
    font-size: 15.5px;
}
.mdl-show-sale-body h6 {
    font-size: 16px;
}
</style>
