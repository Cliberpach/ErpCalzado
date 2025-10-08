<div class="modal fade" id="modal_detalle" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">

                <span class="text-uppercase font-weight-bold"> Detalle de Cuenta Cliente</span>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cuenta_cliente_id" id="cuenta_cliente_id">
                @include('ventas.cuentaCliente.forms.form_pagar')
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-success btn-sm" id="btn_guardar_detalle" form="frmDetalle">
                        Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap4.min.css">
    <style>
        .imagen {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        const parametrosMdlPagar = {
            id: null,
            clienteId: null
        };

        let dtDetallePago = null;

        function eventsMdlPagar() {
            document.querySelector('#frmDetalle').addEventListener('submit', (e) => {
                e.preventDefault();
                storePago(e.target);
            })

            $('#modal_detalle').on('hidden.bs.modal', function(e) {
                limpiarMdlPagar();
            });

        }

        async function openMdlPagar(cuentaId) {
            parametrosMdlPagar.id = cuentaId;

            mostrarAnimacion();
            const data = await getCuentaCliente(cuentaId);
            if (!data) return;
            parametrosMdlPagar.clienteId = data.cuenta.cliente_id;
            pintarCuentaCliente(data.cuenta);

            destruirDataTable(dtDetallePago);
            limpiarTabla('dataTables-detalle')
            pintarDetallePago(data.detalle);
            dtDetallePago = iniciarDataTable('dataTables-detalle');

            $("#btn-detalle").attr('href', '/cuentas/cuentaCliente/reporte/' + cuentaId)
            $('#modal_detalle').modal('show');
            ocultarAnimacion();
        }

        async function getCuentaCliente(cuentaId) {
            try {
                const res = await axios.get(route('cuentaCliente.getDatos', {
                    id: cuentaId
                }));
                if (!res.data.success) {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    return null;
                }
                return res.data.data;
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓN OBTENER CUENTA CLIENTE');
                return null;
            }
        }

        function pintarCuentaCliente(cuenta) {
            document.querySelector('#cliente_text').value = cuenta.cliente;
            document.querySelector('#numero').value = cuenta.numero_doc;
            document.querySelector('#monto').value = cuenta.monto;
            document.querySelector('#saldo').value = cuenta.saldo;
            document.querySelector('#estado').value = cuenta.estado;
            document.querySelector('#pedido_nro').value = `PE-${cuenta.pedido_id}`;
        }

        function pintarDetallePago(detalle) {
            const tbody = document.querySelector('#dataTables-detalle tbody');

            detalle.forEach((value) => {
                let acciones = `
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenu${value.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu${value.id}">
                `;

                if (!value.comprobante_id) {
                    acciones += `
                        <a class="dropdown-item" href="javascript:void(0);" onclick="openMdlComprobante(${value.id});">
                            <i class="fas fa-file-invoice"></i> Generar comprobante
                        </a>
                    `;
                }
                if (value.comprobante_id) {
                    const url_open_pdf = route("ventas.documento.comprobante", {
                        id: value.comprobante_id,
                        size: 80
                    });
                    acciones += `
                        <a class="dropdown-item" href="${url_open_pdf}" target="_blank">
                            <i class="fas fa-file-pdf text-danger"></i> Ver comprobante
                        </a>
                    `;
                }

                let botonImagen = value.ruta_imagen ?
                    `<a class="btn btn-primary btn-xs" href="/cuentaCliente/imagen/${value.id}">
                        <i class="fas fa-download"></i>
                    </a>` : '-';

                let fila = `
                    <tr>
                        <td>${value.fecha ?? ''}</td>
                        <td>${value.observacion ?? ''}</td>
                        <td>${value.monto ?? ''}</td>
                        <td>${botonImagen}</td>
                        <td>${value.comprobante_nro??''}</td>
                        <td>${acciones}</td>
                    </tr>
                `;

                tbody.innerHTML += fila;
            });
        }

        function storePago(formPagar) {
            Swal.fire({
                title: '¿Confirmar pago?',
                text: "¿Estás seguro de realizar este pago?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, pagar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        limpiarErroresValidacion('msgError');
                        Swal.fire({
                            title: 'Procesando pago...',
                            text: 'Por favor espera',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const formData = new FormData(formPagar);
                        formData.append('id', parametrosMdlPagar.id);
                        const res = await axios.post(route('cuentaCliente.detallePago'), formData);
                        if (res.data.success) {
                            toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                            dtCuentasCliente.ajax.reload(null, false);
                            $('#modal_detalle').modal('hide');
                        } else {
                            toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        }
                    } catch (error) {
                        if (error.response) {
                            if (error.response.status === 422) {
                                const errors = error.response.data.errors;
                                pintarErroresValidacion(errors, 'error');
                                toastr.error('Errores de validación encontrados.', 'ERROR DE VALIDACIÓN');
                            }
                        } else if (error.request) {
                            toastr.error('No se pudo contactar al servidor. Revisa tu conexión a internet.',
                                'ERROR DE CONEXIÓN');
                        } else {
                            toastr.error(error.message, 'ERROR EN LA PETICIÓN PAGAR CUENTA');
                        }
                    } finally {
                        Swal.close();
                    }
                }
            });
        }

        /* Limpiar imagen */
        $('#limpiar_imagen').click(function() {
            limpiarImagen();
        })

        function limpiarImagen() {
            $('.imagen').attr("src", "{{ asset('img/default.png') }}")
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen').val('')
        }

        $('#imagen').on('change', function() {
            var fileInput = document.getElementById('imagen');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            $imagenPrevisualizacion = document.querySelector(".imagen");

            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                var data = userFile.src;
                $imagenPrevisualizacion.src = data
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen').attr("src", "{{ asset('img/default.png') }}")
            }
        });

        async function changeModoPago(b) {

            //======= EFECTIVO ========
            if (b.value == 1) {
                $("#efectivo_venta").attr('readonly', false)
                $("#importe_venta").attr('readonly', true)
                $("#importe_venta").val(0.00)
                changeEfectivo()
            } else { //======= OTRO MÉT PAGO ========
                $("#efectivo_venta").attr('readonly', false)
                $("#importe_venta").attr('readonly', false)
                $("#efectivo_venta").val(0.00)
            }

            mostrarAnimacion();
            toastr.clear();
            const cuentas = await getCuentasPorMetodoPago(b.value);
            if (!cuentas) return;
            pintarCuentas(cuentas);
            ocultarAnimacion();
        }

        function pintarCuentas(cuentas) {
            if (window.cuentaSelect) {
                window.cuentaSelect.destroy();
            }
            window.cuentaSelect = new TomSelect("#cuenta", {
                options: cuentas.map(c => ({
                    value: c.cuenta_id,
                    text: c.cuentaLabel
                })),
                placeholder: "SELECCIONAR",
                allowEmptyOption: true,
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                plugins: ['clear_button']
            });
        }

        function changeEfectivo() {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#cantidad').val(suma.toFixed(2))
        }

        function changeImporte() {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#cantidad').val(suma.toFixed(2));
        }

        function tipoPago(tipoPago) {
            const tipo_pago = tipoPago.value;
            if (tipo_pago == "TODO") {
                const saldo = document.querySelector('#saldo').value;
                const modoPagoId = document.querySelector('#modo_pago').value;
                if (modoPagoId == 1) {
                    document.querySelector('#efectivo_venta').value = saldo;
                } else {
                    document.querySelector('#importe_venta').value = saldo;
                }
                document.querySelector('#cantidad').value = saldo;
            }
            if (tipo_pago == "A CUENTA") {
                document.querySelector('#efectivo_venta').value = 0;
                document.querySelector('#importe_venta').value = 0;
                document.querySelector('#cantidad').value = 0;
            }
        }

        function iniciarSelectsMdlPagar() {
            window.pagoSelect = new TomSelect("#pago", {
                placeholder: "SELECCIONAR",
                allowEmptyOption: true,
                create: false,
                maxOptions: null,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            window.modoPagoSelect = new TomSelect("#modo_pago", {
                placeholder: "SELECCIONAR",
                allowEmptyOption: false,
                create: false,
                maxOptions: null,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            window.cuentaSelect = new TomSelect("#cuenta", {
                placeholder: "SELECCIONAR",
                allowEmptyOption: false,
                create: false,
                maxOptions: null,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });

            // window.mododDespachoSelect = new TomSelect("#modo_despacho", {
            //     placeholder: "SELECCIONAR",
            //     allowEmptyOption: false,
            //     create: false,
            //     maxOptions: null,
            //     sortField: {
            //         field: "text",
            //         direction: "asc"
            //     }
            // });
        }

        async function getCuentasPorMetodoPago(metodoPagoId) {
            try {
                const res = await axios.get(route('utilidades.getCuentasPorMetodoPago', {
                    metodo_pago: metodoPagoId
                }));
                if (!res.data.success) {
                    toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    return null;
                }
                toastr.info(res.data.message, 'OPERACIÓN COMPLETADA');
                return res.data.data;
            } catch (error) {
                toastr.error(error, 'ERROR EN LA PETICIÓ OBTENER CUENTAS POR MÉTODO DE PAGO');
                return null;
            }
        }

        function limpiarMdlPagar() {
            document.querySelector('#cliente').value = '';
            document.querySelector('#numero').value = '';
            document.querySelector('#monto').value = '';
            document.querySelector('#saldo').value = '';
            document.querySelector('#estado').value = '';
            document.querySelector('#observacion').value = '';
            document.querySelector('#nro_operacion').value = '';
            window.pagoSelect.setValue("A CUENTA");
            window.modoPagoSelect.setValue(3);
            limpiarImagen();
        }
    </script>
@endpush
