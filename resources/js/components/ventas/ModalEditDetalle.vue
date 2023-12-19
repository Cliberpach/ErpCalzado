<template>
    <div>
        <div class="modal inmodal" id="modal_editar_detalle" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" @click.prevent="cerrarModal('EDITAR')" class="close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <i class="fa fa-cogs modal-icon"></i>
                        <h4 class="modal-title">Detalle del documento de venta</h4>
                        <small class="font-bold">Editar detalle</small>
                    </div>
                    <div class="modal-body">
                        <form id="edit_detalle_venta" @submit.prevent="AddDetalle">

                            <div class="form-group">
                                <label class="col-form-label required">Producto-lote:</label>
                                <input type="text" class="form-control" v-model="producto" readonly>
                            </div>
                            <div class="form-group">
                                <label class="">Unidad de Medida</label>
                                <input type="text" id="medida_editar" class="form-control" v-model="unidadMedida"
                                    disabled>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6 col-xs-12">
                                    <label class="required">Cantidad</label>
                                    <input type="text" v-model="cantidad" :max="cantidadMax" class="form-control"
                                        min="1" required>
                                </div>
                                <div class="col-lg-6 col-xs-12">
                                    <label class="required">Precio</label>
                                    <input type="text" class="form-control" v-model="precio" maxlength="15" required>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <div class="col-md-6 text-left">
                            <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                class="leyenda-required">Los
                                campos marcados con asterisco (<label class="required"></label>) son
                                obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" id="btn_editar_detalle" form="edit_detalle_venta"
                                class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                            <button type="button" @click.prevent="cerrarModal('EDITAR')"
                                class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal inmodal" id="modal-codigo-precio-editar" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xs">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" @click.prevent="cerrarModal('CODIGO')">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <i class="fa fa-cogs modal-icon"></i>
                        <h4 class="modal-title">CODIGO</h4>
                        <small class="font-bold">Ingresar</small>
                    </div>
                    <div class="modal-body">
                        <form id="frmCodigoEditar" @submit.prevent="AddCodigo">
                            <div class="row">
                                <div class="col-12">
                                    <!-- @if (codigoPrecioMenor()->estado_precio_menor == '1') -->
                                    <div class="form-group" v-if="formEdit.estado_precio_menor=='1'">
                                        <label class="required">Código para vender a menor precio</label>
                                        <input type="password" class="form-control" v-model="codigoPrecioMenor"
                                            placeholder="Código" autocomplete="off" required>
                                    </div>
                                    <!-- @endif -->
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <div class="col-md-6 text-left">
                            <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                class="leyenda-required">Los campos
                                marcados con asterisco (*) son obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary btn-sm" form="frmCodigoEditar"
                                style="color:white;"><i class="fa fa-save"></i> Guardar</button>
                            <button type="button" class="btn btn-danger btn-sm"
                                @click.prevent="cerrarModal('CODIGO')"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { RedondearDecimales } from "../../helpers.js";
export default {
    name: "ModalEditaDetalle",
    props: {
        item: {
            type: Object,
            required: true,
            default: null
        },
        detalles: {
            type: Array,
            required: true,
            default: []
        }
    },
    data() {
        return {
            formEdit: {
                cantidad:0,
                cantidadMax:0,
                precio:0,
                precioMinimo:0,
                unidadMedida:"",
                id:0,
                producto:"",
                estado_precio_menor:"",
                codigo_precio_menor:"",
                igv:0
            },
            producto: "",
            cantidad: "",
            precio: "",
            unidadMedida: "",
            cantidadMax: "",
            codigoPrecioMenor: "",
            tablaDetalles: [],
        }
    },
    watch: {
        detalles(value) {
            this.tablaDetalles = value;
        },
        item: {
            handler(value) {
                if (value) {
                    this.formEdit = value;
                }
            },
            deep: true
        },
        formEdit: {
            handler(value) {
                this.cantidad = value.cantidad;
                this.precio = value.precio;
                this.unidadMedida = value.unidadMedida;
                this.cantidadMax = value.cantidadMax;
                this.producto = value.producto;
            },
            deep: true
        },
        cantidad(value) {
            let cant = !isNaN(Number(value)) ? Number(value) : 0;
            let max = this.cantidadMax;

            if (cant > max) {
                toastr.error('La cantidad ingresada supera al stock del producto Max(' + max + ').', 'Error');
                this.cantidad = max;
            }
        }
    },
    methods: {
        async AddDetalle(condicion = "VISTA") {
            try {
                let enviar = true;
                let cant = !isNaN(Number(this.cantidad)) ? Number(this.cantidad) : 0;
                let precio = !isNaN(Number(this.precio)) ? Number(this.precio) : 0;

                if (cant == 0)
                    throw 'Ingrese cantidad del Producto..';

                if (precio == 0)
                    throw 'Ingrese el precio del producto superior a 0.0.';

                if (precio < this.formEdit.precioMinimo) {
                    if (this.formEdit.estado_precio_menor == '1') {
                        if (this.codigoPrecioMenor != this.formEdit.codigo_precio_menor) {
                            if (condicion == 'MODAL') {
                                toastr.error('El codigo para poder vender a un precio menor a lo establecido es incorrecto.', 'Error');
                            }
                            else {
                                this.codigoPrecioMenor = "";
                                $('#modal-codigo-precio-editar').modal({ backdrop: 'static', keyboard: false });
                            }
                            enviar = false;
                        } else {
                            $("#modal-codigo-precio-editar").modal("hide");
                            $("body").addClass("modal-open");
                        }
                    }
                    else {
                        toastr.error('No puedes vender a un precio menor a lo establecido.', 'Error');
                        enviar = false;
                    }
                }

                if (enviar) {
                    if (cant > 0 && this.formEdit.cantidad > 0 && this.formEdit.id > 0) {
                        const {data} = await this.axios.post(route('ventas.documento.update.lote'),{
                            lote_id : this.formEdit.id,
                            cantidad_res : cant,
                            cantidad_sum : this.formEdit.cantidad,
                        });
                        const {success} = data;

                        if(!success){
                            toastr.warning('Ocurrió un error porfavor recargar la pagina.')
                        }else{
                            this.ActualizarTabla();
                        }
                    }else{
                        toastr.error('Cerrar ventana y volver a editar producto.', 'Error');
                    }
                }
            } catch (ex) {
                toastr.error(ex, 'Error');
            }
        },
        limpiar() {

        },
        AddCodigo() {
            this.AddDetalle("MODAL");
        },
        ActualizarTabla() {
            let pdescuento = 0;
            let precio_inicial = convertFloat(this.precio);
            let igv = convertFloat(this.formEdit.igv);
            let igv_calculado = convertFloat(igv / 100);

            let valor_unitario = 0.00;
            let precio_unitario = 0.00;
            let dinero = 0.00;
            let precio_nuevo = 0.00;
            let valor_venta = 0.00;
            let cantidad = convertFloat(this.cantidad);

            precio_unitario = precio_inicial;
            valor_unitario = precio_unitario / (1 + igv_calculado);
            dinero = precio_unitario * (pdescuento / 100);
            precio_nuevo = precio_unitario - dinero;
            valor_venta = precio_nuevo * cantidad;

            this.tablaDetalles.forEach(item=>{
                if(Number(item.producto_id) == Number(this.formEdit.id)){
                    item.producto_id= this.formEdit.id;
                    item.unidad= this.formEdit.unidadMedida;
                    item.producto= this.formEdit.producto;
                    item.precio_unitario= precio_unitario;
                    item.valor_unitario= valor_unitario;
                    item.valor_venta= RedondearDecimales(valor_venta);
                    item.cantidad= cantidad;
                    item.precio_inicial= precio_inicial;
                    item.dinero= dinero;
                    item.descuento= pdescuento;
                    item.precio_nuevo= precio_nuevo;
                    item.precio_minimo= this.formEdit.precioMinimo;
                }
            });
            $("#modal_editar_detalle").modal("hide");
            this.$emit("update:detalles",this.tablaDetalles);
        },
        cerrarModal(caso) {
            switch (caso) {
                case "EDITAR":
                    $("#modal_editar_detalle").modal("hide");
                    break;
                case "CODIGO":
                    $("#modal-codigo-precio-editar").modal("hide");
                    $("body").addClass("modal-open");
                    break;
            }
        }
    }
}
</script>