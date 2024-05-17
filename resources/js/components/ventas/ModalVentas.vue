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
                        <h4 class="modal-title">VENTAS PENDIENTES DE PAGO </h4>
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
        <ModalPagoVue :modoPagos="modoPagos" :imgDefault="imgDefault" :cuentas="cuentas" :pagos="formPago" 
        :cliente_id="cliente_id" :recibos_caja="recibos_caja" :saldoRecibosCaja="saldoRecibosCaja"/>
    </div>

</template>
<script>
import ModalPagoVue from './ModalPago.vue';
export default {
    name: "ModalVentas",
    props: {
        ventasPendientes: [],
        imgDefault: "",
        modoPagos: [],
        cliente_id:null
    },
    components: {
        ModalPagoVue
    },
    data() {
        return {
            saldoRecibosCaja:0,
            recibos_caja:[],
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
                didOpen: async () => {
                    Swal.showLoading();
                    Swal.stopTimer();

                    //============ OBTENER LAS CUENTAS BANCARIAS DE LA EMPRESA ==========
                    const res_cuentas   =   await this.getCuentas(item.empresa_id);
                    if(res_cuentas){
                        //========= OBTENER LOS RECIBOS DE CAJA DEL CLIENTE ========
                        const res_recibos_caja  =   await this.getRecibosCaja(this.cliente_id);
                        if(res_recibos_caja){
                            me.formPago = item;
                            $("#modal_pago").modal("show");
                            timerInterval = 0;
                            Swal.resumeTimer();
                        }
                    }

                    


                    // $.ajax({
                    //     dataType: 'json',
                    //     type: 'post',
                    //     url: route('ventas.documento.getCuentas'),
                    //     data: {
                    //         '_token': $('meta[name=csrf-token]').val(),
                    //         'empresa_id': item.empresa_id
                    //     },
                    //     success: function (response) {
                            
                    //         if (response.success) {
                    //             console.log(item)
                    //             me.cuentas = response.cuentas;
                    //             me.formPago = item;
                    //             $("#modal_pago").modal("show");
                    //             timerInterval = 0;
                    //             Swal.resumeTimer();
                    //         } else {
                    //             timerInterval = 0;
                    //             Swal.resumeTimer();
                    //         }
                    //     }
                    // });
                },
                willClose: () => {
                    clearInterval(timerInterval)
                }
            });
        },
        async getCuentas(empresa_id){
            try {
                const res   =   await axios.post(route('ventas.documento.getCuentas'),{empresa_id});
                if(res.data.success){
                    //========= COLOCAMOS LAS CUENTAS DE LA EMPRESA ============
                    this.cuentas      =   res.data.cuentas;
                    return true;
                }else{
                    toastr.error('ERROR AL OBTENER LAS CUENTAS BANCARIAS DE LA EMPRESA','ERROR EN EL SERVIDOR');
                    return false;
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR AL OBTENER CUENTAS BANCARIAS DE LA EMPRESA');
                return false;
            }
        },
        async getRecibosCaja(cliente_id){
            try {
                const res       =   await axios.get(route('ventas.documento.getRecibosCaja',cliente_id));
                if(res.data.success){
                    //========== COLOCAMOS LOS RECIBOS =========
                    this.recibos_caja       =   res.data.recibos_caja;

                    this.recibos_caja.forEach((recibo)=>{
                        this.saldoRecibosCaja   +=  parseFloat(recibo.saldo);
                    })

                    return true;
                }else{
                    toastr.error(res.data.exception,res.data.message);
                    return false;
                }
            } catch (error) {
                toastr.error(error,'ERROR EN EL SERVIDOR AL OBTENER RECIBOS DE CAJA');
                return false;
            }
        }


    }
}
</script>