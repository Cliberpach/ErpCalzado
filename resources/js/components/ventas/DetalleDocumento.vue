<template>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class=""><b>Detalle del documento de venta</b></h4>
        </div>
        <div class="panel-body ibox-content">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <i class="fa fa-dashboard"></i>
                            </th>
                            <th class="text-center">PRODUCTO</th>
                            <th class="text-center">COLOR</th>
                            <template v-for="t in tallasEnCarrito">
                                <th class="text-center">{{ t.descripcion }}</th>
                            </template>
                            <th class="text-center">P. VENTA</th>
                            <th class="text-center">SUBTOTAL</th>
                            <th class="text-center">DSCTO %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-if="carrito.length > 0">
                            <tr v-for="(item, index) in carrito" :key="index">
                                <td class="text-center">
                                    <div class='btn-group'>
                                        <button type="button" class='btn btn-sm btn-warning btn-edit'
                                            style='color:white' @click.prevent="$emit('editarItem', item)">
                                            <i class='fa fa-edit'></i>
                                        </button>
                                        <button type="button" class='btn btn-sm btn-danger btn-delete'
                                            style='color:white' @click.prevent="$emit('eliminarItem', item, index)">
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="width: 160px;">
                                        {{ item.producto_nombre }}
                                    </div>
                                </td>
                                <td>
                                    {{ item.color_nombre }}
                                </td>
                                <td v-for="t in tallasEnCarrito" :key="t.id">
                                    <p style="font-weight: bold;">{{ printTallaDetalle(t.id, item) }}</p>
                                </td>
                                <td>
                                    <div v-if="item.precio_venta !== item.precio_venta_nuevo">
                                        <del style="color: gray;">{{ item.precio_venta.toFixed(2) }}</del><br>
                                        <strong>{{ item.precio_venta_nuevo.toFixed(2) }}</strong>
                                    </div>
                                    <div v-else>
                                        {{ item.precio_venta_nuevo.toFixed(2) }}
                                    </div>
                                </td>
                                <td>
                                    <div v-if="Number(item.subtotal) !== Number(item.subtotal_nuevo)">
                                        <del style="color: gray;">{{ Number(item.subtotal).toFixed(2) }}</del><br>
                                        <strong>{{ Number(item.subtotal_nuevo).toFixed(2) }}</strong>
                                    </div>
                                    <div v-else>
                                        {{ Number(item.subtotal_nuevo).toFixed(2) }}
                                    </div>
                                </td>
                                <td>
                                    <input @input="$emit('descuento', item.producto_id, item.color_id, $event)"
                                        type="text" :value="item.porcentaje_descuento" style="width: 100px;"
                                        class="form-control">
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 5" class="text-center">
                                    <strong>No hay detalles</strong>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <div class="table-responsive">
                    <table style="margin:0 0 0 auto;">
                        <tfoot>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    SUBTOTAL:</td>
                                <td class="subtotal" colspan="1" style="font-weight: bold; text-align:end;">
                                    {{ `S/. ${Number(monto_subtotal).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    EMBALAJE:</td>
                                <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                    <div class="input-group">
                                        <span class="input-group-text" id="embalaje-addon">
                                            <svg style="width: 20px;" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 640 512">
                                                <path
                                                    d="M425.7 256c-16.9 0-32.8-9-41.4-23.4L320 126l-64.2 106.6c-8.7 14.5-24.6 23.5-41.5 23.5-4.5 0-9-.6-13.3-1.9L64 215v178c0 14.7 10 27.5 24.2 31l216.2 54.1c10.2 2.5 20.9 2.5 31 0L551.8 424c14.2-3.6 24.2-16.4 24.2-31V215l-137 39.1c-4.3 1.3-8.8 1.9-13.3 1.9zm212.6-112.2L586.8 41c-3.1-6.2-9.8-9.8-16.7-8.9L320 64l91.7 152.1c3.8 6.3 11.4 9.3 18.5 7.3l197.9-56.5c9.9-2.9 14.7-13.9 10.2-23.1zM53.2 41L1.7 143.8c-4.6 9.2 .3 20.2 10.1 23l197.9 56.5c7.1 2 14.7-1 18.5-7.3L320 64 69.8 32.1c-6.9-.8-13.5 2.7-16.6 8.9z" />
                                            </svg>
                                        </span>
                                        <input style="width: 70px;" :value="monto_embalaje"
                                            @input="$emit('update:monto_embalaje', $event.target.value)" type="text"
                                            class="form-control" aria-label="PRECIO DESPACHO"
                                            aria-describedby="embalaje-addon">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    ENVÍO:</td>
                                <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                    <div class="input-group">
                                        <span class="input-group-text btn"
                                            :class="hayDatosEnvio ? 'btn-success' : 'btn-light'" id="envio-addon"
                                            @click.prevent="$emit('setDataEnvio')">
                                            <i class="fas fa-truck"></i>
                                        </span>
                                        <input style="width: 70px;" :value="monto_envio"
                                            @input="$emit('update:monto_envio', $event.target.value)" type="text"
                                            class="form-control" aria-label="PRECIO ENVÍO"
                                            aria-describedby="envio-addon">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    DESCUENTO:</td>
                                <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                    <span>{{ `S/. ${formatoNumero(monto_descuento)}` }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    TOTAL:</td>
                                <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                    {{ `S/. ${formatoNumero(monto_total)}` }}
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    IGV:</td>
                                <td class="igv" colspan="1" style="font-weight: bold; text-align:end;">
                                    {{ `S/. ${formatoNumero(monto_igv)}` }}
                                </td>
                            </tr>
                            <tr>
                                <td :colspan="tallasEnCarrito.length + 4" style="font-weight: bold; text-align:end;">
                                    TOTAL A PAGAR:
                                </td>
                                <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
                                    {{ `S/. ${formatoNumero(monto_total_pagar)}` }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
export default {
    name: 'DetalleDocumento',
    props: [
        'carrito',
        'tallas',
        'monto_subtotal',
        'monto_embalaje',
        'monto_envio',
        'monto_descuento',
        'monto_igv',
        'monto_total',
        'monto_total_pagar',
        'hayDatosEnvio',
    ],
    computed: {
        tallasEnCarrito() {
            const tallaIds = new Set();
            (this.carrito || []).forEach(item => {
                (item.tallas || []).forEach(t => tallaIds.add(Number(t.talla_id)));
            });
            return (this.tallas || []).filter(t => tallaIds.has(Number(t.id)));
        },
    },
    methods: {
        formatoNumero(valor) {
            return parseFloat(valor).toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        printTallaDetalle(talla_id, item) {
            const itemTalla = (item.tallas || []).find(t => Number(talla_id) === Number(t.talla_id));
            return itemTalla ? itemTalla.cantidad : '';
        },
    },
};
</script>
