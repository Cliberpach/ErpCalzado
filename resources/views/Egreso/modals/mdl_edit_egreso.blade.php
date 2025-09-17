<div class="modal inmodal" id="modal_editar_egreso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title">Egreso</h4>
                <small class="font-bold">Editar Egreso</small>
            </div>
            <div class="modal-body">
                @include('Egreso.forms.form_edit_egreso')
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button form="frm_edit_egreso" type="submit" class="btn btn-success btn-sm btn-submit-egreso"><i
                            class="fa fa-save"></i>
                        GUADAR</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> CANCELAR</button>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <!-- Select2 -->
    <script>
        const parametrosMdlEditEgreso   =   {id:null};

        function eventsMdlEditEgreso() {
            iniciarSelect2MdlEditEgreso();

            document.querySelector('#frm_edit_egreso').addEventListener('submit', (e) => {
                e.preventDefault();
                actualizarEgreso(e.target);
            })

            $('#modal_editar_egreso').on('hidden.bs.modal', function() {
                limpiarMdlEditarEgreso();
            });
        }

        function actualizarEgreso(formActualizarEgreso) {
            Swal.fire({
                title: '¿Desea actualizar el egreso?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'No, cancelar',
                customClass: {
                    confirmButton: 'btn btn-success mr-2',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then(async (result) => {
                if (result.isConfirmed) {

                    limpiarErroresValidacion('msgError');

                    Swal.fire({
                        title: 'Actualizando egreso...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // const validacion    =   validacionActualizarEgreso();
                    // if(!validacion)return;

                    try {
                        const formData = new FormData(formActualizarEgreso);
                        const res = await axios.post(route('Egreso.update',parametrosMdlEditEgreso.id), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtEgresos.ajax.reload();
                            $('#modal_editar_egreso').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        if ('response' in error) {
                            if (error.response.status == 422) {
                                pintarErroresValidacion(error.response.data.errors, 'edit_error');
                                toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                            }
                        } else {
                            toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR EGRESO');
                        }
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'OPERACIÓN CANCELADA',
                        icon: 'info',
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            confirmButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    });
                }
            });
        }

        function validacionActualizarEgreso() {
            const efectivo_venta = $("#efectivo_venta").val();
            const importe_venta = $("#importe_venta").val();
            const cuenta = $("#cuenta").val();
            const cantidad = convertFloat(efectivo_venta) + convertFloat(importe_venta);

            let enviar = true;

            if (cantidad == 0 || cantidad < 0) {
                enviar = false;
                toastr.error('El monto de egreso debe ser mayor a 0.');
            }

            return enviar;

        }

        function iniciarSelect2MdlEditEgreso() {
            window.cuentaEditSelect = new TomSelect("#cuenta_edit", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });

            window.metodoPagoEditSelect = new TomSelect("#modo_pago_edit", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });

            window.cuentaBancariaEditSelect = new TomSelect("#cuenta_bancaria_edit", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });
        }

        $("#tipo_documento_edit").on('change', function(e) {
            var tipoDocumento = $("#tipo_documento_edit option:selected").text()
            if (tipoDocumento == "RECIBO") {
                $("#documento_edit").attr('disabled', true)
            } else {
                $("#documento_edit").attr('disabled', false)
            }
        });

        async function changeModoPagoEdit(b) {
            if (b.value == 1) {
                $("#efectivo_venta_edit").attr('readonly', false)
                $("#importe_venta_edit").attr('readonly', true)
                $("#importe_venta_edit").val(0.00)
                changeEfectivo()
            } else {
                $("#efectivo_venta_edit").attr('readonly', false)
                $("#importe_venta_edit").attr('readonly', false)
            }

            const labelCuenta = document.querySelector(".lbl-cuenta-bancaria-edit");
            const selectCuentaBancaria = document.querySelector('#cuenta_bancaria_edit');
            const inputNroOp = document.querySelector('#nro_operacion_edit');
            const inputFechaOp = document.querySelector('#fecha_operacion_edit');


            if (b.value != 1) { //======= PAGO ELECTRÓNICO =======
                labelCuenta.classList.add("required_field");
                selectCuentaBancaria.required = true;
                inputFechaOp.required = true;
                inputNroOp.required = true;

                const cuentas = await getCuentasBancariasEdit(b.value);
                if (!cuentas) return;
                pintarCuentasBancariasEdit(cuentas);

            } else {
                labelCuenta.classList.remove("required_field");
                selectCuentaBancaria.required = false;
                inputFechaOp.required = false;
                inputNroOp.required = false;
                pintarCuentasBancariasEdit([]);
                window.cuentaBancariaSelect.setValue(null);
                inputFechaOp.value = new Date().toISOString().split("T")[0];;
                inputNroOp.value = '';
            }
        }

        function changeEfectivoEdit() {
            let efectivo = convertFloat($('#efectivo_venta_edit').val());
            let importe = convertFloat($('#importe_venta_edit').val());
            let suma = efectivo + importe;
            $('#monto').val(suma.toFixed(2))
        }

        function changeImporteEdit() {
            let efectivo = convertFloat($('#efectivo_venta_edit').val());
            let importe = convertFloat($('#importe_venta_edit').val());
            let suma = efectivo + importe;
            $('#monto_edit').val(suma.toFixed(2));
        }

        async function pintarCuentasBancariasEdit(cuentas) {
            if (window.cuentaBancariaEditSelect) {
                window.cuentaBancariaEditSelect.destroy();
            }

            window.cuentaBancariaEditSelect = new TomSelect("#cuenta_bancaria_edit", {
                options: cuentas.map(cuenta => ({
                    value: cuenta.cuenta_id,
                    text: cuenta.cuentaLabel
                }))
            });
        }


        async function getCuentasBancariasEdit(metodoPagoId) {
            try {
                toastr.clear();
                mostrarAnimacion();
                const res = await axios.get(route('utilidades.getCuentasPorMetodoPago', metodoPagoId));
                if (res.data.success) {
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                    return res.data.data;
                }
                toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                return null;
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CUENTAS BANCARIAS');
                return null;
            } finally {
                ocultarAnimacion();
            }
        }

        function limpiarMdlEditarEgreso() {
            window.cuentaSelect.setValue(null);
            document.querySelector('#monto').value = 0;
            document.querySelector('#efectivo_venta').value = 0;
            document.querySelector('#importe_venta').value = 0;
            document.querySelector('#documento').value = '';
            document.querySelector('#descripcion').value = '';
            document.querySelector('#nro_operacion').value = '';
            document.querySelector('#fecha_operacion').value = '';
            parametrosMdlEditEgreso.id  =   null;
        }

        async function openMdlEditEgreso(id) {
            parametrosMdlEditEgreso.id  =   id;
            const egreso = await getEgreso(id);
            setEgresoEditar(egreso);
            $("#modal_editar_egreso").modal("show");
        }

        async function getEgreso(id) {
            try {
                toastr.clear();
                mostrarAnimacion();
                const res = await axios.get(route('Egreso.getEgreso', id));
                if (!res.data.success) {
                    toastr.error(res.data.message, 'EROR EN EL SERVIDOR');
                    return null;
                }
                toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
                return res.data.data;
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER EGRESO');
            } finally {
                ocultarAnimacion();
            }
        }

        async function setEgresoEditar(egreso) {

            window.cuentaEditSelect.setValue(egreso.cuenta_id);
            document.querySelector('#tipo_documento_edit').value = 'RECIBO';
            document.querySelector('#monto_edit').value = egreso.monto;
            document.querySelector('#documento_edit').value = egreso.documento;
            document.querySelector('#descripcion_edit').value = egreso.descripcion;
            document.querySelector('#efectivo_venta_edit').value = egreso.efectivo;
            document.querySelector('#importe_venta_edit').value = egreso.importe;
            document.querySelector('#nro_operacion_edit').value = egreso.nro_operacion;
            document.querySelector('#fecha_operacion_edit').value = egreso.fecha_operacion;

            window.metodoPagoEditSelect.setValue(egreso.tipo_pago_id, true);
            await changeModoPagoEdit(document.querySelector('#modo_pago_edit'));
            window.cuentaBancariaEditSelect.setValue(egreso.cuenta_bancaria_id);

        }
    </script>
@endpush
