<div class="modal fade" id="modal_envio" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Datos de envío</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmEnvio" class="formulario">
                    <div class="row mb-3">
                        <div class="col-12 col-md-12">
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <label for="" style="font-weight: bold;">UBIGEO</label>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <button onclick="borrarEnvio()" type="button" class="btn btn-danger">BORRAR ENVÍO
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-3 col-md-3">
                            <label style="font-weight: bold;" class="required" for="departamento">DEPARTAMENTO</label>
                            <select class="" name="departamento" id="departamento"
                                onchange="setUbicacionDepartamento(this.value,'first')">
                                @foreach ($departamentos as $departamento)
                                    <option @if ($departamento->id == 13) selected @endif
                                        value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3 col-md-3">
                            <label style="font-weight: bold;" class="required" for="provincia">PROVINCIA</label>
                            <select class="" name="provincia" id="provincia"
                                onchange="setUbicacionProvincia(this.value,'first')">
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-3 col-md-3">
                            <label style="font-weight: bold;" class="required" for="distrito">DISTRITO</label>
                            <select class="" name="distrito" id="distrito" onchange="setMdlDistrito()">
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-3 col-md-3">
                            <label class="required" style="font-weight: bold;" for="zona">ZONA</label>
                            <input type="text" id="zona" name="zona" class=" text-center form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="required" for="" style="font-weight: bold;">TIPO DE ENVÍO</label>
                            <select name="tipo_envio" id="tipo_envio" placeholder="Seleccionar"
                                onchange="getEmpresasEnvio()">
                                @foreach ($tipos_envio as $tipo_envio)
                                    <option value="{{ $tipo_envio->id }}">{{ $tipo_envio->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="required" for="" style="font-weight: bold;">TIPO PAGO</label>
                            <select name="tipo_pago_envio" id="tipo_pago_envio" class="">
                                @foreach ($tipos_pago_envio as $tipo_pago_envio)
                                    <option value="{{ $tipo_pago_envio->id }}">{{ $tipo_pago_envio->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="required" for="empresa_envio" style="font-weight: bold;">EMPRESAS</label>
                            <select required name="empresa_envio" id="empresa_envio" placeholder="Seleccionar"
                                onchange="getSedesEnvio()">
                                <option value="">Seleccionar</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="required" for="" style="font-weight: bold;">SEDES</label>
                            <select required name="sede_envio" id="sede_envio" class="" placeholder="Seleccionar">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 d-flex align-items-center">
                            <div class="row" style="width: 100%;">
                                <div class="col-2 pr-0 d-flex align-items-center">
                                    <input style="width: 50px;" id="check_entrega_domicilio" type="checkbox"
                                        class="form-control" onclick="entregaDomicilio(this.checked)">
                                </div>
                                <div class="col-9 pl-0">
                                    <label for="check_entrega_domicilio" class="mb-0"
                                        style="font-weight: bold;">ENTREGA EN DOMICILIO</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <label id="lbl_direccion_entrega" for="direccion_entrega"
                                style="font-weight: bold;">DIRECCION DE ENTREGA</label>
                            <input maxlength="100" readonly type="text" class="form-control"
                                id="direccion_entrega" name="direccion_entrega">
                        </div>
                    </div>
                    <div class="row mb-3 rowOrigen">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <label class="required" for="origen_venta" style="font-weight: bold;">ORIGEN
                                VENTA</label>
                            <select name="origen_venta" id="origen_venta" class="">
                                @foreach ($origenes_ventas as $origen_venta)
                                    <option value="{{ $origen_venta->id }}">{{ $origen_venta->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <label for="fecha_envio" style="font-weight: bold;">FECHA ENVÍO</label>
                            <input id="fecha_envio" name="fecha_envio" type="date" class="form-control"
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <label for="observaciones" style="font-weight: bold;">OBS RÓTULO</label>
                            <textarea maxlength="35" id="obs-rotulo" name="obs-rotulo" class="form-control"></textarea>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <label for="observaciones" style="font-weight: bold;">OBS DESPACHO</label>
                            <textarea id="obs-despacho" name="obs-despacho" class="form-control"></textarea>
                        </div>
                    </div>
                    <hr>
                    <label for="" style="font-weight: bold;">DATOS DEL DESTINATARIO</label>
                    <div class="row">
                        <div class="col-3">
                            <label class="required" for="tipo_doc_destinatario">TIPO DOCUMENTO</label>
                            <select onchange="cambiarTipoDocDest(this.value)" class=""
                                name="tipo_doc_destinatario" id="tipo_doc_destinatario" placeholder="Seleccionar">
                                @foreach ($tipos_documento as $tipo_documento)
                                    @if ($tipo_documento->id == 6 || $tipo_documento->id == 7)
                                        <option value="{{ $tipo_documento->id }}">{{ $tipo_documento->simbolo }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="required" for="nro_doc_destinatario">Nro.
                                <span class="span-tipo-doc-dest"></span>
                            </label>
                            <div class="input-group">
                                <input type="text" id="nro_doc_destinatario" class="form-control" maxlength="8"
                                    required>

                                <span class="input-group-append" id="btn-consultar-dni">
                                    <button type="button" style="color:white" class="btn btn-success"
                                        onclick="consultarDocumento()">
                                        <i class="fa fa-search"></i>
                                        <span id="entidad"> CONSULTAR</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-5">
                            <label class="required" for="nombres_destinatario">Nombres</label>
                            <input required type="text" id="nombres_destinatario" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="frmEnvio" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function eventsModalEnvio() {

        //setUbicacionDepartamento(13, 'first');
        document.querySelector('#frmEnvio').addEventListener('submit', (e) => {
            e.preventDefault();
            guardarEnvio(e.target);
        });

    }

    function iniciarSelectsMdlEnvio() {
        window.departamentoSelect = new TomSelect("#departamento", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.provinciaSelect = new TomSelect("#provincia", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.distritoSelect = new TomSelect("#distrito", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.tipoEnvioSelect = new TomSelect("#tipo_envio", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.tipoPagoEnvioSelect = new TomSelect("#tipo_pago_envio", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.empresaEnvioSelect = new TomSelect("#empresa_envio", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.sedeEnvioSelect = new TomSelect("#sede_envio", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.origenVentaSelect = new TomSelect("#origen_venta", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        window.tipoDocDestinatarioSelect = new TomSelect("#tipo_doc_destinatario", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    }

    //========== SELECCIONAR DEPARTAMENTO Y CARGAR PROVINCIAS ===========
    async function setUbicacionDepartamento(dep_id, provincia_id) {

        //====== LIMPIAR SEDES =======
        if (window.sedeEnvioSelect) {
            window.sedeEnvioSelect.clear(); // Limpia selección
            window.sedeEnvioSelect.clearOptions(); // Limpia opciones
        }

        //====== DESELECCIONAR EMPRESAS ENVÍO ======
        if (window.empresaEnvioSelect) {
            window.empresaEnvioSelect.clear(); // Solo limpia selección
        }

        if (dep_id) {
            const departamento_id = dep_id;

            setZona(getZona(departamento_id));

            const provincias = await getProvincias(departamento_id, provincia_id);
            pintarProvincias(provincias, provincia_id);
        } else {
            //======= SI DEPARTAMENTO ES NULL ========
            //======= LIMPIAR PROVINCIAS ======
            if (window.provinciaSelect) {
                window.provinciaSelect.destroy();
            }
            document.querySelector('#provincia').innerHTML = '';
            window.provinciaSelect = new TomSelect('#provincia', {
                options: [], // sin opciones por ahora
                placeholder: 'SELECCIONAR',
                allowEmptyOption: true
            });

            //======== LIMPIAR DISTRITOS ======
            if (window.distritoSelect) {
                window.distritoSelect.destroy();
            }
            document.querySelector('#distrito').innerHTML = '';
            window.distritoSelect = new TomSelect('#distrito', {
                options: [],
                placeholder: 'SELECCIONAR',
                allowEmptyOption: true
            });
        }

    }

    async function setUbicacionProvincia(prov_id, distrito_id) {
        //====== LIMPIAR SEDES =======
        if (window.sedeEnvioSelect) {
            window.sedeEnvioSelect.clear(); // Quitar selección
            window.sedeEnvioSelect.clearOptions(); // Quitar opciones
        }

        //====== DESELECCIONAR EMPRESAS ENVÍO ======
        if (window.empresaEnvioSelect) {
            window.empresaEnvioSelect.clear();
        }

        if (prov_id) {
            const provincia_id = prov_id;
            const distritos = await getDistritos(provincia_id);
            pintarDistritos(distritos, distrito_id); // Esta función también debe estar adaptada a Tom Select
        } else {
            //======= SI PROVINCIA ES NULL ========
            if (window.distritoSelect) {
                window.distritoSelect.clear();
                window.distritoSelect.clearOptions();
            }
        }
    }

    function setMdlDistrito() {
        //====== LIMPIAR SEDES =======
        if (window.sedeEnvioSelect) {
            window.sedeEnvioSelect.clear(); // Quitar selección
            window.sedeEnvioSelect.clearOptions(); // Quitar todas las opciones
        }

        //====== DESELECCIONAR EMPRESAS ENVÍO ======
        if (window.empresaEnvioSelect) {
            window.empresaEnvioSelect.clear();
        }
    }

    function getZona(departamento_id) {
        const departamentos = @json($departamentos);
        const departamento = departamentos.filter((d) => {
            return d.id == departamento_id;
        })

        return departamento[0].zona;
    }

    function setZona(zona_nombre) {
        document.querySelector('#zona').value = zona_nombre;
    }

    //====== CONTROL DE ANIMACIÓN =======
    function mostrarAnimacion() {
        document.querySelector('.content-envio').classList.add('sk__loading');
        document.querySelector('.sk-spinner').classList.remove('hide-envio');
    }

    function ocultarAnimacion() {
        document.querySelector('.content-envio').classList.remove('sk__loading');
        document.querySelector('.sk-spinner').classList.add('hide-envio');
    }


    //======= GET PROVINCIAS ==========
    async function getProvincias(departamento_id) {
        //======= MOSTRAR ANIMACIÓN ======
        mostrarAnimacion();

        try {
            const {
                data
            } = await this.axios.post(route('mantenimiento.ubigeo.provincias'), {
                departamento_id
            });

            const {
                error,
                message,
                provincias
            } = data;

            return provincias;
        } catch (ex) {

        } finally {
            ocultarAnimacion();
        }
    }

    //======== pintar provincias =========
    function pintarProvincias(provincias, provincia_id) {
        if (window.provinciaSelect) {
            window.provinciaSelect.destroy();
        }

        let data = provincias.map(provincia => ({
            value: provincia.id,
            text: provincia.text
        }));

        window.provinciaSelect = new TomSelect("#provincia", {
            options: data,
            placeholder: "SELECCIONAR",
            allowEmptyOption: true,
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        if (provincia_id === 'first' && data.length > 0) {
            provinciaSelect.setValue(data[0].value);
        } else if (provincia_id) {
            provinciaSelect.setValue(provincia_id);
        }

        $(document).trigger("provinciasCargadas");
    }


    //====== PINTAR DISTRITOS ========
    async function getDistritos(provincia_id, distrito_id) {
        try {
            const {
                data
            } = await this.axios.post(route('mantenimiento.ubigeo.distritos'), {
                provincia_id
            });
            const {
                error,
                message,
                distritos
            } = data;
            return distritos;
        } catch (ex) {

        }
    }

    //======== PINTAR DISTRITOS =========
    function pintarDistritos(distritos, distrito_id) {
        if (window.distritoSelect) {
            window.distritoSelect.destroy();
        }

        let data = distritos.map((d) => ({
            value: d.id,
            text: d.text
        }));

        window.distritoSelect = new TomSelect("#distrito", {
            options: data,
            placeholder: "SELECCIONAR",
            allowEmptyOption: true,
            create: false,
            maxItems: 1, // modo select normal
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        if (distrito_id === 'first' && data.length > 0) {
            window.distritoSelect.setValue(data[0].value);
        } else if (distrito_id) {
            window.distritoSelect.setValue(distrito_id);
        }

        $(document).trigger("distritosCargados");
    }

    //========== CARGAR EMPRESAS ENVÍO ========
    async function getEmpresasEnvio() {
        mostrarAnimacion();

        //======= SI SE SELECCIONÓ ALGUN TIPO DE ENVÍO =====
        if (window.tipoEnvioSelect && window.tipoEnvioSelect.getValue()) {

            //==== OBTENIENDO EL TIPO DE ENVÍO SELECCIONADO ====
            const tipo_envio = window.tipoEnvioSelect.getItem(window.tipoEnvioSelect.getValue()).innerText.trim();

            try {
                const {
                    data
                } = await this.axios.get(route("consulta.ajax.getEmpresasEnvio", tipo_envio));

                if (data.success) {
                    pintarEmpresasEnvio(data.empresas_envio);
                    console.log(data);
                } else {
                    toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER EMPRESAS DE ENVÍO');
                }
            } catch (error) {
                toastr.error(error, 'ERROR EN EL SERVIDOR');
            } finally {
                ocultarAnimacion();
            }

        } else {
            //======= SI TIPO ENVÍO ES NULL ========
            //====== LIMPIAR EMPRESAS ENVÍO =======
            if (window.empresaEnvioSelect) {
                window.empresaEnvioSelect.clear();
                window.empresaEnvioSelect.clearOptions();
            }

            //======= LIMPIAR SEDES ENVÍO ======
            if (window.sedeEnvioSelect) {
                window.sedeEnvioSelect.clear();
                window.sedeEnvioSelect.clearOptions();
            }

            ocultarAnimacion();
        }
    }


    //========== PINTAR EMPRESAS ENVÍO =========
    function pintarEmpresasEnvio(empresas_envio) {

        if (window.empresaEnvioSelect) {
            window.empresaEnvioSelect.destroy();
        }

        let data = empresas_envio.map(ee => ({
            value: ee.id,
            text: ee.empresa
        }));

        window.empresaEnvioSelect = new TomSelect("#empresa_envio", {
            options: data,
            placeholder: "SELECCIONAR",
            allowEmptyOption: true,
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        empresaEnvioSelect.clear();

        $(document).trigger("empresasEnvioCargadas");
    }



    //=========== OBTENER SEDES ENVÍO =========
    async function getSedesEnvio() {
        mostrarAnimacion();

        if (!window.empresaEnvioSelect || !window.empresaEnvioSelect.getValue()) {
            //======= SI EMPRESA ENVÍO ES NULL ========
            if (window.sedeEnvioSelect) {
                window.sedeEnvioSelect.clear();
                window.sedeEnvioSelect.clearOptions();
            }
            ocultarAnimacion();
            return;
        }

        try {
            const empresa_envio_id = window.empresaEnvioSelect.getValue();
            let ubigeo = [];

            const departamento = {
                id: $("#departamento").val(),
                nombre: $("#departamento").find('option:selected').text()
            };
            const provincia = {
                id: $("#provincia").val(),
                text: $("#provincia").find('option:selected').text()
            };
            const distrito = {
                id: $("#distrito").val(),
                text: $("#distrito").find('option:selected').text()
            };

            ubigeo.push(departamento, provincia, distrito);
            ubigeo = JSON.stringify(ubigeo);

            const {
                data
            } = await axios.get(route("consulta.ajax.getSedesEnvio", {
                empresa_envio_id,
                ubigeo
            }));

            if (data.success) {
                pintarSedesEnvio(data.sedes_envio);
                console.log(data);
            } else {
                toastr.error(`${data.message} - ${data.exception}`, 'ERROR AL OBTENER SEDES DE ENVÍO');
            }
        } catch (error) {
            toastr.error(error, 'ERROR EN EL SERVIDOR');
        } finally {
            ocultarAnimacion();
        }
    }


    //========== PINTAR SEDES ENVÍO =========
    function pintarSedesEnvio(sedes_envio) {
        if (window.sedeEnvioSelect) {
            window.sedeEnvioSelect.destroy();
        }

        let data = sedes_envio.map(se => ({
            value: se.id,
            text: se.direccion
        }));

        window.sedeEnvioSelect = new TomSelect("#sede_envio", {
            options: data,
            placeholder: "SELECCIONAR",
            allowEmptyOption: true,
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        sedeEnvioSelect.clear();
        document.dispatchEvent(new Event("sedeEnvioCargada"));
    }


    //========= CHECK ENTREGA DOMICILIO =====
    function entregaDomicilio(value_check) {
        console.log(value_check);
        document.querySelector('#direccion_entrega').readOnly = !value_check;
        document.querySelector('#direccion_entrega').required = value_check;

        value_check ? document.querySelector('#lbl_direccion_entrega').classList.add('required') : document
            .querySelector('#lbl_direccion_entrega').classList.remove('required');

    }

    //============ CONSULTAR DOCUMENTO ========
    async function consultarDocumento() {
        try {
            mostrarAnimacion();

            const dni_destinatario = document.querySelector('#nro_doc_destinatario').value;
            if (dni_destinatario.length === 8) {
                await consultarAPI(dni_destinatario);
                ocultarAnimacion();
            } else {
                this.loading = false;
                toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
            }

        } catch (ex) {
            alert("Error en consultarDocumento" + ex);
        }
    }

    async function consultarAPI(dni_destinatario) {
        try {
            let documento = dni_destinatario;
            let url = route('getApidni', {
                dni: documento
            });

            const {
                data
            } = await this.axios.get(url);

            CamposDNI(data);


        } catch (ex) {
            this.loading = false;
            alert("Error en consultarAPI" + ex);
        }
    }

    function CamposDNI(results) {
        const {
            success,
            data
        } = results;
        if (success) {
            document.querySelector('#nombres_destinatario').value = data.nombres + ' ' + data.apellido_paterno + ' ' +
                data.apellido_materno;
        } else {

        }
    }

    function cambiarTipoDocDest(tipo_doc_dest) {
        document.querySelector('#nro_doc_destinatario').value = '';

        //======== 0:DNI  1:CARNET EXT. ======
        if (tipo_doc_dest.length == 0 || tipo_doc_dest == 1) {
            document.querySelector('#btn-consultar-dni').style.display = 'none';
            document.querySelector('#nro_doc_destinatario').maxLength = 20;
        }
        if (tipo_doc_dest == 0 && tipo_doc_dest.length === 1) {
            document.querySelector('#btn-consultar-dni').style.display = 'block';
            document.querySelector('#nro_doc_destinatario').maxLength = 8;
        }



        const destinatario_tipo_doc = $("#tipo_doc_destinatario").find('option:selected').text();
        document.querySelector('.span-tipo-doc-dest').textContent = destinatario_tipo_doc;

    }

    function guardarEnvio(formDespacho) {

        if ($("#departamento").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UN DEPARTAMENTO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#provincia").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UNA PROVINCIA', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#distrito").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UN DISTRITO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#tipo_envio").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UN TIPO DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#tipo_pago_envio").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UN TIPO DE PAGO PARA EL ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#empresa_envio").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UNA EMPRESA DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#sede_envio").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UNA SEDE DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if ($("#sede_envio").find('option:selected').text() === "") {
            toastr.error('SELECCIONE UNA SEDE DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        const tipo_doc_destinatario = $("#tipo_doc_destinatario").find('option:selected').text();
        if (tipo_doc_destinatario === "DNI") {
            if (document.querySelector('#nro_doc_destinatario').value.length !== 8) {
                toastr.error('INGRESE UN NRO DOCUMENTO VÁLIDO PARA EL DESTINATARIO', "ERROR");
                return;
            }
        }
        if (tipo_doc_destinatario.length === 0) {
            toastr.error('SELECCIONE UN TIPO DE DOCUMENTO');
            return;
        }


        if (document.querySelector('#nombres_destinatario').value == 0) {
            toastr.error('DEBE INGRESAR EL NOMBRE DEL DESTINATARIO', "ERROR");
            return;
        }


        if ($("#origen_venta").find('option:selected').text() === "") {
            toastr.warning('ORIGEN VENTA POR DEFECTO WATHSAPP', "ORIGEN VENTA");
            return;
        }

        //====== GUARDANDO DATA DE ENVIO ======
        const departamento = {
            nombre: $("#departamento").find('option:selected').text()
        };
        const provincia = {
            text: $("#provincia").find('option:selected').text()
        };
        const distrito = {
            text: $("#distrito").find('option:selected').text()
        };
        const empresa_envio = {
            id: $("#empresa_envio").val(),
            empresa: $("#empresa_envio").find('option:selected').text()
        };
        const sede_envio = {
            id: $("#sede_envio").val(),
            direccion: $("#sede_envio").find('option:selected').text()
        };
        const tipo_envio = {
            descripcion: $("#tipo_envio").find('option:selected').text()
        };
        const destinatario = {
            nro_documento: document.querySelector('#nro_doc_destinatario').value,
            nombres: document.querySelector('#nombres_destinatario').value,
            tipo_documento: $("#tipo_doc_destinatario").find('option:selected').text()
        };
        const tipo_pago_envio = {
            descripcion: $("#tipo_pago_envio").find('option:selected').text()
        };
        const entrega_domicilio = document.querySelector('#check_entrega_domicilio').checked;
        const direccion_entrega = document.querySelector('#direccion_entrega').value;
        const fecha_envio_propuesta = document.querySelector('#fecha_envio').value;
        const origen_venta = {
            descripcion: $("#origen_venta").find('option:selected').text() == '' ? 'WATHSAPP' : $("#origen_venta")
                .find('option:selected').text()
        };
        const obs_rotulo = document.querySelector('#obs-rotulo').value;
        const obs_despacho = document.querySelector('#obs-despacho').value;

        const form_envio = {
            departamento,
            provincia,
            distrito,
            empresa_envio,
            sede_envio,
            tipo_envio,
            destinatario,
            tipo_pago_envio,
            entrega_domicilio,
            direccion_entrega,
            fecha_envio_propuesta,
            origen_venta,
            obs_rotulo,
            obs_despacho
        };

        document.querySelector('#data_envio').value = JSON.stringify(form_envio);
        toastr.success('DATOS DE ENVÍO GUARDADOS', 'OPERACIÓN COMPLETADA');
        $("#modal_envio").modal("hide");
    }

    function borrarEnvio() {
        document.querySelector('#data_envio').value = JSON.stringify({});
        toastr.error('DATOS DE ENVÍO BORRADOS', 'ENVÍO ELIMINADO');
        $("#modal_envio").modal("hide");
    }
</script>
