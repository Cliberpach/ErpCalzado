<template>
    <div class="modal inmodal" id="modal-codigo-precio" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xs">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <i class="fa fa-cogs modal-icon"></i>
                    <h4 class="modal-title">CODIGO</h4>
                    <small class="font-bold">Ingresar</small>
                </div>
                <div class="modal-body">
                    <form id="frmCodigo" @submit.prevent="AgregarCodigo">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" v-if="estadoPrecioMenor=='1'">
                                    <label class="required">Código para vender a menor precio</label>
                                    <input type="password" class="form-control" id="codigo_precio_menor"
                                        placeholder="Código" autocomplete="off" required v-model="codigoPrecioMenor">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                            campos
                            marcados con asterisco (*) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" class="btn btn-primary btn-sm" form="frmCodigo" style="color:white;"><i
                                class="fa fa-save"></i> Guardar</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "ModalCodigoPrecioMenor",
    props: {
        estadoPrecioMenor: {
            type: String,
            required: true,
            default: ""
        }
    },
    data() {
        return {
            codigoPrecioMenor: ""
        }
    },
    methods: {
        AgregarCodigo() {

            if (this.estadoPrecioMenor == '1') {
                if (this.codigoPrecioMenor != "") {

                    let obj = {
                        condicion: "MODAL",
                        codigoPrecio: this.codigoPrecioMenor
                    }

                    this.$emit("addCodigoPrecioMenor", obj);
                    this.codigoPrecioMenor="";

                }else{
                    toastr.error("Ingresa el codigo autorizado");
                }
            }
        }
    }
}
</script>