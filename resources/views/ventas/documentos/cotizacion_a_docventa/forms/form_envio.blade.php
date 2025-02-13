<form id="frmEnvio" class="formulario">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="row justify-content-between">
                <div class="col-6">
                    <label for="" style="font-weight: bold;">UBIGEO</label>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <button onclick="borrarEnvio()" type="button" class="btn btn-danger">BORRAR ENVÍO</button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3 col-md-3">
                    <div class="form-group">
                        <label class="required" for="departamento">Departamento</label>
                        <select class="select2_form" name="departamento" id="departamento" onchange="setUbicacionDepartamento(this.value,'first')">
                            @foreach ($departamentos as $departamento)
                                <option @if ($departamento->id == 13) selected @endif value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                            @endforeach
                        </select>
                      
                    </div>
                </div>
                <div class="col-3 col-md-3">
                    <div class="form-group">
                        <label class="required" for="provincia">Provincia</label>
                        <select class="select2_form form-control" name="provincia" id="provincia" onchange="setUbicacionProvincia(this.value,'first')">
                        </select>
                    </div>
                </div>
                <div class="col-3 col-md-3">
                    <div class="form-group">
                        <label class="required" for="distrito">Distrito</label>
                        <select class="select2_form form-control" name="distrito" id="distrito" onchange="setDistrito()">
                        </select>
                      
                    </div>
                </div>
                <div class="col-3 col-md-3">
                    <div class="form-group">
                        <label class="required" for="zona">Zona</label>
                        <input type="text" id="zona" name="zona"
                        required   class=" text-center form-control" readonly>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-4">
                    <label class="required" for="" style="font-weight: bold;">TIPO DE ENVÍO</label>
                    <select name="tipo_envio" id="tipo_envio" class="form-control select2_form" onchange="getEmpresasEnvio()">

                    </select>
                    
                </div>
                <div class="col-4">
                    <label class="required" for="" style="font-weight: bold;">TIPO PAGO</label>
                    <select name="tipo_pago_envio" id="tipo_pago_envio" class="form-control select2_form">

                    </select>
                  
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-4">
                    <label class="required" for="vselectEmpresa" style="font-weight: bold;">EMPRESAS</label>
                    <select name="empresa_envio" id="empresa_envio" class="form-control select2_form" onchange="getSedesEnvio()">
                    </select>
                
                </div>
                <div class="col-6">
                    <label class="required" for="" style="font-weight: bold;">SEDES</label>
                    <select name="sede_envio" id="sede_envio" class="form-control select2_form">
                    </select>
            
                </div>
            </div>
            <div class="row mt-3" v-if="mostrar_entrega_domicilio">
                <div class="col-4 d-flex align-items-center">
                    <div class="row" style="width: 100%;">
                        <div class="col-2 pr-0 d-flex align-items-center">
                            <input style="width: 50px;" id="check_entrega_domicilio" type="checkbox" class="form-control" onclick="entregaDomicilio(this.checked)">
                        </div>
                        <div class="col-9 pl-0">
                            <label for="check_entrega_domicilio" class="mb-0" style="font-weight: bold;">ENTREGA EN DOMICILIO</label>
                        </div>
                    </div>
                </div>
                <div class="col-7">
                    <label id="lbl_direccion_entrega" for="direccion_entrega" style="font-weight: bold;">DIRECCION DE ENTREGA</label>
                    <input maxlength="100" readonly  type="text" class="form-control" id="direccion_entrega" name="direccion_entrega">
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-3">
                    <label for="origen_venta" style="font-weight: bold;">ORIGEN VENTA</label>
                    <select name="origen_venta" id="origen_venta" class="form-control select2_form">
                    </select>
                  
                </div>
                <div class="col-3">
                    <label for="fecha_envio" style="font-weight: bold;">FECHA ENVÍO</label>
                    <input id="fecha_envio" name="fecha_envio" type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-3">
                    <label for="observaciones" style="font-weight: bold;">OBS RÓTULO</label>
                    <textarea maxlength="35" id="obs-rotulo" name="obs-rotulo" class="form-control"></textarea>
                </div>
                <div class="col-3">
                    <label for="observaciones" style="font-weight: bold;">OBS DESPACHO</label>
                    <textarea id="obs-despacho" name="obs-rotulo" class="form-control"></textarea>
                </div>
            </div>
            <hr>
            <label for="" style="font-weight: bold;">DATOS DEL DESTINATARIO</label>
            <div class="row">
                <div class="col-3">
                    <label class="required" for="tipo_doc_destinatario">TIPO DOCUMENTO</label>
                    <select onchange="cambiarTipoDocDest(this.value)" class="form form-control" 
                    name="tipo_doc_destinatario" id="tipo_doc_destinatario">

                    </select>
                </div>
                <div class="col-4">
                    <label class="required" for="nro_doc_destinatario">Nro. 
                        <span class="span-tipo-doc-dest"></span>
                    </label>
                        <div class="input-group">
                            <input type="text" id="nro_doc_destinatario" class="form-control" 
                            maxlength="8" required>

                            <span class="input-group-append" id="btn-consultar-dni">
                                <button type="button" style="color:white" class="btn btn-primary"
                                    onclick="consultarDocumento()">
                                    <i class="fa fa-search"></i>
                                    <span id="entidad"> CONSULTAR</span>
                                </button>
                            </span>
                        </div>
                </div>
                <div class="col-5">
                    <label class="required" for="nombres_destinatario">Nombres</label>
                    <input required type="text" id="nombres_destinatario" 
                    class="form-control"> 
                </div>
            </div>
        </div>
    </div>
</form>