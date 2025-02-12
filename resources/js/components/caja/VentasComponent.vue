<template>
    <div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-12">
                <h2 style="text-transform:uppercase">
                    <b>Listado de Documentos de Venta de Hoy </b>
                </h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a @click="irHome()">Panel de Control</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong>Documentos de Ventas</strong>
                    </li>
                </ol>
            </div>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table
                                    class="table dataTables-documento table-striped table-bordered table-hover"
                                    style="text-transform:uppercase"
                                >
                                    <thead>
                                        <tr>
                                            <th
                                                colspan="2"
                                                class="text-center"
                                            ></th>
                                            <th colspan="5" class="text-center">
                                                DOCUMENTO DE VENTA
                                            </th>
                                            <th colspan="4" class="text-center">
                                                FORMAS DE PAGO
                                            </th>
                                            <th
                                                colspan="4"
                                                class="text-center"
                                            ></th>
                                        </tr>
                                        <tr>
                                            <th style="display:none;"></th>
                                            <th class="text-center">C.O</th>
                                            <th class="text-center"># DOC</th>
                                            <th class="text-center">
                                                FECHA DOC.
                                            </th>
                                            <th class="text-center">TIPO</th>
                                            <th class="text-center">CLIENTE</th>
                                            <th class="text-center">MONTO</th>
                                            <th class="text-center">TRANSF.</th>
                                            <th class="text-center">OTROS</th>
                                            <th class="text-center">EFECT.</th>
                                            <th class="text-center">TIEMPO</th>
                                            <th class="text-center">ESTADO</th>
                                            <th class="text-center">SUNAT</th>
                                            <th class="text-center">
                                                DESCARGAS
                                            </th>
                                            <th class="text-center">
                                                ACCIONES
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="modal inmodal"
            id="modal_descargas_pdf"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                        >
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title descarga-title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 text-center">
                                <div class="form-group">
                                    <button class="btn btn-info file-pdf">
                                        <i class="fa fa-file-pdf-o"></i></button
                                    ><br />
                                    <b>Descargar A4</b>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 text-center">
                                <div class="form-group">
                                    <button class="btn btn-info file-ticket">
                                        <i class="fa fa-file-o"></i></button
                                    ><br />
                                    <b>Descargar Ticket</b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    data-dismiss="modal"
                                >
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                    <table class="table dataTables-ventas-pendientes table-striped table-bordered table-hover">
                                        <thead>
                                            <th>TIPO DOC</th>
                                            <th># DOC</th>
                                            <th>FECHA DOC</th>
                                            <th>MONTO</th>
                                            <th><i class="fa fa-dashboard"></i></th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6 text-left">
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal inmodal" id="modal_pago" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title pago-title"></h4>
                        <small class="font-bold pago-subtitle"></small>
                    </div>
                    <div class="modal-body">
                        <form v-on:submit.prevent="submitFormularioPago()" id="pago_venta" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12 col-md-6 br">
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Venta</label>
                                        <input type="text" class="form-control" v-model="form.venta_id" id="venta_id" name="venta_id" readonly>
                                        <input type="hidden" name="_token">
                                    </div>
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Tipo Pago</label>
                                        <input type="text" class="form-control" id="tipo_pago_id" name="tipo_pago_id" v-model="form.tipo_pago_id" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Monto</label>
                                        <input type="text" class="form-control" v-model="form.monto_venta" id="monto_venta" name="monto_venta" onkeypress="return filterFloat(event, this);" readonly>
                                    </div>
                                    <div class="form-group|">
                                        <label class="col-form-label required">Efectivo</label>
                                        <input type="text" class="form-control" v-model="form.efectivo" id="efectivo" name="efectivo" onkeypress="return filterFloat(event, this);" @keyup="changeEfectivo()">
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Modo de pago</label>
                                        <v-select
                                            :options="desc_pagos"
                                            placeholder="SELECCIONAR"
                                            v-model="tipo_pago"
                                            @input="setSelectedPago"
                                        ></v-select>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-form-label required">Importe</label>
                                        <input type="text" class="form-control" id="importe" v-model="form.importe" name="importe" onkeypress="return filterFloat(event, this);" @keyup="changeImporte()">
                                    </div>
                                    <div class="form-group d-none" id="div_cuentas">
                                        <label class="col-form-label">Cuentas</label>
                                        <v-select
                                            :options="cuentas"
                                            placeholder="SELECCIONAR"
                                            v-model="cuenta"
                                            @input="setSelectedCuenta"
                                        ></v-select>
                                    </div>
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Cuenta</label>
                                        <input type="text" class="form-control" id="cuenta_id" name="cuenta_id" v-model="form.cuenta_id" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label id="imagen_label">Imagen:</label>

                                        <div class="custom-file">
                                            <input id="imagen" type="file" name="imagen" class="custom-file-input" @change="changeImage()" accept="image/*">

                                            <label for="imagen" id="imagen_txt"
                                                class="custom-file-label selected">Seleccionar</label>

                                            <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>

                                        </div>
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <div class="col-6 align-content-center">
                                            <div class="row justify-content-end">
                                                <a href="javascript:void(0);" id="limpiar_imagen" @click="limpiarImagen()">
                                                    <span class="badge badge-danger">x</span>
                                                </a>
                                            </div>
                                            <div class="row justify-content-center">
                                                <p>
                                                    <img class="imagen" src="/img/default.png"
                                                        alt="">
                                                    <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6 text-left">
                            <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary btn-sm" form="pago_venta" :disabled="iPagando == 1"><i class="fa fa-save"></i> Guardar</button>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal inmodal" id="modal_pago_show" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="max-width: 60%;">
                <div class="modal-content animated bounceInRight">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title pago-title"></h4>
                        <small class="font-bold pago-subtitle"></small>
                    </div>
                    <div class="modal-body">
                        <form v-on:submit.prevent="updateFormularioPago()" id="update_pago_venta" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12 col-md-6 br">
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Venta</label>
                                        <input type="text" class="form-control" v-model="form_show.venta_id" id="venta_id" name="venta_id" readonly>
                                    </div>
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Tipo Pago</label>
                                        <input type="text" class="form-control" v-model="form_show.tipo_pago_id" id="tipo_pago_id" name="tipo_pago_id" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Monto</label>
                                        <input type="text" class="form-control" v-model="form_show.monto_venta" id="monto_venta" name="monto_venta" onkeypress="return filterFloat(event, this);" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Efectivo</label>
                                        <input type="text" class="form-control" v-model="form_show.efectivo" id="efectivo" name="efectivo" onkeypress="return filterFloat(event, this);" @keyup="changeEfectivoShow()">
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Modo de pago</label>
                                        <v-select
                                            :options="desc_pagos_show"
                                            placeholder="SELECCIONAR"
                                            v-model="tipo_pago_show"
                                            @input="setSelectedPagoShow"
                                        ></v-select>
                                    </div>
                                    <div class="form-group">
                                        <label  class="col-form-label required">Importe</label>
                                        <input type="text" class="form-control" id="importe" v-model="form_show.importe" name="importe" onkeypress="return filterFloat(event, this);" @keyup="changeImporteShow()">
                                    </div>
                                     <div class="form-group d-none" id="div_cuentas">
                                        <label class="col-form-label">Cuentas</label>
                                        <v-select
                                            :options="cuentas_show"
                                            placeholder="SELECCIONAR"
                                            v-model="cuenta_show"
                                            @input="setSelectedCuentaShow"
                                        ></v-select>
                                    </div>
                                    <div class="form-group d-none">
                                        <label class="col-form-label required">Cuenta</label>
                                        <input type="text" class="form-control" id="cuenta_id" name="cuenta_id" v-model="form_show.cuenta_id" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">

                                    <div class="form-group">
                                        <label id="imagen_label">Imagen 1:</label>
                                        <div class="custom-file">
                                            <input id="imagen_update" type="file" name="imagen" class="custom-file-input" accept="image/*" @change="changeImageShow()">

                                            <label for="imagen" id="imagen_txt"
                                                class="custom-file-label selected">Seleccionar</label>

                                            <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>
                                            <input type="hidden" name="ruta_pago" id="ruta_pago">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label id="imagen_label">Imagen 2:</label>
                                        <div class="custom-file">
                                            <input id="imagen_update_2" type="file" name="imagen_2" class="custom-file-input" accept="image/*" @change="changeImageShow2()">

                                            <label for="imagen_2" id="imagen_txt_2"
                                                class="custom-file-label selected">Seleccionar</label>

                                            <div class="invalid-feedback"><b><span id="error-imagen-2"></span></b></div>
                                            <input type="hidden" name="ruta_pago_2" id="ruta_pago_2">
                                        </div>
                                    </div>

                                    <div class="form-group row justify-content-center">
                                        <div class="col-6 align-content-center">
                                            <div class="row justify-content-end">
                                                <a href="javascript:void(0);" id="limpiar_imagen"  @click="limpiarImagenShow()">
                                                    <span class="badge badge-danger">x</span>
                                                </a>
                                            </div>
                                            <div class="row justify-content-center">
                                                <p>

                                                    <img class="imagen_update" alt="IMG" style="width:140px;object-fit: cover;">
                                                    <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-6 align-content-center">
                                            <div class="row justify-content-end">
                                                <a href="javascript:void(0);" id="limpiar_imagen_2"  @click="limpiarImagenShow2()">
                                                    <span class="badge badge-danger">x</span>
                                                </a>
                                            </div>
                                            <div class="row justify-content-center">
                                                <p>
                                                    <img class="imagen_update_2" alt="IMG"  height="200px" style="width:140px;border-radius:10%;object-fit: cover;">
                                                    <input id="url_imagen_2" name="url_imagen_2" type="hidden" value="">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary btn-sm" form="update_pago_venta" :disabled="iEditando == 1"><i class="fa fa-pencil"></i> Editar</button>
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>



import Axios from "axios";
import "datatables.net-bs4";
import "datatables.net-buttons-bs4";
export default {
    props: ["modospago"],
    data() {
        return {
            table: null,
            tableVentas: null,
            ventas: [],
            desc_pagos: [],
            desc_pagos_show: [],
            cuentas: [],
            cuentas_show: [],
            value_pagos: [],
            value_pagos_show: [],
            options: [],
            tipo_pago: null,
            tipo_pago_show: null,
            cuenta: null,
            cuenta_show: null,
            form: {
                venta_id: null,
                tipo_pago_id: null,
                importe: null,
                efectivo: null,
                monto_venta: null,
                cuenta_id: null,
                image: null,

            },
            form_show: {
                venta_id: null,
                tipo_pago_id: null,
                importe: null,
                efectivo: 0.00,
                monto_venta: null,
                cuenta_id: null,
                image: null,

            },
            iPagando: 0,
            iEditando: 0
        };
    },
    mounted() {
        
        let $this = this;
        for(let i = 0; i < $this.modospago.length; i++)
        {
            let pago = {'code': $this.modospago[i].id, 'label': $this.modospago[i].descripcion}
            $this.desc_pagos.push(pago);
            $this.desc_pagos_show.push(pago);
        }

        $this.loadTable();
        Echo.channel("ventasCaja").listen("VentasCajaEvent", e => {
            toastr.success("Nueva venta registrada");
            $this.actualizarTable();
        });

        $this.table.on('click','.pagar',function(){
            let data = $this.table.row($(this).closest("tr")).data();
            $('.ventas-title').html(data.cliente);
            $('#modal_ventas').modal('show');
            $this.initTableVentas(data.cliente_id, data.condicion_id);
        });

        $this.table.on('click','.btn-pdf',function(){
            let data = $this.table.row($(this).closest("tr")).data();
            let fn_pdf = 'comprobanteElectronico(' + data.id + ')';
            let fn_ticket = 'comprobanteElectronicoTicket(' + data.id + ')';
            $('.descarga-title').html(data.serie + '-' + data.correlativo);
            $('.file-pdf').attr('onclick',fn_pdf);
            $('.file-ticket').attr('onclick',fn_ticket);
            $('#modal_descargas_pdf').modal('show');
        });

        $this.tableVentas.on('click','.pagar',function(){
            let data =$this.tableVentas.row($(this).closest("tr")).data();
            $('.pago-title').html(data.numero_doc);
            $this.form.monto_venta = data.total;
            $this.form.venta_id = data.id;
            $this.form.importe = data.total;
            $this.form.efectivo = '0.00';
            $('.pago-subtitle').html(data.cliente);

            $('#modal_pago').modal('show');
            $this.tipo_pago = 'EFECTIVO';
            $this.form.tipo_pago_id = '1';
            $('#efectivo').attr('readonly', true);
            $('#importe').attr('readonly', true);
            $this.initCuentas(data.empresa_id);
        });

        $this.table.on('click','.verPago',function(){
            //========== LIMPIANDO LABEL QUE LLEVA EL NOMBRE DE LA IMG =======
            document.querySelector('#modal_pago_show #imagen_txt').textContent   =   'Seleccionar';
            document.querySelector('#modal_pago_show #imagen_txt_2').textContent   =   'Seleccionar';


            //======== EXTRAYENDO DATA DE LA FILA ================
            let data = $this.table.row($(this).closest("tr")).data();

            //========== COLOCANDO INFORMACIÓN DE LA FILA EN EL MODAL ==========
            $('#modal_pago_show .pago-title').html(data.numero_doc);
            $('#modal_pago_show #div_cuentas').addClass('d-none');
            //======== COLOCANDO RUTA_PAGO EN EL INPUT TYPE HIDDEN RUTA_PAGO =======
            $('#modal_pago_show #ruta_pago').val(data.ruta_pago)
            $('#modal_pago_show #ruta_pago_2').val(data.ruta_pago_2)

            let pago = {
                code: data.tipo_pago_id,
                label: data.tipo_pago
            }

            let cuenta = {
                code: data.banco_empresa_id,
                label: data.banco_empresa
            }

            $this.setSelectedPagoShow(pago)
            $this.setSelectedCuentaShow(cuenta)
            $this.form_show.monto_venta     = data.total
            $this.form_show.venta_id        = data.id

           

            //========= PARA LA IMAGEN 1 =======
            if(data.ruta_pago)
            {
                let ruta    = data.ruta_pago;
              
               ruta        = ruta.replace('public','');
               ruta        = '/storage'+ruta;
               
                
                //======== CUADRO DONDE SE VISUALIZA LA IMAGEN =======
                let imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen_update");
                imagenPrevisualizacion.src = ruta;

                //======== COLOCANDO NOMBRE DE LA IMG EN EL LABEL =========
                const imgName   = ruta.split('/').pop();
                $('#modal_pago_show #imagen_txt').addClass("selected").html(imgName);
            }
            else
            {
                $('#modal_pago_show #imagen_update').val('');
                let imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen_update");
                imagenPrevisualizacion.src = "/img/default.png";
            }

            //======== PARA LA IMAGEN 2 ============
            if(data.ruta_pago_2)
            {
                let ruta    = data.ruta_pago_2;
              
               ruta        = ruta.replace('public','');
               ruta        = '/storage'+ruta;
               
                
                //========= INPUT FILE =======
                let inputPrevisualizacion  = document.querySelector("#modal_pago_show .imagen_update_2");
                inputPrevisualizacion.src = ruta;
                
                //======== COLOCANDO NOMBRE DE LA IMG EN EL LABEL =========
                const imgName   = ruta.split('/').pop();
                $('#modal_pago_show #imagen_txt_2').addClass("selected").html(imgName);
            }
            else
            {
                $('#modal_pago_show #imagen_update').val('');
                let imagenPrevisualizacion = document.querySelector("#modal_pago_show .imagen_update_2");
                imagenPrevisualizacion.src = "/img/default.png";
            }

            if(data.cuenta_id)
            {
                $('#modal_pago_show #div_cuentas').removeClass('d-none');
                $this.initCuentasShow(data.empresa_id,data.cuenta_id);
            }
            if(data.tipo_pago_id != 1)
            {
                $this.form_show.efectivo = data.efectivo;
                $this.form_show.importe = data.importe;
            }
            else
            {
                $this.form_show.efectivo = 0.00;
                $this.form_show.importe = data.importe;
            }

            $('#modal_pago_show .pago-subtitle').html(data.cliente);
            $('#modal_pago_show').modal('show');
        });

     
    },
    methods: {
        irHome: function() {
            window.location = route("home");
        },
        loadTable: function() {
            this.table = $(".dataTables-documento").DataTable({
                dom: '<"html5buttons"B>lTfgitp',
                bPaginate: true,
                bLengthChange: true,
                bFilter: true,
                bInfo: true,
                bAutoWidth: false,
                processing: true,
                ajax: route("ventas.caja.getDocument"),
                columns: [
                    //DOCUMENTO DE VENTA
                    {
                        data: "cotizacion_venta",
                        className: "text-center letrapequeña",
                        visible: false
                    },

                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {
                            if (data.cotizacion_venta) {
                                return "<input type='checkbox' disabled checked>";
                            } else {
                                return "<input type='checkbox' disabled>";
                            }
                        }
                    },

                    {
                        data: "numero_doc",
                        className: "text-center letrapequeña"
                    },

                    {
                        data: "fecha_documento",
                        className: "text-center letrapequeña"
                    },

                    {
                        data: "tipo_venta",
                        className: "text-center letrapequeña",
                        visible: false
                    },

                    {
                        data: "cliente",
                        className: "text-left letrapequeña"
                    },

                    {
                        data: "total",
                        className: "text-center letrapequeña"
                    },
                    {
                        data: "transferencia",
                        className: "text-center letrapequeña"
                    },

                    {
                        data: "otros",
                        className: "text-center letrapequeña"
                    },
                    {
                        data: "efectivo",
                        className: "text-center letrapequeña"
                    },
                    {
                        data: "dias",
                        className: "text-center letrapequeña"
                    },

                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {
                            switch (data.estado) {
                                case "PENDIENTE":
                                    return (
                                        "<span class='badge badge-danger' d-block>" +
                                        data.estado +
                                        "</span>"
                                    );
                                    break;
                                case "PAGADA":
                                    return (
                                        "<span class='badge badge-primary verPago' style='cursor: pointer;' d-block>" +
                                        data.estado +
                                        "</span>"
                                    );
                                    break;
                                case "ADELANTO":
                                    return (
                                        "<span class='badge badge-success' d-block>" +
                                        data.estado +
                                        "</span>"
                                    );
                                    break;
                                case "DEVUELTO":
                                    return (
                                        "<span class='badge badge-warning' d-block>" +
                                        data.estado +
                                        "</span>"
                                    );
                                    break;
                                default:
                                    return (
                                        "<span class='badge badge-success' d-block>" +
                                        data.estado +
                                        "</span>"
                                    );
                            }
                        }
                    },

                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {
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
                        }
                    },
                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {
                            return (
                                "<button class='btn btn-info btn-pdf mb-1' title='PDF'>PDF</button>" +
                                "<button class='btn btn-info' onclick='xmlElectronico(" +
                                data.id +
                                ")' title='XML'>XML</button>"
                            );
                        }
                    },
                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {
                            let cadena = "";
                            if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE' && data.tipo_venta_id == '129')
                            {
                                cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' @click='fnPagar("+data+")' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                            }

                            if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE' && data.tipo_venta_id != 129 && (data.convertir == '' || data.convertir == null))
                            {
                                cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' @click='fnPagar("+data+")' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                            }
                            
                            if (
                                data.condicion == "CONTADO" &&
                                data.estado == "PAGADA"
                            ) {
                                cadena =
                                    cadena +
                                    "<button type='button' class='btn btn-sm btn-success m-1 verPago' title='Ver'><i class='fa fa-eye'></i> Ver Pago</button>";
                            }

                            return cadena;
                        }
                    }
                ],
                fnRowCallback: function(
                    nRow,
                    aData,
                    iDisplayIndex,
                    iDisplayIndexFull
                ) {
                    /*if (aData.sunat == 0 && aData.tipo_venta_id != 129) {
                        $('td', nRow).css('background-color', '#D6EAF8');
                    }

                    if (aData.sunat == 1 && aData.tipo_venta_id != 129) {
                        $('td', nRow).css('background-color', '#D1F2EB');
                    }*/

                    if (aData.notas > 0) {
                        $("td", nRow).css("background-color", "#FDEBD0");
                    }
                },
                language: {
                    url: window.location.origin + "/Spanish.json"
                },
                order: []
            });
            this.tableVentas = $('.dataTables-ventas-pendientes').DataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bInfo": true,
                "bAutoWidth": false,
                "data": this.ventas,
                "columns": [
                    //DOCUMENTO DE VENTA
                    {
                        data: 'tipo_venta',
                        className: "text-center letrapequeña",
                    },
                    {
                        data: 'numero_doc',
                        className: "text-center letrapequeña",
                    },
                    {
                        data: 'fecha_documento',
                        className: "text-center letrapequeña"
                    },
                    {
                        data: 'total',
                        className: "text-center letrapequeña",
                    },
                    {
                        data: null,
                        className: "text-center letrapequeña",
                        render: function(data) {

                            let cadena = '';

                            if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE' && data.tipo_venta_id == '129')
                            {
                                cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                            }

                            if(data.condicion == 'CONTADO' && data.estado == 'PENDIENTE' && data.tipo_venta_id != 129 && (data.convertir == '' || data.convertir == null))
                            {
                                cadena = cadena +
                                "<button type='button' class='btn btn-sm btn-primary m-1 pagar' title='Pagar'><i class='fa fa-money'></i> Pagar</button>";
                            }

                            return cadena;
                        }
                    }

                ],
                "language": {
                    "url": window.location.origin + "/Spanish.json"
                },
                "order": [],
            });
        },
        actualizarTable: function() {
            this.table.ajax.reload();
        },
        initTableVentas: function(cliente_id, condicion_id)
        {
            let $this = this;
            let timerInterval;
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
                        dataType : 'json',
                        type : 'post',
                        url : route('ventas.caja.getDocumentClient'),
                        data : {'_token': $('input[name=_token]').val(), 'cliente_id': cliente_id, 'condicion_id': condicion_id},
                        success: function(response) {
                            if (response.success) {
                                $this.ventas = [];
                                $this.ventas = response.ventas;
                                $this.loadTableVentas();
                                timerInterval = 0;
                                Swal.resumeTimer();
                                //console.log(colaboradores);
                            } else {
                                Swal.resumeTimer();
                                $this.ventas = [];
                                $this.loadTableVentas();
                            }
                        }
                    });
                },
                willClose: () => {
                    clearInterval(timerInterval)
                }
            });
        },
        loadTableVentas: function()
        {
            this.tableVentas.clear();
            this.tableVentas.rows.add(this.ventas);
            this.tableVentas.draw();
        },
        initCuentas:function(empresa_id)
        {
            let $this = this;
            $this.cuentas = [];
            let timerInterval;
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
                        dataType : 'json',
                        type : 'post',
                        url : route('ventas.documento.getCuentas'),
                        data : {'_token': $('input[name=_token]').val(), 'empresa_id': empresa_id},
                        success: function(response) {
                            if (response.success) {
                                if (response.cuentas.length > 0) {
                                    for(var i = 0;i < response.cuentas.length; i++)
                                    {
                                        let newOption = {'code': response.cuentas[i].id, 'label': response.cuentas[i].descripcion + ': ' + response.cuentas[i].num_cuenta};
                                        $this.cuentas.push(newOption);
                                    }

                                } else {
                                }
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
        },
        initCuentasShow:function(empresa_id)
        {
            let $this = this;
            $this.cuentas_show = [];
            let timerInterval;
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
                        dataType : 'json',
                        type : 'post',
                        url : route('ventas.documento.getCuentas'),
                        data : {'_token': $('input[name=_token]').val(), 'empresa_id': empresa_id},
                        success: function(response) {
                            if (response.success) {
                                if (response.cuentas.length > 0) {
                                    for(var i = 0;i < response.cuentas.length; i++)
                                    {
                                        let newOption = {'code': response.cuentas[i].id, 'label': response.cuentas[i].descripcion + ': ' + response.cuentas[i].num_cuenta};
                                        $this.cuentas_show.push(newOption);
                                    }

                                } else {
                                }
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
        },
        initCuentasShow:function(empresa_id)
        {
            let $this = this;
            $this.cuentas_show = [];
            let timerInterval;
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
                        dataType : 'json',
                        type : 'post',
                        url : route('ventas.documento.getCuentas'),
                        data : {'_token': $('input[name=_token]').val(), 'empresa_id': empresa_id},
                        success: function(response) {
                            if (response.success) {
                                if (response.cuentas.length > 0) {
                                    for(var i = 0;i < response.cuentas.length; i++)
                                    {
                                        let newOption = {'code': response.cuentas[i].id, 'label': response.cuentas[i].descripcion + ': ' + response.cuentas[i].num_cuenta};
                                        $this.cuentas_show.push(newOption);
                                    }

                                } else {
                                }
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
        },
        setSelectedPago: function(value)
        {
            let $this = this;
            let monto = $this.form.monto_venta;
            let importe = $this.form.importe;
            let efectivo = $this.form.efectivo;
            let suma = convertFloat(importe) + convertFloat(efectivo);
            $this.cuenta = '';
            $this.form.cuenta_id = null;
            $('#efectivo').attr('readonly', false);
            $('#importe').attr('readonly', false);
            if(value != null)
            {
                $this.form.tipo_pago_id = value.code;
                $this.tipo_pago = value.label;
                if(value.label == 'EFECTIVO')
                {
                    $('#efectivo').attr('readonly', true);
                    $('#importe').attr('readonly', true);
                    $this.form.efectivo = '0.00';
                    $this.form.importe = monto;
                }

                if(value.label == 'TRANSFERENCIA')
                {
                    $('#div_cuentas').removeClass('d-none');
                }else{
                    $('#div_cuentas').addClass('d-none');
                }
            }
            else
            {
                $this.form.tipo_pago_id = null;
            }

        },
        setSelectedPagoShow: function(value)
        {
            let $this = this;
            let monto = $this.form_show.monto_venta;
            let importe = $this.form_show.importe;
            let efectivo = $this.form_show.efectivo;
            let suma = convertFloat(importe) + convertFloat(efectivo);
            $this.cuenta_show = '';
            $this.form_show.cuenta_id = null;
            $('#modal_pago_show #efectivo').attr('readonly', false);
            $('#modal_pago_show #importe').attr('readonly', false);
            if(value != null)
            {
                $this.form_show.tipo_pago_id = value.code;
                $this.tipo_pago_show = value.label;
                if(value.label == 'EFECTIVO')
                {
                    $('#modal_pago_show #efectivo').attr('readonly', true);
                    $('#modal_pago_show #importe').attr('readonly', true);
                    $this.form_show.efectivo = '0.00';
                    $this.form_show.importe = monto;
                }

                if(value.label == 'TRANSFERENCIA')
                {
                    $('#modal_pago_show #div_cuentas').removeClass('d-none');
                }else{
                    $('#modal_pago_show #div_cuentas').addClass('d-none');
                }
            }
            else
            {
                $this.form_show.tipo_pago_id = null;
            }

        },
        changeEfectivo: function()
        {
            let $this = this;
            let modo = $this.tipo_pago;
            let monto = convertFloat($this.form.monto_venta);
            let efectivo = convertFloat($this.form.efectivo);
            let importe = $this.form.importe;
            if(modo != 'EFECTIVO')
            {
                let diferencia = monto - efectivo;
                $this.form.importe = diferencia.toFixed(2);
            }
        },
        changeImporte: function()
        {
            let $this = this;
            let modo = $this.tipo_pago;
            let monto = convertFloat($this.form.monto_venta);
            let importe = convertFloat($this.form.importe);
            let efectivo = $this.form.efectivo;
            if(modo != 'EFECTIVO')
            {
                let diferencia = monto - importe;
                $this.form.efectivo = diferencia.toFixed(2);
            }
        },
        changeImage: function()
        {
            var fileInput = document.getElementById('imagen');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            let $imagenPrevisualizacion = document.querySelector(".imagen");
            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                this.form.image = event.target.files[0]
                console.log(this.form.image)
                var data = userFile.src;
                $imagenPrevisualizacion.src = data;
                let fileName = $('#imagen').val().split('\\').pop();
                $('#imagen').next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                this.form.image = null
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen').attr("src", "/img/default.png")
            }
        },
        limpiarImagen: function()
        {
            $('.imagen').attr("src", "/img/default.png")
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen').val('')
            this.form.image = null
        },
        setSelectedCuenta: function(value)
        {
            let $this = this;
            if(value != null)
            {
                $this.form.cuenta_id = value.code;
                $this.cuenta = value.label;
            }
        },
        submitFormularioPago: function()
        {
            let $this = this;
            let frm = document.getElementById('pago_venta');
            let formData = new FormData(frm);
            // formData.forEach(function(value, key){
            //     console.log(key)
            //     console.log(value);
            // });
            $this.iPagando = 1;
            axios.post(route('ventas.caja.storePago'), formData)
            .then(response => {
                let respuesta = response.data;
                if (respuesta.result == 'success') {
                    toastr.success(respuesta.mensaje)
                    location.reload()
                } else {
                    let sHtmlMensaje = sHtmlErrores(respuesta.data.errors);
                    toastr.error(sHtmlMensaje);
                    $this.iPagando = 0;
                }
            })
            .catch(error => {
                let sHtmlMensaje = sHtmlErrores(error.responseJSON.errors);
                toastr.error(sHtmlMensaje);
            }).then(() => $this.iPagando = 0);
        },
        changeEfectivoShow: function()
        {
            let $this = this;
            let modo = $this.tipo_pago_show;
            let monto = convertFloat($this.form_show.monto_venta);
            let efectivo = convertFloat($this.form_show.efectivo);
            let importe = $this.form_show.importe;
            if(modo != 'EFECTIVO')
            {
                let diferencia = monto - efectivo;
                $this.form_show.importe = diferencia.toFixed(2);
            }
        },
        changeImporteShow: function()
        {
            let $this = this;
            let modo = $this.tipo_pago_show;
            let monto = convertFloat($this.form_show.monto_venta);
            let importe = convertFloat($this.form_show.importe);
            let efectivo = $this.form_show.efectivo;
            if(modo != 'EFECTIVO')
            {
                let diferencia = monto - importe;
                $this.form_show.efectivo = diferencia.toFixed(2);
            }
        },
        limpiarImagenShow: function()
        {
            $('.imagen_update').attr("src", "/img/default.png")
            var fileName = "Seleccionar"
            $('#modal_pago_show #imagen_txt').addClass("selected").html(fileName);
            $('#imagen_update').val('')
            $('#modal_pago_show #ruta_pago').val('')
            this.form_show.image = null
        },
        limpiarImagenShow2: function()
        {
            $('.imagen_update_2').attr("src", "/img/default.png")
            var fileName = "Seleccionar"
            $('#modal_pago_show #imagen_txt_2').addClass("selected").html(fileName);
            $('#imagen_update_2').val('')
            $('#modal_pago_show #ruta_pago_2').val('')
            this.form_show.image = null
        },
        changeImageShow: function()
        {
            var fileInput = document.getElementById('imagen_update');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            let $imagenPrevisualizacion = document.querySelector(".imagen_update");
            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen_update');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                this.form_show.image = event.target.files[0]
                console.log(this.form_show.image)
                var data = userFile.src;
                $imagenPrevisualizacion.src = data;
                let fileName = $('#imagen_update').val().split('\\').pop();
                $('#imagen_update').next('#modal_pago_show .custom-file-label').addClass("selected").html(fileName);
            } else {
                this.form_show.image = null
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen_update').attr("src", "/img/default.png")
            }
        },
        changeImageShow2: function()
        {
            var fileInput = document.getElementById('imagen_update_2');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            let $imagenPrevisualizacion = document.querySelector(".imagen_update_2");
            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen_update_2');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                this.form_show.image = event.target.files[0]
                console.log(this.form_show.image)
                var data = userFile.src;
                $imagenPrevisualizacion.src = data;
                let fileName = $('#imagen_update_2').val().split('\\').pop();
                $('#imagen_update_2').next('#modal_pago_show .custom-file-label').addClass("selected").html(fileName);
            } else {
                this.form_show.image = null
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen_update_2').attr("src", "/img/default.png")
            }
        },
        setSelectedCuentaShow: function(value)
        {
            let $this = this;
            if(value != null)
            {
                $this.form_show.cuenta_id = value.code;
                $this.cuenta_show = value.label;
            }
        },
        updateFormularioPago: function()
        {
            let $this = this;
            let frm = document.getElementById('update_pago_venta');
            let formData = new FormData(frm);
            formData.forEach(function(value, key){
                console.log(key)
                console.log(value);
            });
            $this.iEditando = 1;
            axios.post(route('ventas.caja.updatePago'), formData)
            .then(response => {
                let respuesta = response.data;
                if (respuesta.result == 'success') {
                    toastr.success(respuesta.mensaje)
                    location.reload()
                } else {
                    let sHtmlMensaje = sHtmlErrores(respuesta.data.errors);
                    toastr.error(sHtmlMensaje);
                    $this.iEditando = 0;
                }
            })
            .catch(error => {
                let sHtmlMensaje = sHtmlErrores(error.responseJSON.errors);
                toastr.error(sHtmlMensaje);
            }).then(() => $this.iEditando = 0);
        },
    },
    updated() {
        this.$nextTick(function () {

        });
    }
};
</script>
