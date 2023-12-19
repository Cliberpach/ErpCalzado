<template>
    <div>
        <div class="modal inmodal" id="modal_ventas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">VENTAS PENDIENTES DE PAGO</h4>
                        <small class="font-bold ventas-title"></small>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table
                                        class="table table-ventaPendientes table-striped table-bordered table-hover table-sm">
                                        <thead>
                                            <th class="text-center letrapequeña">TIPO DOC</th>
                                            <th class="text-center letrapequeña"># DOC</th>
                                            <th class="text-center letrapequeña">FECHA DOC</th>
                                            <th class="text-center letrapequeña">MONTO</th>
                                            <th class="text-center letrapequeña"><i class="fa fa-dashboard"></i></th>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item,index) in ventasPendientes" :key="index">
                                                <td class="text-center letrapequeña">{{ item.tipo_venta }}</td>
                                                <td class="text-center letrapequeña">{{ item.numero_doc }}</td>
                                                <td class="text-center letrapequeña">{{ item.fecha_documento }}</td>
                                                <td class="text-center letrapequeña">{{ item.total }}</td>
                                                <td class="text-center letrapequeña">
                                                    <template
                                                        v-if="item.condicion == 'CONTADO' && item.estado == 'PENDIENTE' && item.tipo_venta_id == '129'">
                                                        <button type='button' class='btn btn-sm btn-primary'
                                                            title='Pagar' @click.prevent="Pagar(item)"><i
                                                                class='fa fa-money'></i> Pagar</button>
                                                    </template>
                                                    <template
                                                        v-if="item.condicion == 'CONTADO' && item.estado == 'PENDIENTE' && item.tipo_venta_id != 129 && (item.convertir == '' || item.convertir == null)">
                                                        <button type='button' class='btn btn-sm btn-primary'
                                                            title='Pagar' @click.prevent="Pagar(item)"><i
                                                                class='fa fa-money'></i> Pagar</button>
                                                    </template>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6 text-left">
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                    class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <ModalPagoVue :modoPagos="modoPagos" :imgDefault="imgDefault" :cuentas="cuentas" :pagos="formPago"/>
    </div>

</template>
<script>
import ModalPagoVue from './ModalPago.vue';
export default {
    name: "ModalVentas",
    props: {
        ventasPendientes: [],
        imgDefault: "",
        modoPagos: []
    },
    components: {
        ModalPagoVue
    },
    data() {
        return {
            cuentas: [],
            formPago:null,
            loading:false
        }
    },
    methods: {
        Pagar(item) {

            let timerInterval;
            let me = this;
            me.formPago = null;
            Swal.fire({
                title: 'Cargando...',
                icon: 'info',
                customClass: {
                    container: 'my-swal'
                },
                timer: 10,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    Swal.stopTimer();
                    $.ajax({
                        dataType: 'json',
                        type: 'post',
                        url: route('ventas.documento.getCuentas'),
                        data: {
                            '_token': $('meta[name=csrf-token]').val(),
                            'empresa_id': item.empresa_id
                        },
                        success: function (response) {
                            
                            if (response.success) {
                                me.cuentas = response.cuentas;
                                me.formPago = item;
                                $("#modal_pago").modal("show");
                                timerInterval = 0;
                                Swal.resumeTimer();
                            } else {
                                timerInterval = 0;
                                Swal.resumeTimer();
                            }
                        }
                    });
                },
                willClose: () => {
                    clearInterval(timerInterval)
                }
            });
        }
    }
}
</script>