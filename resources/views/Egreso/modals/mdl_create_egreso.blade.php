<div class="modal inmodal" id="modal_crear_egreso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title">Egreso</h4>
                <small class="font-bold">Nuevo Egreso</small>
            </div>
            <div class="modal-body">
                @include('Egreso.forms.form_create_egreso')
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button form="frm_egreso_create" type="submit" class="btn btn-success btn-sm btn-submit-egreso"><i
                            class="fa fa-save"></i>
                        GUADAR</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> CANCELAR</button>
                </div>
            </div>

        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Select2 -->
    <script>
        function eventsMdlCreateEgreso() {
            iniciarSelect2MdlCreateEgreso();
            setDefaultMdlCrearEgreso();

            document.querySelector('#frm_egreso_create').addEventListener('submit', (e) => {
                e.preventDefault();
                registrarEgreso(e.target);
            })

            $('#modal_crear_egreso').on('hidden.bs.modal', function() {
                limpiarMdlCrearEgreso();
            });
        }

        function registrarEgreso(formCreateEgreso) {
            Swal.fire({
                title: '¿Desea registrar el egreso?',
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
                        title: 'Registrando egreso...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // const validacion    =   validacionRegistrarEgreso();
                    // if(!validacion)return;

                    try {
                        const formData = new FormData(formCreateEgreso);
                        const res = await axios.post(route('Egreso.store'), formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtEgresos.ajax.reload();
                            $('#modal_crear_egreso').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        if ('response' in error) {
                            if (error.response.status == 422) {
                                pintarErroresValidacion(error.response.data.errors, 'error');
                                toastr.error(error.response.data.message, 'ERROR EN EL SERVIDOR');
                            }
                        } else {
                            toastr.error(error, 'ERROR EN LA PETICIÓN REGISTRAR EGRESO');
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

        function validacionRegistrarEgreso() {
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

        function iniciarSelect2MdlCreateEgreso() {
            window.cuentaSelect = new TomSelect("#cuenta", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });

            window.metodoPagoSelect = new TomSelect("#modo_pago", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });

            window.cuentaBancariaSelect = new TomSelect("#cuenta_bancaria", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "SELECCIONAR"
            });
        }

        $("#tipo_documento").on('change', function(e) {
            var tipoDocumento = $("#tipo_documento option:selected").text()
            if (tipoDocumento == "RECIBO") {
                $("#documento").attr('disabled', true)
            } else {
                $("#documento").attr('disabled', false)
            }
        });
        /*$("#frm_egreso_create").on('submit', function(e) {
            e.preventDefault();
            document.querySelector('.btn-submit-egreso').disabled = true;

            var efectivo_venta = $("#efectivo_venta").val();
            var importe_venta = $("#importe_venta").val();
            var cuenta = $("#cuenta").val();
            var cantidad = convertFloat(efectivo_venta) + convertFloat(importe_venta);
            var modo_pago = $("#modo_pago").val();

            let enviar = true;
            if (cantidad.length == 0 || cuenta.length == 0 || modo_pago.length == 0) {
                enviar = false;
                toastr.error('Ingrese todos los datos');
            }

            if (cantidad == 0 || cantidad < 0) {
                enviar = false;
                toastr.error('El monto de egreso debe ser mayor a 0.');
            }

            if (enviar) {
                axios.get("{{ route('Caja.movimiento.verificarestado') }}").then((value) => {
                    let data = value.data
                    if (!data.success) {
                        toastr.error(data.mensaje);
                    } else {
                        $('.btn-submit-egreso').attr('disabled', true);
                        $('.btn-submit-egreso').html('Cargando <span class="loading bullet"></span> ');
                        this.submit();
                    }
                })
            } else {
                document.querySelector('.btn-submit-egreso').disabled = false;
            }
        })*/

        async function changeModoPago(b) {
            if (b.value == 1) {
                $("#efectivo_venta").attr('readonly', false)
                $("#importe_venta").attr('readonly', true)
                $("#importe_venta").val(0.00)
                changeEfectivo()
            } else {
                $("#efectivo_venta").attr('readonly', false)
                $("#importe_venta").attr('readonly', false)
            }

            const labelCuenta = document.querySelector(".lbl-cuenta-bancaria");
            const selectCuentaBancaria = document.querySelector('#cuenta_bancaria');
            const inputNroOp = document.querySelector('#nro_operacion');
            const inputFechaOp = document.querySelector('#fecha_operacion');


            if (b.value != 1) { //======= PAGO ELECTRÓNICO =======
                labelCuenta.classList.add("required_field");
                selectCuentaBancaria.required = true;
                inputFechaOp.required = true;
                inputNroOp.required = true;

                const cuentas = await getCuentasBancarias(b.value);
                if (!cuentas) return;
                pintarCuentasBancarias(cuentas);

            } else {
                labelCuenta.classList.remove("required_field");
                selectCuentaBancaria.required = false;
                inputFechaOp.required = false;
                inputNroOp.required = false;
                pintarCuentasBancarias([]);
                window.cuentaBancariaSelect.setValue(null);
                inputFechaOp.value = new Date().toISOString().split("T")[0];;
                inputNroOp.value = '';
            }
        }

        function changeEfectivo() {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#monto').val(suma.toFixed(2))
        }

        function changeImporte() {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#monto').val(suma.toFixed(2));
        }

        async function pintarCuentasBancarias(cuentas) {
            if (window.cuentaBancariaSelect) {
                window.cuentaBancariaSelect.destroy();
            }

            window.cuentaBancariaSelect = new TomSelect("#cuenta_bancaria", {
                options: cuentas.map(cuenta => ({
                    value: cuenta.cuenta_id,
                    text: cuenta.cuentaLabel
                }))
            });
        }


        async function getCuentasBancarias(metodoPagoId) {
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

        function limpiarMdlCrearEgreso() {
            window.cuentaSelect.setValue(null);
            document.querySelector('#monto').value = 0;
            document.querySelector('#efectivo_venta').value = 0;
            document.querySelector('#importe_venta').value = 0;
            document.querySelector('#documento').value = '';
            document.querySelector('#descripcion').value = '';
            document.querySelector('#nro_operacion').value = '';
            document.querySelector('#fecha_operacion').value = '';
            window.metodoPagoSelect.setValue(3);
        }

        function setDefaultMdlCrearEgreso() {
            window.metodoPagoSelect.setValue(3);
        }

        function openMdlCreateEgreso() {
            $("#modal_crear_egreso").modal("show");
        }
    </script>
@endpush
