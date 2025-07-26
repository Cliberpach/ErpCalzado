<template>
    <div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>
                                <a style="color: #FDEBD0;" href="javascript:void(0);"><i
                                        class="fa fa-square fa-2x"></i></a> DOC CON NOTA DE CRÉDITO
                                <a style="color: #EBDEF0;" href="javascript:void(0);"><i
                                        class="fa fa-square fa-2x"></i></a>DOC CONVERTIDO
                                <a style="color:#E3E9FE" href="javascript:void(0);"><i
                                        class="fa fa-square fa-2x"></i></a>DOC CON CAMBIO DE TALLA
                                <a style="color:#caffcc" href="javascript:void(0);"><i
                                        class="fa fa-square fa-2x"></i></a>DOC CON GUIA
                            </h5>
                        </div>
                        <div class="ibox-content tables_wrapper">
                            <div class="row">

                                <div class="col-md-3 form-group">
                                    <label for="">Fecha Inicio:</label>
                                    <input type="date" id="fechaInicial" class="form-control form-control-sm"
                                        v-model="fechaInicial" />
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="">Fecha Fin:</label>
                                    <input type="date" id="fechaFinal" class="form-control form-control-sm"
                                        v-model="fechaFinal" />
                                </div>

                                <div class="col-md-3 form-group d-flex align-items-end">
                                    <button @click="filtrarDtVentas" class="btn btn-primary btn-sm w-100">
                                        Filtrar
                                    </button>
                                </div>


                                <div class="col-md-1 d-none">
                                    <label for="" class="text-white">-</label>
                                    <button type="button" class="btn btn-primary btn-block" id="reload">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="dt-ventas"
                                            class="table table-index table-striped table-bordered table-hover nowrap"
                                            style="text-transform: uppercase" ref="table-documentos">
                                            <thead class="">
                                                <tr>
                                                    <th class="text-center letrapequeña bg-white">#</th>
                                                    <th class="text-center letrapequeña bg-white">COT</th>
                                                    <th class="text-center letrapequeña bg-white">CV</th>
                                                    <th class="text-center letrapequeña bg-white">PE</th>
                                                    <th class="text-center letrapequeña bg-white">CDR</th>
                                                    <th data-priority="2" class="text-center letrapequeña bg-white">DOC
                                                    </th>
                                                    <th class="text-center letrapequeña bg-white">FECHA</th>
                                                    <th class="text-center letrapequeña bg-white">REGISTRADOR</th>
                                                    <th class="text-center letrapequeña bg-white">SEDE</th>
                                                    <th class="text-center letrapequeña bg-white">ALMACÉN</th>
                                                    <th data-priority="3" class="text-center letrapequeña bg-white">
                                                        CLIENTE</th>
                                                    <th class="text-center letrapequeña bg-white">MONTO</th>
                                                    <th class="text-center letrapequeña bg-white">CONDICION</th>
                                                    <th class="text-center letrapequeña bg-white">ESTADO</th>
                                                    <th class="text-center letrapequeña bg-white">SUNAT</th>
                                                    <th class="text-center letrapequeña bg-white">DESCARGAS</th>
                                                    <th data-priority="1" class="text-center letrapequeña bg-white">
                                                        ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalVentasVue :ventasPendientes="ventasPendientes" :imgDefault="imginicial" :modoPagos="this.lst_modos_pago"
            :cliente_id="cliente_id" />
        <ModalPdfDownloadVue :pdfData.sync="pdfData" />
        <ModalEnvioVue :cliente="cliente" @updateDataEnvio="updateDataEnvio" ref="modalEnvioRef" />
    </div>
</template>
<script>

import ModalPdfDownloadVue from '../../../components/ventas/ModalPdfDownload.vue';
import ModalVentasVue from '../../../components/ventas/ModalVentas.vue';
import ModalEnvioVue from '../../../components/ventas/ModalEnvio.vue';

import 'datatables.net-responsive-bs4';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css';

export default {
    name: "VentaLista",
    props: ["imginicial", "lst_modos_pago"],
    components: {
        ModalPdfDownloadVue,
        ModalVentasVue,
        ModalEnvioVue,
    },
    data() {
        const today = new Date().toISOString().slice(0, 10);
        return {
            tabla: null,
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
                fechaInicial: today,
                fechaFinal: today,
                cliente: "",
                numero_doc: "",
                tamanio: 10,
                page: 1,
            },
            fechaInicial: today,
            fechaFinal: today,
            cliente: "",
            cliente_id: null,
            numero_doc: "",
            ventasPendientes: [],
            loading: false,
            pdfData: null,
            modopagos: []
        };
    },
    watch: {
        params: {
            handler() {
                this.$nextTick(this.Lista);
            },
            deep: true,
        },
        cliente(value) {
            this.params.cliente = value;
            this.params.page = 1;
        },
        numero_doc(value) {
            this.params.numero_doc = value;
            this.params.page = 1;
        }
    },
    async created() {

    },
    methods: {
        cambiarTallas(documento_id) {
            const url = route('venta.cambiarTallas.create', documento_id);

            window.location.href = url;
        },
        async updateDataEnvio(data_envio) {
            try {
                const res = await axios.post(route('ventas.despachos.updateDespacho'), data_envio);
            } catch (error) {

            }

        },
        async setDataEnvio(documento_id) {
            //========= TRAER LA DATA DE ENVÍO DEL DOCUMENTO ========
            try {
                const res = await axios.get(route('ventas.despachos.getDespacho', documento_id));
                if (res.data.success) {
                    //======= PASAR DATA DESPACHO AL MODAL ENVÍO =========
                    this.$refs.modalEnvioRef.metodoHijo(res.data.despacho, documento_id);

                    $("#modal_envio").modal("show");

                } else {
                    toastr.error(res.data.exception, res.data.message);
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
            let estado = ``;

            if (data.sunat == '1' && data.cdr_response_code == '0') {
                estado = `<span class='badge badge-primary' d-block>ACEPTADO</span>`;
            }
            if (data.sunat == '1' && data.cdr_response_code != '0') {
                estado = `<span class='badge badge-danger' d-block>RECHAZADO</span>`;
            }
            if (data.sunat == '1' && data.cdr_response_code == 'EN ESPERA') {
                estado = `<span class='badge badge-warning' d-block>ENVIADO</span>`;
            }
            if (data.sunat == '2') {
                estado = `<span class='badge badge-danger' d-block>NULA</span>`;
            }
            if (data.sunat == '0') {
                estado = `<span class='badge badge-success' d-block>REGISTRADO</span>`;
            }
            if (data.estado === 'ANULADO') {
                estado = `<span class='badge badge-danger' d-block>ANULADO</span>`;
            }

            return estado;

        },
        enviarSunat(id) {

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
                        const data = { success: false, message: "ERROR EN LA SOLICITUD", exception: error };
                        return data;
                    }
                }
            }).then((result) => {

                if (result.value && result.value.success) {
                    $('#dt-ventas').DataTable().ajax.reload(null, false);
                    toastr.success(result.value.message, 'DOCUMENTO ENVIADO A SUNAT', { timeOut: 5000 });
                }

                if (result.value && !result.value.success) {
                    $('#dt-ventas').DataTable().ajax.reload(null, false);
                    toastr.error(result.value.exception, result.value.message, { timeOut: 0 });
                }

                if (result.dismiss === Swal.DismissReason.cancel) {
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

                        // Axios con CSRF (Laravel ya lo configura si usas axios por defecto)
                        axios.post(route('ventas.getDocumentClient'), {
                            cliente_id: item.cliente_id,
                            condicion_id: item.condicion_id
                        })
                            .then(response => {
                                if (response.data.success) {
                                    const { ventas } = response.data;
                                    me.ventasPendientes = ventas;
                                    me.cliente_id = item.cliente_id;
                                    $('#modal_ventas').modal('show');
                                }

                                Swal.resumeTimer();
                            })
                            .catch(error => {
                                console.error('Error al obtener documentos del cliente:', error);
                                Swal.resumeTimer();
                            });
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
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
                    var url = route('ventas.documento.guiaCreate', { id });
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
                    return route('ventas.documento.convertirCreate', { id });
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
        },
        PintarRowTable(aData) {
            if (aData.notas > 0) {
                return { 'background-color': "#FDEBD0" };
            }

            if (aData.convert_en_id) {
                return { 'background-color': "#EBDEF0" }
            }

            if (aData.cambio_talla == '1') {
                return { 'background-color': "#E3E9FE" }
            }

            if (aData.guia_id) {
                return { 'background-color': "#caffcc" }
            }

        },
        async regularizarVenta(documento) {
            toastr.clear();

            Swal.fire({
                title: `ANULAR EL DOC ${documento.serie}-${documento.correlativo}`,
                text: "SE GENERARÁ UN NUEVO DOC DE VENTA COMO REEMPLAZO!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "SÍ!"
            }).then(async (result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'PROCESANDO',
                        text: 'ANULANDO DOC DE VENTA',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });


                    try {

                        const res = await axios.post(route('ventas.regularizarVenta'), {
                            documento_id: documento.id
                        });

                        const success = res.data.success;
                        if (success) {
                            const message = res.data.message;

                            //======== ACTUALIZANDO LISTADO =====
                            this.Lista();

                            //========= RESPUESTA EXITOSA ======
                            toastr.success(message, 'OPERACIÓN COMPLETADA', {
                                timeOut: 0,
                            });

                            const url_open_pdf = route("ventas.documento.comprobante", { id: res.data.documento_id, size: 80 });
                            window.open(url_open_pdf, 'Comprobante SISCOM', 'location=1, status=1, scrollbars=1,width=900, height=600');
                        } else {

                            //========== MANEJANDO ERRORES DE VALIDACIÓN DEL REQUEST =====
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');

                        }
                    } catch (error) {
                        toastr.error(error, 'ERROR EN LA PETICIÓN REGULARIZAR VENTA', {
                            timeOut: 0,
                        });
                    } finally {
                        Swal.close();
                    }

                }
            });
        },
        filtrarDtVentas() {
            if (this.tabla) {
                this.tabla.ajax.reload();
            }
        },
        getRowByIdVue(table, id) {
            const allRows = table.rows().data().toArray();
            return allRows.find(row => row.id == id);
        }

    },
    mounted() {
        this.$nextTick(() => {
            const vm = this;
            vm.tabla = $('#dt-ventas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: route('ventas.getVentas'),
                    type: 'GET',
                    data: function (d) {
                        d.fechaInicio = vm.fechaInicial;
                        d.fechaFin = vm.fechaFinal;
                    }
                },
                responsive: true,
                createdRow: function (row, data, dataIndex) {
                    $(row).addClass('letrapequeña');
                },
                initComplete: function () {
                    $('.dropdown-toggle').dropdown();
                },
                drawCallback: function () {
                    $('.dropdown-toggle').dropdown();
                },
                columns: [
                    { data: 'id', name: 'cd.id', searchable: false },
                    { data: 'cotizacion_id', name: 'co.id', searchable: false },
                    { data: 'convert_de_serie', name: 'cd.convert_de_serie', searchable: false },
                    {
                        searchable: false,
                        data: null,
                        name: 'cd.pedido_id',
                        render: function (data, type, row) {
                            if (row.pedido_id) {
                                return `<p style="margin:0;">PE-${row.pedido_id}</p><p style="margin:0;">${row.tipo_doc_venta_pedido}</p>`;
                            }
                            return '-';
                        }
                    },
                    { data: 'regularizado_de_serie', name: 'cd.regularizado_de_serie', searchable: false },
                    { data: 'numero_doc', name: 'numero_doc' },
                    { data: 'fecha_documento', name: 'cd.fecha_documento', searchable: false },
                    { data: 'registrador_nombre', name: 'u.usuario', searchable: false },
                    { data: 'sede_nombre', name: 'es.nombre', searchable: false },
                    { data: 'almacen_nombre', name: 'cd.almacen_nombre' },
                    { data: 'cliente', name: 'cd.cliente' },
                    { data: 'total_pagar', name: 'cd.total_pagar', searchable: false },
                    { data: 'condicion', name: 'condicions.descripcion', searchable: false },
                    { data: 'estado_pago', name: 'cd.estado_pago', searchable: false },
                    {
                        searchable: false,
                        data: 'sunat',
                        name: 'cd.sunat',
                        render: function (data, type, row) {
                            let estado = '';

                            if (row.estado === 'ANULADO') {
                                estado = `<span class='badge badge-danger d-block'>ANULADO</span>`;
                            } else if (data == '1' && row.cdr_response_code == '0') {
                                estado = `<span class='badge badge-primary d-block'>ACEPTADO</span>`;
                            } else if (data == '1' && row.cdr_response_code != '0' && row.cdr_response_code !== 'EN ESPERA') {
                                estado = `<span class='badge badge-danger d-block'>RECHAZADO</span>`;
                            } else if (data == '1' && row.cdr_response_code === 'EN ESPERA') {
                                estado = `<span class='badge badge-warning d-block'>ENVIADO</span>`;
                            } else if (data == '2') {
                                estado = `<span class='badge badge-danger d-block'>NULA</span>`;
                            } else if (data == '0') {
                                estado = `<span class='badge badge-success d-block'>REGISTRADO</span>`;
                            }

                            return estado;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                            <div class="btn-group" role="group">
                                <button data-id="${row.id}"  class="btn btn-dark btn-sm btn-pdf" title="PDF">
                                    <strong>PDF</strong>
                                </button>
                                <button class="btn btn-info btn-sm btn-xml" data-id="${row.id}" title="XML">
                                    <strong>XML</strong>
                                </button>
                            </div>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {

                            let acciones = `
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-th"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                `;

                            //======== ENVIAR SUNAT =========
                            if (data.sunat == '0' && data.tipo_venta != "129" && data.estado == 'ACTIVO') {

                                acciones += `<a data-id="${row.id}" class="dropdown-item btn-enviar-sunat" href="javascript:void(0);">
                                                <i class="fa fa-send" style="color: #0065b3;"></i> Sunat
                                            </a>`;
                            }

                            //======= NOTAS CRÉDITO ========
                            if (
                                (row.sunat == '1' || row.notas > 0)
                                && row.tipo_venta != "129"
                                && row.estado == 'ACTIVO'
                                && row.estado_pago == 'PAGADA'
                            ) {
                                acciones += `<a href="${vm.routes(row.id, 'NOTAS')}" class="dropdown-item">
                                                <i class="fa fa-file-o" style="color: #77600e;"></i> Notas
                                            </a>`;
                            }

                            //====== GUÍA REMISIÓN ========
                            if (
                                row.sunat == '1' &&
                                row.notas == 0 &&
                                !row.guia_id &&
                                row.estado == 'ACTIVO'
                            ) {
                                acciones += `<a href="javascript:void(0);" class="dropdown-item btn-generar-guia" data-id="${row.id}" title="Guía Remisión">
                                                <i class="fa fa-file"></i> Guía
                                            </a>`;
                            }

                            //======== NOTA DEVOLUCIÓN ========
                            if (
                                row.tipo_venta == 129 &&
                                (
                                    (row.condicion == 'CONTADO' && row.estado_pago == 'PAGADA' && row.estado == 'ACTIVO') ||
                                    (row.condicion == 'CREDITO' || row.condicion == 'CRÉDITO')
                                )
                            ) {
                                acciones += `<a href="${vm.routes(row.id, 'DEVO')}" class="dropdown-item" title="Nota de devolución">
                                                <i class="fa fa-file-o"></i> Devoluciones
                                            </a>`;
                            }

                            //======== EDITAR DOCUMENTO ========
                            if (
                                row.estado_pago === 'PENDIENTE' &&
                                row.sunat === '0' &&
                                !row.convert_en_id &&
                                !row.convert_de_id &&
                                !row.pedido_id &&
                                row.notas == 0 &&
                                row.estado === 'ACTIVO'
                            ) {
                                acciones += `<a href="${vm.routes(row.id, 'EDITAR')}" class="dropdown-item" title="Editar">
                                    <i class="fas fa-edit" style="color:chocolate;"></i> Editar
                                </a>`;
                            }

                            //========= CAMBIO TALLA =======
                            if (row.notas == 0 && row.estado === 'ACTIVO' && !row.convert_en_id) {
                                acciones += `<a href="javascript:void(0);" data-id="${row.id}" class="dropdown-item btn-cambiar-talla" title="Cambio de Talla">
                                    <i class="fas fa-exchange-alt" style="color: #3307ab;"></i> Cambio de Talla
                                </a>`;
                            }

                            //========= CONVERTIR DOC VENTA ==========
                            if (
                                row.tipo_venta == 129 &&
                                !row.convert_en_id &&
                                row.notas == 0 &&
                                row.estado === 'ACTIVO' &&
                                row.estado_pago === 'PAGADA'
                            ) {
                                acciones += `<a href="${vm.routes(row.id, 'CONVERTIR')}" class="dropdown-item" title="Convertir">
                                    <i class="fas fa-file-invoice" style="color: blue;"></i> Convertir
                                </a>`;
                            }


                            // PAGAR - Contado, pendiente, tipo_venta 129, activo
                            if (
                                row.condicion === 'CONTADO' &&
                                row.estado_pago === 'PENDIENTE' &&
                                row.tipo_venta == '129' &&
                                row.estado === 'ACTIVO'
                            ) {
                                acciones += `<a href="javascript:void(0);" class="dropdown-item btn-pagar" data-row='${JSON.stringify(row)}' title="Pagar">
                                    <i class="fa fa-money" style="color: #007502;"></i> Pagar
                                </a>`;
                            }

                            // PAGAR - Contado, pendiente, tipo_venta distinto de 129, sin convert_de_id, activo
                            if (
                                row.condicion === 'CONTADO' &&
                                row.estado_pago === 'PENDIENTE' &&
                                row.tipo_venta != '129' &&
                                (!row.convert_de_id || row.convert_de_id === '') &&
                                row.estado === 'ACTIVO'
                            ) {
                                acciones += `<a href="javascript:void(0);" class="dropdown-item btn-pagar" data-row='${JSON.stringify(row)}' title="Pagar">
                                    <i class="fa fa-money" style="color: #007502;"></i> Pagar
                                </a>`;
                            }

                            // REGULARIZAR - regularize = 1, sunat != 2, cdr != 0, activo, tipo_venta != 129
                            if (
                                row.regularize == '1' &&
                                row.sunat != '2' &&
                                row.cdr_response_code != '0' &&
                                row.estado === 'ACTIVO' &&
                                row.tipo_venta != '129'
                            ) {
                                acciones += `<a href="javascript:void(0);" class="dropdown-item btn-regularizar" data-row='${JSON.stringify(row)}' title="Anular y replicar">
                                    <i class="far fa-copy"></i> ANULAR Y REPLICAR
                                </a>`;
                            }

                            // DESPACHO - estado_despacho distinto de 'DESPACHADO', tiene valor, sin notas, activo
                            if (
                                row.estado_despacho &&
                                row.estado_despacho !== 'DESPACHADO' &&
                                row.notas == 0 &&
                                row.estado === 'ACTIVO'
                            ) {
                                acciones += `<a href="javascript:void(0);" class="dropdown-item btn-despacho" data-id="${row.id}" title="Editar datos despacho">
                                    <i class="fas fa-truck"></i> DESPACHO
                                </a>`;
                            }


                            acciones += `</div></div>`;
                            return acciones;
                        }
                    }
                ],
                rowCallback: (row, data, index) => {
                    const color = this.PintarRowTable(data);
                    $(row).attr('style', color);
                },
                language: {
                    processing: "Procesando...",
                    search: "Buscar por doc, almacén, cliente:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                    },
                    autoFill: {
                        cancel: "Cancelar",
                        fill: "Rellenar todas las celdas con",
                        fillHorizontal: "Rellenar celdas horizontalmente",
                        fillVertical: "Rellenar celdas verticalmente"
                    },
                    decimal: ",",
                    thousands: ".",
                    select: {
                        rows: {
                            _: "%d filas seleccionadas",
                            0: "Haga clic en una fila para seleccionarla",
                            1: "1 fila seleccionada"
                        }
                    }
                }
            });

            vm.tabla.on('draw', function () {
                console.log(vm.tabla.rows().data().toArray());
            });

            // Delegación de evento para botón PDF
            $('#dt-ventas tbody').on('click', '.btn-pdf', function () {
                const id = $(this).data('id');
                const rowData = vm.getRowByIdVue(vm.tabla, id);

                console.log("Row Data:", rowData);

                if (rowData) {
                    vm.ModalPdf(rowData);
                } else {
                    console.warn("No se encontró la fila con id:", id);
                }
            });

            // Botón XML
            $('#dt-ventas tbody').on('click', '.btn-xml', function () {
                const id = $(this).data('id');
                const rowData = $('#dt-ventas').DataTable().row($(this).closest('tr')).data();
                vm.xmlElectronico(rowData.id); // Aquí puedes usar solo el ID o el row completo si lo necesitas
            });

            //========= BTN ENVIAR SUNAT ========
            $(document).on('click', '.btn-enviar-sunat', function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                vm.enviarSunat(id);
            });

            //======== BTN GUIA ======
            $(document).on('click', '.btn-generar-guia', function () {
                const id = $(this).data('id');
                vm.guia(id);
            });

            //======= BTN CAMBIAR TALLA ========
            $(document).on('click', '.btn-cambiar-talla', function () {
                const id = $(this).data('id');
                vm.cambiarTallas(id);
            });

            $('#dt-ventas').on('click', '.btn-pagar', function () {
                const row = $(this).data('row');
                vm.Pagar(row);
            });

            $('#dt-ventas').on('click', '.btn-regularizar', function () {
                const row = $(this).data('row');
                vm.regularizarVenta(row);
            });

            $('#dt-ventas').on('click', '.btn-despacho', function () {
                const id = $(this).data('id');
                vm.setDataEnvio(id);
            });


        });
    }
};
</script>
<style>
.tables_wrapper table.table-index tbody td {
    vertical-align: middle !important;
}

.dropdown-menu {
    max-height: 100px;
    overflow-y: auto;
}
</style>
