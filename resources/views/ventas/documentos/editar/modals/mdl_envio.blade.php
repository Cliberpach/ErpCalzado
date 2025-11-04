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
                @include('ventas.documentos.editar.forms.form_envio')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="frmEnvio" class="btn btn-success btn-guardar-envio">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function eventsModalEnvio() {

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
            },
            plugins: ['clear_button']
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

        try {
            //======= MOSTRAR ANIMACIÓN ======
            mostrarAnimacion();
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
            mostrarAnimacion();
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
        } catch (error) {
            toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER DISTRITOS MDL ENVÍO');
        } finally {
            ocultarAnimacion();
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

            if (window.tipoEnvioSelect.getValue() == 189) {
                document.querySelector('#sede_envio').required = false;
            } else {
                document.querySelector('#sede_envio').required = true;
            }

            //==== OBTENIENDO EL TIPO DE ENVÍO SELECCIONADO ====
            const tipo_envio = document.querySelector('#tipo_envio').value;

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
            },
            plugins: ['clear_button']
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

            const departamento = $("#departamento").val();
            const provincia = $("#provincia").val();
            const distrito = $("#distrito").val();

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
            let url = route('utilidades.consultarDocumento', {
                tipo_doc: 6,
                nro_doc: documento
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
            document.querySelector('#nombres_destinatario').value = data.nombre_completo;
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

        if (!window.departamentoSelect.getValue()) {
            toastr.error('SELECCIONE UN DEPARTAMENTO', "CAMPO OBLIGATORIO");
            return;
        }

        if (!window.provinciaSelect.getValue()) {
            toastr.error('SELECCIONE UNA PROVINCIA', "CAMPO OBLIGATORIO");
            return;
        }

        if (!window.distritoSelect.getValue()) {
            toastr.error('SELECCIONE UN DISTRITO', "CAMPO OBLIGATORIO");
            return;
        }
        if (!window.tipoEnvioSelect.getValue()) {
            toastr.error('SELECCIONE UN TIPO DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if (!window.tipoPagoEnvioSelect.getValue()) {
            toastr.error('SELECCIONE UN TIPO DE PAGO PARA EL ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if (!window.empresaEnvioSelect.getValue()) {
            toastr.error('SELECCIONE UNA EMPRESA DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        if (!window.sedeEnvioSelect.getValue() && window.tipoEnvioSelect.getValue() != 189) {
            toastr.error('SELECCIONE UNA SEDE DE ENVÍO', "CAMPO OBLIGATORIO");
            return;
        }

        //======= TIPO DOC DESTINATARIO ========
        const tipo_doc_destinatario = window.tipoDocDestinatarioSelect.getValue();
        if (tipo_doc_destinatario == 6) {
            if (document.querySelector('#nro_doc_destinatario').value.length !== 8) {
                toastr.error('INGRESE UN NRO DOCUMENTO VÁLIDO PARA EL DESTINATARIO', "ERROR");
                return;
            }
        }
        if (!tipo_doc_destinatario) {
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
        const departamento = document.querySelector('#departamento').value;;
        const provincia = document.querySelector('#provincia').value;
        const distrito = document.querySelector('#distrito').value;
        const empresa_envio = document.querySelector('#empresa_envio').value;
        const sede_envio = document.querySelector('#sede_envio').value;
        const tipo_envio = document.querySelector('#tipo_envio').value;
        const destinatario = {
            nro_documento: document.querySelector('#nro_doc_destinatario').value,
            nombres: document.querySelector('#nombres_destinatario').value,
            tipo_documento: $("#tipo_doc_destinatario").find('option:selected').text()
        };
        const tipo_pago_envio = document.querySelector('#tipo_pago_envio').value;
        const entrega_domicilio = document.querySelector('#check_entrega_domicilio').checked;
        const direccion_entrega = document.querySelector('#direccion_entrega').value;
        const fecha_envio_propuesta = document.querySelector('#fecha_envio').value;
        const origen_venta = document.querySelector('#origen_venta').value;
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

        document.querySelector('#monto-envio').classList.add('btn-success');
        document.querySelector('#monto-envio').classList.remove('btn-light');

        $("#modal_envio").modal("hide");
    }

    function limpiarMdlEnvio() {
        window.departamentoSelect.setValue(15);
        window.tipoEnvioSelect.setValue(187)
        window.tipoPagoEnvioSelect.setValue(196);
        window.empresaEnvioSelect.setValue(null);
        window.sedeEnvioSelect.setValue(null);
        document.querySelector('#check_entrega_domicilio').checked = false;
        document.querySelector('#direccion_entrega').value = '';
        document.querySelector('#direccion_entrega').readOnly = true;
    }

    function borrarEnvio() {
        limpiarMdlEnvio();
        document.querySelector('#data_envio').value = null;
        toastr.error('DATOS DE ENVÍO BORRADOS', 'ENVÍO ELIMINADO');

        document.querySelector('#monto-envio').classList.add('btn-light');
        document.querySelector('#monto-envio').classList.remove('btn-success');

        $("#modal_envio").modal("hide");
    }

    function desactivarEventosSelectsMdlEnvio() {
        document.querySelector('#departamento').onchange = null;
        document.querySelector('#provincia').onchange = null;
        document.querySelector('#distrito').onchange = null;
        document.querySelector('#tipo_envio').onchange = null;
        document.querySelector('#empresa_envio').onchange = null;
    }

    function activarEventosSelectsMdlEnvio() {
        document.querySelector('#departamento').onchange = function(e) {
            setUbicacionDepartamento(e.target.value, 'first');
        };

        document.querySelector('#provincia').onchange = function(e) {
            setUbicacionProvincia(e.target.value, 'first');
        };

        document.querySelector('#distrito').onchange = function(e) {
            setMdlDistrito();
        };

        document.querySelector('#tipo_envio').onchange = function(e) {
            getEmpresasEnvio();
        };

        document.querySelector('#empresa_envio').onchange = function(e) {
            getSedesEnvio();
        };
    }
</script>
