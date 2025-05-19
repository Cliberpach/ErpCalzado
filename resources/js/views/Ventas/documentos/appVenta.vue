<template>
    <div>
        <div class="row wrapper border-bottom white-bg page-heading align-items-end">
            <div class="col-12 col-md-10">
                <h2 style="text-transform:uppercase">
                    <b v-if="ruta == 'index'">Lista de Documentos de Venta</b>
                    <b v-if="ruta == 'create'">REGISTRAR NUEVO DOCUMENTO DE VENTA</b>
                </h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a :href="routes('HOME')">Panel de Controls</a>
                    </li>
                    <li class="breadcrumb-item" :class="ruta == 'index' ? 'active' : ''">
                        <template v-if="ruta == 'index'">
                            <strong>Documentos de Ventas</strong>
                        </template>
                        <template v-else>
                            <a href="javascript:void(0)" @click.prevent="ruta = 'index'">
                                Documentos de Ventas
                            </a>
                        </template>
                    </li>
                    <li class="breadcrumb-item" v-if="ruta == 'create' ? 'active' : ''"
                        :class="ruta == 'create' ? 'active' : ''">
                        <strong>Registrar</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2 col-md-2" v-if="ruta == 'index'">
                <button type="button" class="btn btn-block btn-w-m btn-primary m-t-md" @click.prevent="ruta = 'create'">
                    <i class="fa fa-plus-square"></i> Añadir nuevo
                </button>
            </div>
        </div>
        <template v-if="ruta == 'index'">
            <venta-lista :imginicial="imginicial" />
        </template>
        <template v-if="ruta == 'create'">
            <venta-create :ruta.sync="ruta"
            :v_sede="this.v_sede"
            :registrador="this.registrador"
            :idcotizacion="idcotizacion"
            :lst_almacenes="this.lst_almacenes"
            :lst_departamentos_base="this.lst_departamentos_base"
            :lst_provincias_base="this.lst_provincias_base"
            :lst_distritos_base="this.lst_distritos_base"
            />
        </template>
    </div>
</template>

<script>

export default {
    name: "AppVue",
    props: [
        "imginicial",
        "v_sede",
        "registrador",
        "lst_almacenes",
        "lst_departamentos_base",
        "lst_provincias_base",
        "lst_distritos_base"],
    components:{
    },
    data() {
        return {
            ruta: "index",
            idcotizacion: 0
        }
    },
    watch: {
        ruta(data) {

            if (data == "create") {
                let cotizacion = this.idcotizacion == 0 ? '' : '?cotizacion=' + this.idcotizacion;
                let url = route('ventas.documento.create') + cotizacion;
                history.pushState(null, "", url);
            }

            if (data == "index") {
                history.pushState(null, "", route('ventas.documento.index'));
                this.idcotizacion = 0;
            }
        }
    },
    created() {
        try {

            let url = location.href.split('/');
            let pathUrl = url[url.length - 1];
            let pathUrlGet = pathUrl.split('?');
            if (pathUrlGet.length == 1) {
                if (pathUrlGet.shift() == 'create') {
                    this.ruta = "create";
                }

            } else {
                let parametros = pathUrlGet[1];
                parametros = parametros.split("=");
                if (parametros[0] === "cotizacion") {
                    this.idcotizacion = Number(parametros[1]);
                    this.ruta = "create";
                } else {
                    throw "la variable " + parametros + " no es valido.";
                }
            }
        } catch (ex) {
            console.log(ex);
        }

    },
    methods: {
        routes(tipo) {
            switch (tipo) {
                case "HOME": {
                    return route('home');
                    break;
                }
                case "CREATE": {
                    return route('ventas.documento.create');
                    break;
                }
            }
        }
    },
    mounted() {

    }

}


</script>
