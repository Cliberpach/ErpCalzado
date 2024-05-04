<template>
    <div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><a style="color: #FDEBD0;" href="#"><i class="fa fa-square fa-2x"></i></a> Documento con
                                NOTAS DE CREDITO <a style="color: #EBDEF0;" href="#"><i
                                        class="fa fa-square fa-2x"></i></a>
                                Documento de CONTINGENCIA</h5>
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
                                                    <th class="text-center letrapequeña bg-white"># DOC</th>
                                                    <th class="text-center letrapequeña bg-white">FECHA DOC.</th>
                                                    <th class="text-center letrapequeña bg-white">CLIENTE</th>
                                                    <th class="text-center letrapequeña bg-white">MONTO</th>
                                                    <th class="text-center letrapequeña bg-white">TIEMPO</th>
                                                    <th class="text-center letrapequeña bg-white">MODO</th>
                                                    <th class="text-center letrapequeña bg-white">ESTADO</th>
                                                    <th class="text-center letrapequeña bg-white">SUNAT</th>
                                                    <th class="text-center letrapequeña bg-white">DESCARGAS</th>
                                                    <th class="text-center letrapequeña bg-white">ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-if="documentos.length > 0">
                                                    <tr v-for="(item, index) in documentos" :key="index" :style="PintarRowTable(item)">
                                                        <td class="letrapequeña text-center">
                                                            <input v-if="item.cotizacion_venta" type="checkbox" disabled
                                                                checked />
                                                            <input v-else type="checkbox" disabled />
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.doc_convertido }}
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.numero_doc }}
                                                        </td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.fecha_documento }}
                                                        </td>

                                                        <td class="letrapequeña">
                                                            <div style="width:300px" class="text-truncate">
                                                                {{ item.cliente }}
                                                            </div>
                                                        </td>
                                                        <td class="letrapequeña text-center">{{ item.total_pagar }}</td>
                                                        <td class="letrapequeña text-center">
                                                            {{ item.dias > 4 ? 0 : 4 - item.dias }}
                                                        </td>
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
                                                            <div class="btn-group" role="group"
                                                                aria-label="Second group">
                                                                <template v-if="
                                                                  item.sunat == '0' &&
                                                                  item.tipo_venta_id != 129 &&
                                                                  dias(item) > 0 &&
                                                                  item.contingencia == '0'
                                                                ">
                                                                    <button type="button" class="btn btn-sm btn-success"
                                                                        @click="enviarSunat(item.id, item.contingencia)"
                                                                        title="Enviar Sunat">
                                                                        <i class="fa fa-send"></i>
                                                                        Sunat
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  item.sunat_contingencia == '0' &&
                                                                  item.tipo_venta_id != 129 &&
                                                                  item.contingencia == '1'
                                                                ">
                                                                    <button type="button" class="btn btn-sm btn-success"
                                                                        @click="enviarSunat(item.id, item.contingencia)"
                                                                        title="Enviar Sunat">
                                                                        <i class="fa fa-send"></i>
                                                                        Sunat
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  (item.sunat == '1' ||
                                                                    item.notas > 0 ||
                                                                    item.sunat_contingencia == '1') &&
                                                                  item.tipo_venta_id != 129
                                                                ">
                                                                    <a class="btn btn-sm btn-warning"
                                                                        :href="routes(item.id, 'NOTAS')" title="Notas">
                                                                        <i class="fa fa-file-o"></i> Notas
                                                                    </a>
                                                                </template>

                                                                <template
                                                                    v-if="item.sunat == '1' || item.sunat_contingencia == '1'">
                                                                    <button type="button" class="btn btn-sm btn-info"
                                                                        @click.prevent="guia(item.id)"
                                                                        title="Guia Remisión">
                                                                        <i class="fa fa-file"></i> Guia
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  (item.tipo_venta_id == 129 &&
                                                                    item.condicion == 'CONTADO' &&
                                                                    item.estado_pago == 'PAGADA') ||
                                                                  (item.tipo_venta_id == 129 &&
                                                                    (item.condicion == 'CREDITO' ||
                                                                      item.condicion == 'CRÉDITO'))
                                                                ">
                                                                    <a class="btn btn-sm btn-warning"
                                                                        :href="routes(item.id, 'DEVO')"
                                                                        title="Devoluciones"><i
                                                                            class="fa fa-file-o"></i>
                                                                        Devoluciones</a>
                                                                </template>

                                                                <template v-if="
                                                                  item.tipo_venta_id == 129 &&
                                                                  item.estado_pago == 'PENDIENTE'
                                                                ">
                                                                    <a class="btn btn-sm btn-warning"
                                                                        :href="routes(item.id, 'EDITAR')"
                                                                        title="Editar"><i class="fa fa-pencil"></i>
                                                                        Editar</a>
                                                                </template>

                                                                <template v-if="
                                                                  (item.sunat == '2' && item.tipo_venta_id == 129) ||
                                                                  (item.tipo_venta_id == 129 &&
                                                                    item.estado_pago == 'PENDIENTE' &&
                                                                    item.contingencia == '0')
                                                                ">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger d-none"
                                                                        @click.prevent="eliminar(data.id)"
                                                                        title="Eliminar">
                                                                        <i class="fa fa-trash"></i> Eliminar
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  item.condicion == 'CONTADO' &&
                                                                  item.estado_pago == 'PENDIENTE' &&
                                                                  item.tipo_venta_id == '129'
                                                                ">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary pagar"
                                                                        @click="Pagar(item)" title="Pagar">
                                                                        <i class="fa fa-money"></i> Pagar
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  item.condicion == 'CONTADO' &&
                                                                  item.estado_pago == 'PENDIENTE' &&
                                                                  item.tipo_venta_id != 129 &&
                                                                  (item.convertir == '' || item.convertir == null)
                                                                ">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary pagar"
                                                                        @click="Pagar(item)" title="Pagar">
                                                                        <i class="fa fa-money"></i> Pagar
                                                                    </button>
                                                                </template>

                                                                <template v-if="
                                                                  item.code == '1033' &&
                                                                  item.regularize == '1' &&
                                                                  item.sunat != '2' &&
                                                                  item.contingencia == '0'
                                                                ">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary-cdr"
                                                                        @click.prevent="cdr(item.id)" title="CDR">
                                                                        CDR
                                                                    </button>
                                                                </template>

                                                                <!-- <template v-if="
                                                                  dias(item) <= 0 &&
                                                                  item.contingencia == '0' &&
                                                                  item.tipo_venta_id != '129' &&
                                                                  item.sunat == '0'
                                                                ">
                                                                    <button type="button" class="btn btn-sm btn-warning"
                                                                        @click.prevent="contingencia(item.id)"
                                                                        title="Convertir a comprobante de contingencia">
                                                                        <i class="fa fa-exchange"></i> {{item.estado}}
                                                                    </button> 
                                                                
                                                                </template> -->

                                                                <template v-if="
                                                                  dias(item) <= 0 &&
                                                                  item.estado == 'ACTIVO' &&
                                                                  item.tipo_venta_id != '129' &&
                                                                  item.sunat == '0'
                                                                ">
                                                                    <button type="button" class="btn btn-sm btn-warning"
                                                                        @click.prevent="regularizarVenta(item.id)"
                                                                        title="Crear nuevo doc venta con el mismo detalle">
                                                                        <i class="fa fa-exchange"></i> ANULAR
                                                                    </button> 
                                                                
                                                                </template>

                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <template v-if="!loading && documentos.length == 0">
                                                    <tr>
                                                        <td colspan="12" class="text-center">
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
        <ModalVentasVue :ventasPendientes="ventasPendientes" :imgDefault="imginicial" :modoPagos="modopagos" />
        <ModalPdfDownloadVue :pdfData.sync="pdfData" />
    </div>
</template>
<script>
import ModalPdfDownloadVue from '../../../components/ventas/ModalPdfDownload.vue';
import ModalVentasVue from '../../../components/ventas/ModalVentas.vue';
export default {
    name: "VentaLista",
    props: ["imginicial"],
    components: {
        ModalVentasVue,
        ModalPdfDownloadVue
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
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                },
                buttonsStyling: false
            })

        Swal.fire({
            title: "Opción Enviar a Sunat",
            text: "¿Seguro que desea enviar documento de venta a Sunat?",
            showCancelButton: true,
            icon: 'info',
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
            allowOutsideClick: false,
            // showLoaderOnConfirm: true,
        }).then(async(result) => {
            if (result.value) {

                var url = '';

                if (contingencia == '1') {
                    url = route('ventas.documento.sunat.contingencia',{id});
                } else {
                    //===== ENVIAR CUANDO CONTINGENCIA = 0 ===========
                    url = route('ventas.documento.sunat',{id});
                }

                window.location.href = url


                Swal.fire({
                    title: '¡Cargando!',
                    type: 'info',
                    text: 'Enviando documento de venta a Sunat',
                    showConfirmButton: false,
                    onBeforeOpen: () => {
                        Swal.showLoading()
                    }
                })

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
                                    const { ventas } = response;
                                    me.ventasPendientes = ventas;
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
                var url = route('ventas.guiasremision.create', {id});
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

            if (aData.contingencia == '1') {
                return {'background-color':"#EBDEF0"}
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
</style>