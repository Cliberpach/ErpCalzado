<template>
    <transition name="fade" @before-enter="beforeEnter" @enter="enter" @before-leave="beforeLeave" @leave="leave">
        <div v-if="visible" class="modal-overlay">
            <div class="modal-container d-flex flex-column">
                <div class="modal-header">
                    <h3>{{ title }}</h3>
                    <button @click="closeModal" class="close-button">×</button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th v-for="talla in tallas" :key="talla.id">{{ talla.descripcion }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td v-for="cantidad_talla in cantidadPorTalla">
                                        <input min="0" type="text"
                                        style="width: 60px;"
                                        class="form-control"
                                        v-model="cantidad_talla.cantidad"
                                        @input="cantidad_talla.cantidad = (/^[0-9]+$/.test(cantidad_talla.cantidad)) ? parseInt(cantidad_talla.cantidad) : 0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" @click="confirm">Guardar</button>
                    <button class="btn btn-secondary" @click="closeModal">Cancelar</button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'Modal',
    props: {
        title: {
            type: String,
            default: 'Modal Title',
        },
        visible: {
            type: Boolean,
            default: false,
        },
        tallas:{
            type: Array,
            default:[]
        },
        tallasProducto: {
            type: Array,
            default: () => [],
        },
        productoEditar:{
            type: Object,
            default: () => ({
                producto_id: null,
                color_id: null
            }),
        },
        detalleVenta:{
            type: Array,
            default: () => [],
        }
    },
    data(){
        return{
            cantidadPorTalla: []
        }
    },
    emits: ['close'],
    methods: {
        getCantidadForTalla(tallaId) {
            const tallaEncontrada = this.tallasProducto.find(talla => talla.talla_id == tallaId);
            return tallaEncontrada ? tallaEncontrada.cantidad : '';
        },
        closeModal() {
            this.setCantTallaOriginal();
            this.$emit('close');
        },
        async confirm() {

            //====== EDITAR ITEM =======
            console.log('PRODUCTO EDITAR',this.productoEditar);
            console.log('CANTIDAD POR TALLA',this.cantidadPorTalla);
            console.log('PRODUCTOS TABLA',this.detalleVenta);

            this.$parent.mostrarAnimacionVenta();

            const almacen_id    =   this.$parent.almacenSeleccionado;

            //======= DEBE SELECCIONARSE UN ALMACÉN =======
            if(!almacen_id){
                toastr.clear();
                toastr.error('DEBES SELECCIONAR UN ALMACÉN!!!');
                //======= FOCUS AL VSELECT ALMACÉN ========
                this.$nextTick(() => {
                    this.$parent.$refs.selectAlmacen.$el.querySelector("input").focus();
                });
                this.$parent.ocultarAnimacionVenta();
                return;
            }

            //======= CANTIDADES NUEVAS =====
            const cantidadPorTallaSinCeros = this.cantidadPorTalla.filter(ct => ct.cantidad != '0' && ct.cantidad != '');

            const indiceProductoColor   =   this.detalleVenta.findIndex((d)=>{
                                                return d.producto_id == this.productoEditar.producto_id && d.color_id == this.productoEditar.color_id;
                                            })

            let lstCantidadesEdit   =   [];

            this.cantidadPorTalla.forEach((dt)=>{

                const indiceCantAnt  =   this.detalleVenta[indiceProductoColor].tallas.findIndex((ct)=>{
                    return ct.talla_id == dt.talla_id;
                })

                let producto    =   {};

                if(indiceCantAnt === -1){
                    producto  =   {producto_id:this.productoEditar.producto_id,color_id:this.productoEditar.color_id,
                                        talla_id:dt.talla_id,cantidad_actual:dt.cantidad,cantidad_anterior:0
                    }
                }else{
                    producto  =   {producto_id:this.productoEditar.producto_id,color_id:this.productoEditar.color_id,
                                        talla_id:dt.talla_id,cantidad_actual:dt.cantidad,cantidad_anterior:this.detalleVenta[indiceProductoColor].tallas[indiceCantAnt].cantidad
                    }
                }

                lstCantidadesEdit.push(producto);

            })

            lstCantidadesEdit = lstCantidadesEdit.filter(ct =>
                !(ct.cantidad_actual == '0' && ct.cantidad_anterior == '0') &&
                !(ct.cantidad_actual == '' && ct.cantidad_anterior == '')
            );

            console.log('CANTIDADES EDIT',lstCantidadesEdit);

            //======== ACTUALIZANDO STOCK EDIT =======
            const res   =   await axios.post(route('ventas.documento.actualizarStockEdit'),
                            {almacenId:almacen_id,lstProductos:JSON.stringify(lstCantidadesEdit)});

            if(res.data.success){
                toastr.success(res.data.message,'OPERACIÓN COMPLETADA');
            }else{
                toastr.error(res.data.message,'ERROR EN EL SERVIDOR');
                this.$parent.ocultarAnimacionVenta();
                return;
            }

            if(cantidadPorTallaSinCeros.length === 0){

                if(indiceProductoColor !== -1){
                    this.detalleVenta.splice(indiceProductoColor,1);
                }

             }else{
                this.detalleVenta[indiceProductoColor].tallas   =   cantidadPorTallaSinCeros;
            }

            this.$parent.ocultarAnimacionVenta();
            this.closeModal();
            //this.$emit('confirm');
        },
        beforeEnter(el) {
            el.style.opacity = 0;
            el.style.transform = 'scale(0.8)';
        },
        enter(el, done) {
            el.offsetHeight;
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            el.style.opacity = 1;
            el.style.transform = 'scale(1)';
            done();

            this.setCantTallaOriginal();

            console.log('talla_Agregar',this.cantidadPorTalla);

        },
        setCantTallaOriginal(){
            this.cantidadPorTalla   =   [];

            //========== PREPARANDO CANT POR TALLA =======
            this.tallas.forEach((t)=>{

                const talla_find    =   this.tallasProducto.find((p)=>{
                    return p.talla_id == t.id;
                })

                const talla_agregar =   {
                                            talla_id:t.id,
                                            talla_nombre:t.descripcion,
                                            cantidad:talla_find?talla_find.cantidad:0
                                        };

                this.cantidadPorTalla.push(talla_agregar);

            })
        },
        beforeLeave(el) {
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease'; // Set transition for leave
        },
        leave(el, done) {
            el.style.opacity = 0;
            el.style.transform = 'scale(0.8)';
            done();
        },

    },
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    transition: opacity 0.3s ease;
}

.modal-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 80vh;
    height: 70vh;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    opacity: 1;
    transform: scale(1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.close-button {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}
.modal-body {
    margin-bottom: 15px;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-primary {
    background: #007bff;
    color: #fff;
}
.btn-secondary {
    background: #6c757d;
    color: #fff;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
    transform: scale(0.8);
}
</style>
