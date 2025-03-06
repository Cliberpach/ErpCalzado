<template>
    <div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>
                            <a style="color: #FDEBD0;" href="javascript:void(0);"><i class="fa fa-square fa-2x"></i></a> DOC CON NOTA DE CRÉDITO 
                            <a style="color: #EBDEF0;" href="javascript:void(0);"><i class="fa fa-square fa-2x"></i></a>DOC CONVERTIDO
                            <a style="color:#E3E9FE" href="javascript:void(0);"><i class="fa fa-square fa-2x"></i></a>DOC CON CAMBIO DE TALLA</h5>
                        </div>
                        <div class="ibox-content tables_wrapper">
                            <div class="row">
                                
                                <div class="col-md-3 form-group">
                                    <label for="">Desde:</label>
                                    <input type="date" id="fechaInicial" class="form-control form-control-sm"
                                        v-model="fechaInicial" />
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="text-white">-</label>
                                    <input type="text" placeholder="buscar serie-correlativo" id="numero_doc"
                                        class="form-control form-control-sm" v-model="numero_doc" />
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="text-white">-</label>
                                    <input type="text" placeholder="buscar por cliente" id="cliente"
                                        class="form-control form-control-sm" v-model="cliente" />
                                </div>
                                <div class="col-md-1 d-none">
                                    <label for="" class="text-white">-</label>
                                    <button type="button" class="btn btn-primary btn-block" id="reload">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-index table-striped table-bordered table-hover"
                                            style="text-transform: uppercase" ref="table-documentos">
                                            <thead class="">
                                                <tr>
                                                    <th class="text-center letrapequeña bg-white">COT</th>
                                                    <th class="text-center letrapequeña bg-white">CV</th>
                                                    <th class="text-center letrapequeña bg-white">PE</th>
                                                    <th class="text-center letrapequeña bg-white">DOC</th>
                                                    <th class="text-center letrapequeña bg-white">FECHA</th>
                                                    <th class="text-center letrapequeña bg-white">REGISTRADOR</th>
                                                    <th class="text-center letrapequeña bg-white">SEDE</th>
                                                    <th class="text-center letrapequeña bg-white">ALMACÉN</th>
                                                    <th class="text-center letrapequeña bg-white">CLIENTE</th>
                                                    <th class="text-center letrapequeña bg-white">MONTO</th>
                                                    <th class="text-center letrapequeña bg-white">CONDICION</th>
                                                    <th class="text-center letrapequeña bg-white">ESTADO</th>
                                                    <th class="text-center letrapequeña bg-white">SUNAT</th>
                                                    <th class="text-center letrapequeña bg-white">DESCARGAS</th>
                                                    <th class="text-center letrapequeña bg-white">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-if="documentos.length > 0">
                                                    <tr v-for="(item, index) in documentos" :key="index" :style="PintarRowTable(item)">
                                                        <!-- <td class="letrapequeña text-center">
                                                            <input v-if="item.cotizacion_venta" type="checkbox" disabled
                                                                checked />
                                                            <input v-else type="checkbox" disabled />
                                                        </td> -->
                                                        
                                                        <td class="letrapequeña text-center">
                                                            <span v-if="item.cotizacion_id" v-html="`<strong>CO-${item.cotizacion_id}</strong>`"></span>
                                                        </td>

                                                        <td class="letrapequeña text-center">
                                                            {{ item.convert_de_serie }}
                                                        </td>
                                                        <td class="letrapequeña text-center">

                                                            <p v-if="item.pedido_id" style="margin:0;padding:0;">{{ `PE-${item.pedido_id}` }}</p>
                                                            <p v-if="item.pedido_id" style="margin:0;padding:0;">{{ item.tipo_doc_venta_pedido }}</p>
                                                            <p v-if="!item.pedido_id" style="margin:0;padding:0;">{{ '-' }}</p>

                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.numero_doc }}
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.fecha_documento }}
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.registrador_nombre }}
                                                        </td>

                                                        <td class="letrapequeña">
                                                            <div class="text-truncate">
                                                                {{ item.sede_nombre }}
                                                            </div>
                                                        </td>

                                                        <td class="letrapequeña">
                                                            <div class="text-truncate">
                                                                {{ item.almacen_nombre }}
                                                            </div>
                                                        </td>

                                                        <td class="letrapequeña">
                                                            <div style="width:300px" class="text-truncate">
                                                                {{ item.cliente }}
                                                            </div>
                                                        </td>
                                                        <td class="letrapequeña text-center">{{ item.total_pagar }}</td>
                                                        <!-- <td class="letrapequeña text-center">
                                                            {{ item.dias > 4 ? 0 : 4 - item.dias }}
                                                        </td> -->
                                                        <td class="letrapequeña text-center">
                                                            {{ item.condicion }}
                                                        </td>
                                                        <td class="letrapequeña text-center" v-html="estadoPago(item)">
                                                        </td>
                                                        <td class="letrapequeña text-center" v-html="estadoSunat(item)">
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            <div class="btn-group" role="group"
                                                                aria-label="Second group">
                                                                <button type="button" @click.prevent="ModalPdf(item)"
                                                                    class="btn btn-dark" title="PDF">
                                                                    <strong>PDF</strong>
                                                                </button>
                                                                <button type="button"
                                                                    @click.prevent="xmlElectronico(item.id)"
                                                                    class="btn btn-info" title="XML">
                                                                    <strong>XML</strong>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td class="letrapequeña text-center">


                                                                <b-dropdown  text="Primary" variant="success" class="m-2">
                                                                    <template #button-content>
                                                                        <i class="fas fa-th"></i>
                                                                    </template>

                                                                    <template v-if="item.sunat == '0' &&
                                                                        item.tipo_venta != 129 &&
                                                                        // dias(item) > 0 &&
                                                                        item.contingencia == '0'">

                                                                        <b-dropdown-item @click="enviarSunat(item.id, item.contingencia)">
                                                                            <i class="fa fa-send"  style="color: #0065b3;"></i> Sunat
                                                                        </b-dropdown-item>

                                                                    </template>

                                                                    <template v-if="
                                                                        item.sunat_contingencia == '0' &&
                                                                        item.tipo_venta != 129 &&
                                                                        item.contingencia == '1'">

                                                                        <b-dropdown-item @click="enviarSunat(item.id, item.contingencia)">
                                                                            <i class="fa fa-send"  style="color: #0065b3;"></i> Sunat
                                                                        </b-dropdown-item>

                                                                    </template>
                                                                    
                                                                    <template v-if="(item.sunat == '1' ||
                                                                        item.notas > 0 ||item.sunat_contingencia == '1') &&
                                                                        item.tipo_venta != 129">
                                                                        <b-dropdown-item :href="routes(item.id, 'NOTAS')">
                                                                            <i class="fa fa-file-o" style="color: #77600e;"></i> Notas
                                                                        </b-dropdown-item>
                                                                       
                                                                    </template>

                                                                    <template v-if="item.sunat == '1' && item.notas == 0">
                                                                        <b-dropdown-item title="Guía Remisión" @click.prevent="guia(item.id)">
                                                                            <i class="fa fa-file"></i> Guía
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="(item.tipo_venta == 129 && item.condicion == 'CONTADO' 
                                                                        && item.estado_pago == 'PAGADA') ||
                                                                        (item.tipo_venta == 129 &&
                                                                        (item.condicion == 'CREDITO' ||
                                                                        item.condicion == 'CRÉDITO'))">

                                                                        <b-dropdown-item title="Nota de devolución" :href="routes(item.id, 'DEVO')">
                                                                            <i class="fa fa-file-o"></i> Devoluciones
                                                                        </b-dropdown-item>

                                                                    </template>

                                                                    
                                                                    <template v-if="
                                                                        item.estado_pago == 'PENDIENTE' 
                                                                        && item.sunat == '0'
                                                                        && !item.convert_en_id
                                                                        && !item.convert_de_id
                                                                        && !item.pedido_id
                                                                        && item.notas == 0">
                                                                        <b-dropdown-item title="Editar" :href="routes(item.id, 'EDITAR')">
                                                                            <i class="fas fa-edit" style="color:chocolate;"></i> Editar
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="item.notas == 0">
                                                                        <b-dropdown-item title="Cambio de Talla" @click="cambiarTallas(item.id)">
                                                                            <i class="fas fa-exchange-alt" style="color: #3307ab;"></i> Cambio de Talla
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="item.tipo_venta == 129 && !item.convert_en_id && item.notas == 0">
                                                                        <b-dropdown-item title="Convertir" :href="routes(item.id, 'CONVERTIR')">
                                                                            <i class="fas fa-file-invoice" style="color: blue;"></i> Convertir
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="item.condicion == 'CONTADO' &&
                                                                        item.estado_pago == 'PENDIENTE' &&
                                                                        item.tipo_venta == '129'">
                                                                        <b-dropdown-item title="Pagar"  @click="Pagar(item)">
                                                                            <i class="fa fa-money" style="color: #007502;"></i> Pagar
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="item.condicion == 'CONTADO' &&
                                                                        item.estado_pago == 'PENDIENTE' &&
                                                                        item.tipo_venta != 129 &&
                                                                        (item.convert_de_id == '' || item.convert_de_id == null)">
                                                                        <b-dropdown-item title="Pagar"  @click="Pagar(item)">
                                                                            <i class="fa fa-money" style="color: #007502;"></i> Pagar
                                                                        </b-dropdown-item>
                                                                    </template>


                                                                    <template v-if="item.code == '1033' && item.regularize == '1' &&
                                                                        item.sunat != '2' && item.contingencia == '0'">

                                                                        <b-dropdown-item title="CDR"  @click.prevent="cdr(item.id)">
                                                                            <i class="fa fa-money"></i> CDR
                                                                        </b-dropdown-item>
                                                                    </template>

                                                                    <template v-if="dias(item) <= 0 && item.estado == 'ACTIVO' &&
                                                                        item.tipo_venta != '129' && item.sunat == '0'">
                                                                        <b-dropdown-item title="Crear nuevo doc venta con el mismo detalle"  @click.prevent="regularizarVenta(item.id)">
                                                                            <i class="fa fa-exchange"></i> ANULAR
                                                                        </b-dropdown-item>
                                                                    </template>


                                                                    <template v-if="item.estado_despacho!=='DESPACHADO' && item.estado_despacho
                                                                    && item.notas == 0">
                                                                        <b-dropdown-item title="Crear nuevo doc venta con el mismo detalle"  @click.prevent="setDataEnvio(item.id)">
                                                                            <i class="fas fa-truck"></i> DESPACHO
                                                                        </b-dropdown-item>
                                                                    </template>
                                                                
                                                                </b-dropdown>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template v-if="!loading && documentos.length == 0">
                                                    <tr>
                                                        <td colspan="15" class="text-center">
                                                            <strong class="font-bold">No hay datos</strong>
                                                        </td>
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
                                        Cargando datos
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <nav>
                                        <ul class="pagination">
                                            <template v-if="pagination.currentPage > 1">
                                                <li class="page-item">
                                                    <a class="page-link" href="javascript:void(0)" rel="prev"
                                                        @click.prevent="changePage(pagination.currentPage - 1)"
                                                        aria-label="« Previous">‹</a>
                                                </li>
                                            </template>
                                            <template v-else>
                                                <li class="page-item disabled" aria-disabled="true"
                                                    aria-label="« Previous">
                                                    <span class="page-link" aria-hidden="true">‹</span>
                                                </li>
                                            </template>

                                            <template v-for="(item, index) in pagesNumber">
                                                <template v-if="item == isActive">
                                                    <li class="page-item active" aria-current="page" :key="index">
                                                        <span class="page-link">
                                                            {{ item }}
                                                        </span>
                                                    </li>
                                                </template>
                                                <template v-else>
                                                    <li class="page-item" :key="index">
                                                        <a class="page-link" href="javascript:void(0)"
                                                            @click.prevent="changePage(item)">
                                                            {{ item }}
                                                        </a>
                                                    </li>
                                                </template>
                                            </template>

                                            <template v-if="pagination.currentPage < pagination.lastPage">
                                                <li class="page-item">
                                                    <a class="page-link" href="javascript:void(0)"
                                                        @click.prevent="changePage(pagination.currentPage + 1)"
                                                        rel="next" aria-label="Next »">›</a>
                                                </li>
                                            </template>
                                            <template v-else>
                                                <li class="page-item disabled" aria-disabled="true" aria-label="Next »">
                                                    <span class="page-link" aria-hidden="true">›</span>
                                                </li>
                                            </template>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalVentasVue :ventasPendientes="ventasPendientes" :imgDefault="imginicial" :modoPagos="modopagos" :cliente_id="cliente_id"/>
        <ModalPdfDownloadVue :pdfData.sync="pdfData" />
        <ModalEnvioVue :cliente="cliente" @updateDataEnvio="updateDataEnvio" ref="modalEnvioRef"/>
    </div>
</template>
<script>
import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

import ModalPdfDownloadVue from '../../../components/ventas/ModalPdfDownload.vue';
import ModalVentasVue from '../../../components/ventas/ModalVentas.vue';
import ModalEnvioVue from '../../../components/ventas/ModalEnvio.vue';

export default {
    name: "VentaLista",
    props: ["imginicial"],
    components: {
        ModalVentasVue,
        ModalPdfDownloadVue,
        ModalEnvioVue
    },
    data() {
        return {
            documentos: [],
            pagination: {
                currentPage: 0,
                from: 0,
                lastPage: 0,
                perPage: 0,
                to: 0,
                total: 0,
            },
            offset: 11,
            params: {
                fechaInicial: this.$moment().format("YYYY-MM-DD"),
                cliente: "",
                numero_doc:"",
                tamanio: 10,
                page: 1,
            },
            fechaInicial: this.$moment().format("YYYY-MM-DD"),
            cliente: "",
            cliente_id:null,
            numero_doc:"",
            ventasPendientes: [],
            loading: false,
            pdfData: null,
            modopagos: []
        };
    },
    computed: {
        isActive: function () {
            return this.pagination.currentPage;
        },
        pagesNumber: function () {
            if (!this.pagination.from) {
                return [];
            }
            let from = this.pagination.currentPage - this.offset;
            if (from < 1) {
                from = 1;
            }

            let to = from + this.offset * 2;
            if (to >= this.pagination.lastPage) {
                to = this.pagination.lastPage;
            }

            let pageArray = [];
            while (from <= to) {
                pageArray.push(from);
                from++;
            }

            return pageArray;
        },
    },
    watch: {
        params: {
            handler() {
                this.$nextTick(this.Lista);
            },
            deep: true,
        },
        fechaInicial(value) {
            this.params.page = 1;
            this.params.fechaInicial = value;
        },
        cliente(value) {
            this.params.cliente = value;
            this.params.page = 1;
        },
        numero_doc(value) {
            this.params.numero_doc = value;
            console.log(value);
            console.log(this.params);
            this.params.page = 1;
        }
    },
    async created() {
        await this.Lista();
    },
    methods: {
        cambiarTallas(documento_id){
            const url = route('venta.cambiarTallas.create', documento_id);

            window.location.href = url;
        },
        async updateDataEnvio(data_envio){
            try {
                const res   =   await axios.post(route('ventas.despachos.updateDespacho'),data_envio);
                console.log(res);
            } catch (error) {
                
            }
           
        },
        async setDataEnvio(documento_id) {
            //========= TRAER LA DATA DE ENVÍO DEL DOCUMENTO ========
            try {
                const res   =   await axios.get(route('ventas.despachos.getDespacho',documento_id));
                //console.log(res);
                if(res.data.success){
                    //======= PASAR DATA DESPACHO AL MODAL ENVÍO =========
                    this.$refs.modalEnvioRef.metodoHijo(res.data.despacho,documento_id);

                    $("#modal_envio").modal("show");

                }else{
                    toastr.error(res.data.exception,res.data.message);
                }
            } catch (error) {
                
            }
            
        },
        async Lista() {
            try {
                this.loading = true;

                let { data } = await this.axios.get(route("ventas.getDocument"), {
                    params: this.params,
                });

                this.loading = false;

                const { documentos, pagination, modos_pago } = data;
                
                this.modopagos = modos_pago;
                this.documentos = documentos;
                this.pagination = pagination;
                
            } catch (ex) { }
        },
        estadoPago(data) {
            switch (data.estado_pago) {
                case "PENDIENTE":
                    return (
                        "<span class='badge badge-danger' d-block>" +
                        data.estado_pago +
                        "</span>"
                    );
                    break;
                case "PAGADA":
                    return (
                        "<span class='badge badge-primary verPago' style='cursor: pointer;' d-block>" +
                        data.estado_pago +
                        "</span>"
                    );
                    break;
                case "ADELANTO":
                    return (
                        "<span class='badge badge-success' d-block>" +
                        data.estado_pago +
                        "</span>"
                    );
                    break;
                case "DEVUELTO":
                    return (
                        "<span class='badge badge-warning' d-block>" +
                        data.estado_pago +
                        "</span>"
                    );
                    break;
                default:
                    return (
                        "<span class='badge badge-success' d-block>" +
                        data.estado_pago +
                        "</span>"
                    );
            }
        },
        estadoSunat(data) {
            switch (data.sunat) {
                case "1":
                    return "<span class='badge badge-primary' d-block>ACEPTADO</span>";
                    break;
                case "2":
                    return "<span class='badge badge-danger' d-block>NULA</span>";
                    break;
                default:
                    return "<span class='badge badge-success' d-block>REGISTRADO</span>";
            }
        },
        enviarSunat(id, contingencia) {
            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
            });

            Swal.fire({
                title: "DESEA ENVIAR EL DOCUMENTO DE VENTA A SUNAT?",
                text: "OPERACIÓN NO REVERSIBLE",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí!",
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: async () => {
                    try {
                        const res = await axios.get(route('ventas.documento.sunat', id));
                        return res.data;
                    } catch (error) {
                        const data  =   {success:false,message:"ERROR EN LA SOLICITUD",exception:error};   
                        return data;
                    }
                }
            }).then((result) => {
                
                if (result.value && result.value.success) {

                    //====== ACTUALIZAR DOCUMENTO EN FRONTEND ======
                    const documento_index =   this.documentos.findIndex((d)=>{
                        return  d.id == id;
                    });
                    if(documento_index !== -1){
                        this.documentos[documento_index].sunat  =   '1';
                    }

                    toastr.success(result.value.message,'DOCUMENTO ENVIADO A SUNAT',{timeOut:5000});
                } 

                if(result.value && !result.value.success){

                    //========== ERROR YA HA SIDO ENVIADO EL COMPROBANTE =======
                    if(result.value.code == 1033){
                        //====== ACTUALIZAR DOCUMENTO EN FRONTEND ======
                        const documento_index =   this.documentos.findIndex((d)=>{
                            return  d.id == id;
                        });
                        if(documento_index !== -1){
                            this.documentos[documento_index].sunat          =   '1';
                            this.documentos[documento_index].regularize     =   '0';

                        }
                    }

                    toastr.error(result.value.exception,result.value.message,{timeOut:0});
                }

                if(result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                    title: "Operación cancelada",
                    text: "No se realizaron acciones",
                    icon: "error"
                    });
                }

            });


        },
        dias(data) {
            var dias = data.dias > 4 ? 0 : 4 - data.dias;
            return dias;
        },
        async changePage(page) {
            try {
                this.pagination.currentPage = page;
                this.params.page = page;
                await this.Listar();
            } catch (ex) { }
        },
        Pagar(item) {
            try {
                let timerInterval;
                let clientes = [];
                let me = this;
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
                            url: route('ventas.getDocumentClient'),
                            data: {
                                '_token': $('meta[name=csrf-token]').val(),
                                'cliente_id': item.cliente_id,
                                'condicion_id': item.condicion_id
                            },
                            success: function (response) {
                                if (response.success) {
                                    const { ventas }    =   response;
                                    me.ventasPendientes =   ventas;
                                    me.cliente_id       =   item.cliente_id;
                                    $('#modal_ventas').modal('show');

                                    // timerInterval = 0;
                                    Swal.resumeTimer();
                                    //console.log(colaboradores);
                                } else {
                                    Swal.resumeTimer();
                                    // ventas = [];
                                    // loadTable(ventas);
                                }
                            }
                        });
                    },
                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                });
            } catch (ex) {
                alert(`Error en Pagar ${ex}`);
            }
        },
        guia(id) {
            Swal.fire({
            title: 'Opción Guia de Remision',
            text: "¿Seguro que desea crear una guia de remision?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                //Ruta Guia
                var url = route('ventas.documento.guiaCreate', {id});
                $(location).attr('href', url);

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )

            }
        })
        },
        routes(id, tipo) {
            switch (tipo) {
                case "NOTAS": {
                    return route("ventas.notas", { id });
                }
                case "DEVO": {
                    return route("ventas.notas_dev", { id });
                }
                case "EDITAR": {
                    return route("ventas.documento.edit", { id });
                }
                case "HOME": {
                    return route('home');
                }
                case "CREATE": {
                    return route('ventas.documento.create');
                }
                case "CONVERTIR": {
                    return route('ventas.documento.convertirCreate',{id});
                }
            }
        },
        xmlElectronico(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            });

            Swal.fire({
                title: "Opción XML",
                text: "¿Seguro que desea obtener el documento de venta en xml?",
                showCancelButton: true,
                icon: 'info',
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                // showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.value) {

                    var url = route('ventas.documento.xml', { id });

                    window.location.href = url;
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelado',
                        'La Solicitud se ha cancelado.',
                        'error'
                    )
                }
            })
        },
        ModalPdf(item) {
            this.pdfData = item;
            console.log(item);
        },
        PintarRowTable(aData) {
            if (aData.notas > 0) {
                return {'background-color':"#FDEBD0"};
            }

            if (aData.convert_en_id) {
                return {'background-color':"#EBDEF0"}
            }

            if(aData.cambio_talla == '1'){
                return {'background-color':"#E3E9FE"} 
            }
        },
        async regularizarVenta(documento_id){
            Swal.fire({
            title: "DESEA ANULAR EL DOC DE VENTA?",
            text: "SE GENERARÁ UN NUEVO DOC DE VENTA COMO REEMPLAZO!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "SÍ!"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    let alerta_procesando =   toastr.info('ANULANDO DOC DE VENTA', 'PROCESANDO', {
                            closeButton: false,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            timeOut: 0, 
                            tapToDismiss: false 
                    });

                    try {

                        const res   =   await axios.post(route('ventas.regularizarVenta'),{
                            documento_id
                        });
                        console.log(res);

                        const success   =   res.data.success;
                        if(success){
                            const message       =   res.data.message;

                            //======== ACTUALIZANDO LISTADO =====
                            this.Lista();
                            //========= RESPUESTA EXITOSA ======
                            toastr.success(message,'OPERACIÓN COMPLETADA',{
                                timeOut: 0, 
                            });
                        }else{
                            const type  =   res.data.type;

                            //========== MANEJANDO ERRORES DE VALIDACIÓN DEL REQUEST =====
                            if(type == "VALIDATION"){
                            
                                const messages  =   res.data.data.mensajes;
                                console.log(messages);
                                let message = ``;
                                for (const key in messages) {
                                    if (Object.hasOwnProperty.call(messages, key)) {
                                        const element = messages[key];
                                        message += `| ${element[0]} |`;
                                    }
                                }
                                toastr.error(message,'ERROR DE VALIDACIÓN',{
                                    timeOut: 0, 
                                });
                            }

                            //======= MANEJANDO ERRORES EN ACCIONES SOBRE LA BD =====
                            if(type == "DB"){
                                const message       =   res.data.message;
                                const exception  =   res.data.exception;

                                toastr.error(exception,message,{
                                    timeOut: 0, 
                                });
                            }

                        }
                    } catch (error) {
                        toastr.error('ERROR EN EL SERVIDOR','ERROR',{
                                timeOut: 0, 
                        });
                    }finally{
                        toastr.clear(alerta_procesando);
                    }

                }
            });
        }
    },
    mounted() {
        
    },
    };
</script>
<style>
    .tables_wrapper table.table-index tbody td{
        vertical-align:middle!important;
    }
    .dropdown-menu {
        max-height: 100px; 
        overflow-y: auto; 
    }
</style>