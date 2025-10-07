<!-- Modal -->
<div class="modal fade" id="modalComprobante" tabindex="-1" role="dialog" aria-labelledby="modalComprobanteLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalComprobanteLabel">
                    <i class="fas fa-file-invoice"></i> Generar comprobante de pago cuenta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <form id="formComprobante">

                    <div class="form-group">
                        <label for="cliente" class="required" style="font-weight: bold;">Cliente</label>
                        <select id="cliente" name="cliente" required>
                            <option></option>
                        </select>
                        <span style="font-weight: bold;color:red;" class="cliente_error msgError"></span>
                    </div>

                    <div class="form-group">
                        <label for="tipo_comprobante" style="font-weight: bold;">Tipo de comprobante</label>
                        <select class="form-control" id="tipo_comprobante" name="tipo_comprobante">
                            @foreach ($tipo_comprobantes as $tipo_comprobante)
                                <option value="{{ $tipo_comprobante->id }}">{{ $tipo_comprobante->nombre }}</option>
                            @endforeach
                        </select>
                        <span style="font-weight: bold;color:red;" class="tipo_comprobante_error msgError"></span>
                    </div>
                    <div class="form-group">
                        <label for="observacion" style="font-weight: bold;">Observación</label>
                        <textarea maxlength="200" class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                        <span style="font-weight: bold;color:red;" class="observacion_error msgError"></span>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="submit" form="formComprobante" class="btn btn-success">
                    <i class="fas fa-check"></i> Generar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const parametrosMdlComprobante = {
        pagoId: null,
        ruta: null
    };

    function eventsGenerarComprobante() {
        document.querySelector('#formComprobante').addEventListener('submit', (e) => {
            e.preventDefault();
            generarComprobante(e.target);
        })
    }

    function openMdlComprobante(pagoId) {
        if (!pagoId) {
            toastr.error('FALTA EL ID DEL PAGO');
            return;
        }
        parametrosMdlComprobante.pagoId = pagoId;
        setClienteDefault(parametrosMdlPagar.clienteId);
        $('#modalComprobante').modal('show');
    }

    async function setClienteDefault(clienteId) {
        try {
            const response = await fetch(`{{ route('utilidades.getClientes') }}?cliente_id=${clienteId}`);
            const data = await response.json();

            if (data.success && data.clientes.length > 0) {
                const cliente = data.clientes[0];

                window.clienteSelect.addOption({
                    id: cliente.id,
                    text: cliente.descripcion
                });

                window.clienteSelect.setValue(cliente.id);

                document.getElementById('cliente_text').value = cliente.descripcion;
            }
        } catch (error) {
            toastr.error("Error al cargar cliente inicial", "ERROR");
        }
    }

    function generarComprobante(formGenerarComprobante) {
        Swal.fire({
            title: '¿Desea generar el comprobante?',
            text: "Este comprobante será emitido para el pago de la cuenta seleccionada.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Sí, generar',
            cancelButtonText: '<i class="fas fa-times"></i> No, cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                try {

                    Swal.fire({
                        title: 'Generando comprobante...',
                        html: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(formGenerarComprobante);
                    formData.append('pago_id', parametrosMdlComprobante.pagoId);

                    const res = await axios.post(route('cuentaCliente.generarComprobantePago'), formData);
                    if (res.data.success) {
                        toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                        $('#modalComprobante').modal('hide');
                        window.open(res.data.url_pdf, 'Comprobante SISCOM',
                            'location=1, status=1, scrollbars=1,width=900, height=600');

                        mostrarAnimacion();
                        const data = await getCuentaCliente(parametrosMdlPagar.id);
                        if (!data) return;
                        parametrosMdlPagar.clienteId = data.cuenta.cliente_id;
                        pintarCuentaCliente(data.cuenta);

                        destruirDataTable(dtDetallePago);
                        limpiarTabla('dataTables-detalle')
                        pintarDetallePago(data.detalle);
                        dtDetallePago = iniciarDataTable('dataTables-detalle');
                        ocultarAnimacion();

                    } else {
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                    }
                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN GENERAR COMPROBANTE PAGO');
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // 👉 acción cuando cancela
                Swal.fire({
                    title: 'Operación cancelada',
                    text: 'No se generó ningún comprobante.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#007bff'
                });
            }
        });
    }
</script>
