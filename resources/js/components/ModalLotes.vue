<template>
    <div class="modal inmodal" data-backdrop="static" data-keyboard="false" ref="modal_lote" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" @click.prevent="Cerrar">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <i class="fa fa-cogs modal-icon d-none"></i>
                    <h4 class="modal-title d-none"></h4>
                    <small class="font-bold d-none"></small>
                </div>
                <div class="modal-body">
                    <div class="form-group m-l">
                        <span><b>Instrucciones:</b> Doble click en el registro del producto a vender.</span>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <input type="text" v-model="search" class="form-control form-control-sm"
                                placeholder="buscar producto...">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12 tables_wrapper">
                                <div class="table-responsive m-t">
                                    <table class="table table-bordered" style="width:100%; text-transform:uppercase;"
                                        id="table_lotes">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PRODUCTO</th>
                                                <th class="text-center">UM</th>
                                                <th class="text-center">CATEGORIA</th>
                                                <th class="text-center">MARCA</th>
                                                <th class="text-center">CANTID.</th>
                                                <th class="text-center">COD. BARRA</th>
                                                <th class="text-center">P. NORMAL</th>
                                                <th class="text-center">P. DISTRI.</th>
                                                <th class="text-center" v-if="fullAccess">P.COMPRA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="Lotes.length > 0">
                                                <tr v-for="(item, index) in Lotes" :key="index"
                                                    @dblclick="SelectedProducto(item)" @click="PintarRow(index)"
                                                    :class="index == indexSelected ? 'bg-success' : ''">
                                                    <td>{{ item.nombre }}</td>
                                                    <td>{{ item.unidad_producto }}</td>
                                                    <td>{{ item.categoria }}</td>
                                                    <td>{{ item.marca }}</td>
                                                    <td>{{ item.cantidad_logica }}</td>
                                                    <td>{{ item.codigo_barra }}</td>
                                                    <td>{{ precioNormal(item) }}</td>
                                                    <td>{{ precioDistribuidor(item) }}</td>
                                                    <td v-if="fullAccess">{{ precioCompra(item) }}</td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <template v-if="fullAccess">
                                                        <td colspan="9" class="text-center">No hay productos</td>
                                                    </template>
                                                    <template v-else>
                                                        <td colspan="8" class="text-center">No hay productos</td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tables_processing card" v-if="loading">
                                    <div style="width:100%;display:flex;justify-content:center">
                                        <div>
                                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                                        </div>
                                    </div>
                                    Cargando lotes
                                </div>
                            </div>
                        </div>
                        <PaginationVue :pagination="pagination" @changePage="getResults" />
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-6 text-left">
                        <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                            class="leyenda-required">Seleccionar el lote del producto a vender.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-danger btn-sm" @click.prevent="Cerrar"><i
                                class="fa fa-times"></i> Cancelar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import PaginationVue from './Pagination.vue';

export default {
    name: "ModalLote",
    components: {
        PaginationVue
    },
    props: {
        dataLotes: {
            type: Array,
            default: () => []
        },
        fullAccess: {
            type: Boolean,
            required: true,
            default: false
        },
        searchInput: {
            type: String,
            required: false,
            default: ""
        },
        tipoCliente: {
            type: Number,
            default: () => 0
        },
        tipoComprobante: {
            type: Number,
            default: () => 0,
        },
        show: {
            type: Boolean,
            required: true,
            default: () => false
        }
    },
    data() {
        return {
            Lotes: [],
            search: "",
            indexSelected: null,
            paramsLotes: {
                tipo_cliente: '',
                tipocomprobante: '',
                search: "",
                page: 1
            },
            pagination: {
                current_page: 0,
                last_page: 0,
                per_page: 0,
                to: 0,
                total: 0,
                from: 0,
                limit: 5
            },
            PaginateLote: null,
            loading: false
        }
    },
    computed: {

    },
    watch: {
        search(value) {
            this.paramsLotes.page = 1;
            this.paramsLotes.search = value;
            this.$nextTick(this.Listar);
        },
        dataLotes(value) {
            this.Lotes = value;
        },
        Lotes() {
            this.indexSelected = null;
            this.Lotes.forEach(item => {
                item.precioNormal = 0;
                item.precioDistribuidor = 0;
                item.precioCompra = 0;
            });
        },
        searchInput(value) {
            this.search = value;
        },
        show(value) {
            if (value) {
                this.paramsLotes.page = 1;
                this.paramsLotes.search = "";
                let modal = this.$refs.modal_lote;
                $(modal).modal("show");
                this.$nextTick(this.Listar);
            } else {
                let modal = this.$refs.modal_lote;
                $(modal).modal("hide");
                this.paramsLotes.page = 1;
                this.paramsLotes.search = "";
                this.$nextTick(this.Listar);
            }
        },
        tipoComprobante(value) {
            this.paramsLotes.tipocomprobante = value;
        },
        tipoCliente(value) {
            this.paramsLotes.tipo_cliente = value;
        },
        paramsLotes: {
            handler(value) {
                if (value.tipocomprobante != "" && value.tipo_cliente != "") {
                    this.$nextTick(this.Listar);
                }
            },
            deep: true
        }
    },
    created() {

    },
    methods: {
        async Listar() {
            try {
                this.loading = true;
                const { data } = await this.axios.get(route("ventas.getLoteProductos"), {
                    params: this.paramsLotes
                });
                const { lotes } = data;
                this.PaginateLote = lotes;
                this.Lotes = lotes.data;
                for (const key in lotes) {

                    if (this.pagination.hasOwnProperty(key)) {
                        this.pagination[key] = lotes[key];
                    }
                }
                this.loading = false;
            } catch (ex) {
                console.log("error en ObtenerLotes ", ex);
                this.loading = false;
            }
        },
        PintarRow(index) {
            this.indexSelected = index;
        },
        SelectedProducto(item) {
            this.search = "";
            this.$emit("selectedProductos", item);
        },
        precioNormal(item) {

            if (item.precio_compra == null) {
                let cambio = convertFloat(item.dolar_ingreso);
                let precio = 0;
                var precio_ = item.precio_ingreso;
                let porcentaje = 0;
                let porcentaje_ = convertFloat(item.porcentaje_normal);
                let precio_nuevo = 0;


                if (item.moneda_ingreso == 'DOLARES') {
                    precio = precio_ * cambio;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                else {
                    precio = precio_;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                item.precioNormal = convertFloat(precio_nuevo).toFixed(2);
                return convertFloat(precio_nuevo).toFixed(2);
            } else {

                let cambio = convertFloat(item.dolar_compra);
                let precio = 0;
                var precio_ = item.precio_compra;
                let porcentaje = 0;
                let porcentaje_ = item.porcentaje_normal;
                let precio_nuevo = 0;
                let totalCostoFlote = Number(item.costo_flete) / Number(item.cantidad_comprada);
                let costo_flete = convertFloat(totalCostoFlote * (1 + porcentaje_ / 100)).toFixed(2);

                if (item.moneda_compra == 'DOLARES') {
                    if (item.igv_compra == 1) {
                        precio = precio_ * cambio;
                        precio_nuevo = (precio * (1 + (porcentaje_ / 100))) + Number(costo_flete)
                    }
                    else {
                        precio = (precio_ * cambio * 1.18)
                        precio_nuevo = (precio * (1 + (porcentaje_ / 100))) + Number(costo_flete)
                    }
                }
                else {
                    if (item.igv_compra == 1) {
                        precio = precio_;
                        precio_nuevo = (precio * (1 + (porcentaje_ / 100))) + Number(costo_flete);
                    }
                    else {

                        precio = (precio_ * 1.18);
                        precio_nuevo = (precio * (1 + (porcentaje_ / 100))) + Number(costo_flete);
                    }
                }
                item.precioNormal = convertFloat(precio_nuevo).toFixed(2);
                return convertFloat(precio_nuevo).toFixed(2);
            }
        },
        precioDistribuidor(item) {
            if (item.precio_compra == null) {
                let cambio = convertFloat(item.dolar_ingreso);
                let precio = 0;
                var precio_ = item.precio_ingreso;
                let porcentaje_ = item.porcentaje_distribuidor;
                let precio_nuevo = 0;
                let totalCostoFlote = Number(item.costo_flete) / Number(item.cantidad_comprada);
                let costo_flete = convertFloat(totalCostoFlote * (1 + porcentaje_ / 100)).toFixed(2);
                if (item.moneda_ingreso == 'DOLARES') {
                    precio = precio_ * cambio;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                else {
                    precio = precio_;
                    precio_nuevo = precio * (1 + (porcentaje_ / 100))
                }
                item.precioDistribuidor = convertFloat(precio_nuevo).toFixed(2);
                return convertFloat(precio_nuevo).toFixed(2);
            } else {
                let cambio = convertFloat(item.dolar_compra);
                let precio = 0;
                var precio_ = item.precio_compra;
                let porcentaje = 0;
                let porcentaje_ = item.porcentaje_distribuidor;
                let precio_nuevo = 0;
                let totalCostoFlote = Number(item.costo_flete) / Number(item.cantidad_comprada);
                let costo_flete = convertFloat(totalCostoFlote * (1 + porcentaje_ / 100)).toFixed(2);
                if (item.moneda_compra == 'DOLARES') {
                    if (item.igv_compra == 1) {
                        precio = precio_ * cambio;
                        precio_nuevo = precio * (1 + (porcentaje_ / 100)) + Number(costo_flete);
                    }
                    else {
                        precio = (precio_ * cambio * 1.18)
                        precio_nuevo = precio * (1 + (porcentaje_ / 100)) + Number(costo_flete);
                    }
                }
                else {
                    if (item.igv_compra == 1) {
                        precio = precio_;
                        precio_nuevo = precio * (1 + (porcentaje_ / 100)) + Number(costo_flete);
                    }
                    else {
                        precio = (precio_ * 1.18)
                        precio_nuevo = precio * (1 + (porcentaje_ / 100)) + Number(costo_flete);
                    }
                }
                item.precioDistribuidor = convertFloat(precio_nuevo).toFixed(2);
                return convertFloat(precio_nuevo).toFixed(2);
            }
        },
        precioCompra(item) {
            if (item.precio_mas_igv_soles == null) {
                item.precioCompra = convertFloat(item.precio_ingreso_soles).toFixed(2);
                return item.precio_ingreso_soles;
            } else {
                item.precioCompra = convertFloat(item.precio_mas_igv_soles).toFixed(2);
                return convertFloat(item.precio_mas_igv_soles).toFixed(2);
            }
        },
        Cerrar() {
            this.Lote = [];
            this.search = "";
            this.$emit("update:show", false);
        },
        getResults(page) {
            this.paramsLotes.page = page;
            this.$nextTick(this.Listar);
        }
    },
    mounted() {


    }
}
</script>
