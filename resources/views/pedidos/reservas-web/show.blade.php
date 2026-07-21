@extends('layout')

@section('pedidos-active', 'active')
@section('reservas-web-active', 'active')

@section('bread-module', 'Pedidos')
@section('bread-submodule', 'Reservas Web')
@section('hero-title', 'Detalle de Reserva Web')
@section('hero-subtitle', 'Revisa el pedido, cubre stock si falta, y confirma o anula')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <a href="{{ route('pedidos.reservas_web.index') }}" class="btn btn-default btn-sm mb-3">
                    <i class="fa fa-arrow-left"></i> Volver al listado
                </a>

                <div class="ibox">
                    <div class="ibox-content" id="rw-content">
                        <p class="text-muted"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const RESERVA_ID = {{ (int) $id }};
        let currentReserva = null;
        let currentDocumentoNumero = null;

        document.addEventListener('DOMContentLoaded', cargarReserva);

        async function cargarReserva() {
            try {
                const res = await axios.get(route('pedidos.reservas_web.getDetalle', RESERVA_ID));
                if (!res.data.success) {
                    document.getElementById('rw-content').innerHTML =
                        `<div class="alert alert-danger">${res.data.message}</div>`;
                    return;
                }
                render(res.data.data);
            } catch (e) {
                document.getElementById('rw-content').innerHTML =
                    `<div class="alert alert-danger">${e.response?.data?.message || e.message}</div>`;
            }
        }

        // Sin pasarela real todavía — solo referencia pa verificar a mano.
        // Nunca hay número completo de tarjeta ni CVV guardados.
        function medioPagoTexto(r) {
            if (!r.metodo_pago) return '<span class="text-muted">-</span>';
            if (r.metodo_pago === 'card') {
                const partes = [];
                if (r.pago_titular) partes.push(r.pago_titular);
                if (r.pago_tarjeta_last4) partes.push(`****${r.pago_tarjeta_last4}`);
                if (r.pago_banco) partes.push(r.pago_banco);
                if (r.pago_cuotas) partes.push(`${r.pago_cuotas} cuota(s)`);
                return `Tarjeta — ${partes.join(' · ') || 'sin datos'}`;
            }
            if (r.metodo_pago === 'yape') {
                return `Yape/Plin${r.pago_referencia ? ' — N° operación: ' + r.pago_referencia : ' (sin N° de operación)'}`;
            }
            return r.metodo_pago;
        }

        function render({ reserva, detalle, sede_recojo_nombre, documento_numero, despacho_estado, envio_venta }) {
            currentReserva = reserva;
            currentDocumentoNumero = documento_numero;
            const tienePendiente = detalle.some(d => d.cantidad_pendiente > 0);
            const estadoClase = reserva.estado === 'PENDIENTE' ? 'warning' : (reserva.estado === 'CONFIRMADO' ? 'success' : 'danger');

            const filas = detalle.map(d => {
                const origenes = [];
                if (d.origen_directo.cantidad > 0) {
                    origenes.push(`<div><i class="fa fa-check text-success"></i> ${d.origen_directo.cantidad} ya disponible en <strong>${d.origen_directo.sede ?? '-'}</strong></div>`);
                }
                d.origen_traslados.forEach(t => {
                    const estadoBadge = t.estado === 'RECIBIDO'
                        ? '<span class="badge badge-success">RECIBIDO</span>'
                        : t.estado === 'ANULADO'
                            ? '<span class="badge badge-danger">ANULADO</span>'
                            : '<span class="badge badge-warning">EN TRÁNSITO</span>';
                    origenes.push(`<div><i class="fa fa-truck"></i> ${t.cantidad} desde <strong>${t.sede}</strong> (${t.codigo}) ${estadoBadge}</div>`);
                });
                if (d.cantidad_pendiente > 0 && d.origen_traslados.length === 0) {
                    origenes.push(`<div class="text-danger"><i class="fa fa-exclamation-triangle"></i> ${d.cantidad_pendiente} sin cubrir todavía — sin traslado creado</div>`);
                }

                return `
                    <tr class="${d.cantidad_pendiente > 0 ? 'table-danger' : ''}">
                        <td>${d.producto_nombre ?? '-'}</td>
                        <td>${d.color_nombre}</td>
                        <td>${d.talla_nombre}</td>
                        <td>${d.cantidad}</td>
                        <td style="font-size:0.8rem;">${origenes.join('') || '-'}</td>
                        <td>S/ ${Number(d.precio_venta_1).toFixed(2)}</td>
                    </tr>`;
            }).join('');

            let acciones = `<span class="text-muted">Reserva ya resuelta.</span>`;
            if (reserva.estado === 'CONFIRMADO' && reserva.documento_id) {
                acciones = `
                    <button class="btn btn-info" onclick="reenviarComprobante()"><i class="fa fa-envelope"></i> Reenviar comprobante</button>
                    ${despacho_estado === 'FALTA_DESPACHO' ? `
                        <button class="btn btn-warning" onclick="irAGenerarDespacho()"><i class="fa fa-truck"></i> Generar despacho</button>` : ''}
                    <button class="btn btn-danger" onclick="eliminarReserva()"><i class="fa fa-trash"></i> Eliminar</button>`;
            }
            if (reserva.estado === 'PENDIENTE') {
                acciones = `
                    ${tienePendiente ? `
                        <div class="alert alert-warning" style="font-size:0.85rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Falta cubrir stock en alguna línea (ver columna "Origen del stock" arriba, en rojo).
                            Usa <strong>Cubrir stock</strong> para crear un traslado desde otra sede.
                            <strong>Confirmar</strong> se habilita solo cuando ya no falte nada.
                        </div>
                        <button class="btn btn-warning" onclick="cubrirStock()"><i class="fa fa-truck"></i> Cubrir stock</button>
                        <button class="btn btn-success" disabled title="Cubre el stock faltante antes de confirmar">
                            <i class="fa fa-check"></i> Confirmar
                        </button>` : `
                        <button class="btn btn-success" onclick="confirmarReserva()"><i class="fa fa-check"></i> Confirmar</button>`}
                    <button class="btn btn-danger" onclick="anularReserva()"><i class="fa fa-times"></i> Anular</button>`;
            }

            document.getElementById('rw-content').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Pedido:</strong> ${reserva.codigo_pedido_ecommerce}</p>
                        <p><strong>Cliente:</strong> ${reserva.cliente_nombre} (${reserva.cliente_email})</p>
                        <p><strong>Teléfono:</strong> ${reserva.cliente_telefono ?? '-'}</p>
                        <p><strong>Dirección:</strong> ${reserva.cliente_direccion ?? '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Sede de recojo:</strong> ${sede_recojo_nombre ?? '<span class="text-muted">Envío a domicilio</span>'}</p>
                        <p><strong>Estado:</strong> <span class="badge badge-${estadoClase}">${reserva.estado}</span>${reserva.motivo_anulacion ? ' — ' + reserva.motivo_anulacion : ''}</p>
                        <p><strong>Estado envío:</strong> ${reserva.estado_envio ? `<span class="badge badge-info">${reserva.estado_envio}</span>` : '<span class="text-muted">Aún no despachado</span>'}</p>
                        <p><strong>Fecha:</strong> ${reserva.fecha_reserva ? new Date(reserva.fecha_reserva).toLocaleString('es-PE') : '-'}</p>
                        <p><strong>Medio de pago:</strong> ${medioPagoTexto(reserva)}</p>
                        <p><strong>Comprobante:</strong> ${documento_numero ? `<span class="badge badge-success">${documento_numero}</span>` : '<span class="text-muted">Aún no generado</span>'}</p>
                        <p><strong>Despacho:</strong> ${
                            despacho_estado === 'FALTA_DESPACHO'
                                ? '<span class="badge badge-danger">Falta generar</span>'
                                : despacho_estado === 'ESTANCADO'
                                    ? `<span class="badge badge-warning" title="Generado pero sin marcar enviado/entregado hace más de 3 días">Estancado (${envio_venta.estado})</span>`
                                    : envio_venta
                                        ? `<span class="badge badge-info">${envio_venta.estado}${envio_venta.empresa_envio_nombre ? ' — ' + envio_venta.empresa_envio_nombre : ''}</span>`
                                        : '<span class="text-muted">-</span>'
                        }</p>
                    </div>
                </div>
                <table class="table table-bordered table-sm" style="margin-top:10px;">
                    <thead>
                        <tr><th>Producto</th><th>Color</th><th>Talla</th><th>Cant.</th><th>Origen del stock</th><th>Precio</th></tr>
                    </thead>
                    <tbody>${filas}</tbody>
                </table>
                <p class="text-right"><strong>Total: S/ ${Number(reserva.total).toFixed(2)}</strong></p>
                <hr>
                <div class="d-flex" style="gap:8px;">${acciones}</div>
            `;
        }

        // Fase 2 de docs/PLANIFICATIONS/2026-07-17-flujo-envio-domicilio.md:
        // no existe un deep-link real hacia el modal de despacho (vive en
        // Ventas > Documentos, componente Vue ModalEnvio.vue, se abre por
        // fila con un botón — verificado que no hay ?documento_id=X que lo
        // auto-abra). Copiamos el número de documento al portapapeles para
        // que buscarlo ahí sea rápido, en vez de fingir un prellenado que
        // hoy no existe.
        function irAGenerarDespacho() {
            if (currentDocumentoNumero) {
                navigator.clipboard?.writeText(currentDocumentoNumero).catch(() => {});
                toastr.info(`Documento ${currentDocumentoNumero} copiado — búscalo en la lista y usa su botón "Despacho".`, 'GENERAR DESPACHO', { timeOut: 8000 });
            } else {
                toastr.warning('Esta reserva no tiene documento generado todavía.', 'AVISO');
            }
            window.open('{{ route('ventas.documento.index') }}', '_blank');
        }

        async function eliminarReserva() {
            const { value: texto, isConfirmed } = await Swal.fire({
                title: '¿Eliminar esta reserva confirmada?',
                html: `Esto borra <strong>permanentemente</strong> la venta (comprobante ${currentReserva.comprobante_numero ?? ''}), el despacho asociado, y devuelve el stock. No se puede deshacer.<br><br>Escribe <strong>ELIMINAR</strong> para confirmar:`,
                input: 'text',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar todo',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                inputValidator: (value) => value !== 'ELIMINAR' && 'Escribe ELIMINAR (en mayúsculas) para confirmar',
            });
            if (!isConfirmed || texto !== 'ELIMINAR') return;

            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const res = await axios.post(route('pedidos.reservas_web.eliminar', RESERVA_ID));
                Swal.close();
                if (res.data.success) {
                    toastr.success(res.data.message, 'LISTO');
                    window.location.href = '{{ route('pedidos.reservas_web.index') }}';
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                Swal.close();
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }

        async function confirmarReserva() {
            const result = await Swal.fire({
                title: '¿Confirmar reserva?',
                text: 'Se asume que el pago ya llegó. Esta acción pasa el pedido al flujo de despachos.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1ab394',
            });
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Confirmando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const res = await axios.post(route('pedidos.reservas_web.confirmar', RESERVA_ID));
                Swal.close();
                if (res.data.success) {
                    toastr.success(res.data.message, 'LISTO');
                    window.location.href = '{{ route('pedidos.reservas_web.index') }}';
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                Swal.close();
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }

        async function reenviarComprobante() {
            const { value: correo, isConfirmed } = await Swal.fire({
                title: 'Reenviar comprobante',
                input: 'email',
                inputLabel: 'Correo destino',
                inputValue: currentReserva.cliente_email,
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1ab394',
                inputValidator: (value) => !value && 'Ingresa un correo',
            });
            if (!isConfirmed) return;

            Swal.fire({
                title: 'Enviando...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            try {
                const res = await axios.post(route('ventas.documento.envio'), {
                    id: currentReserva.documento_id,
                    correo,
                });
                Swal.close();
                if (res.data.success) {
                    toastr.success(res.data.message, 'LISTO');
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                Swal.close();
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }

        async function anularReserva() {
            const { value: motivo, isConfirmed } = await Swal.fire({
                title: '¿Anular reserva?',
                input: 'text',
                inputLabel: 'Motivo (opcional)',
                inputPlaceholder: 'Ej. cliente no pagó, se arrepintió...',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, anular y devolver stock',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
            });
            if (!isConfirmed) return;

            try {
                const res = await axios.post(route('pedidos.reservas_web.anular', RESERVA_ID), { motivo });
                if (res.data.success) {
                    toastr.success(res.data.message, 'LISTO');
                    window.location.href = '{{ route('pedidos.reservas_web.index') }}';
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }

        // Recojo en tienda (plan-recojo-tienda.md §2.3): la sede origen la
        // elige el staff a mano, sin sugerencia automática del sistema.
        async function cubrirStock() {
            try {
                const res = await axios.get(route('pedidos.reservas_web.getAlmacenesOrigen', RESERVA_ID));
                const almacenes = res.data.data;

                if (!almacenes.length) {
                    toastr.error('No hay otro almacén PRINCIPAL activo disponible.', 'ERROR');
                    return;
                }

                const options = almacenes.map(a => `<option value="${a.id}">${a.nombre}</option>`).join('');

                const { value: almacenOrigenId, isConfirmed } = await Swal.fire({
                    title: 'Cubrir stock faltante',
                    html: `
                        <p class="text-left" style="font-size:0.85rem;">
                            Se creará un traslado de la sede que elijas hacia la sede del
                            cliente, por lo que falte de cada línea. Se recibe como
                            cualquier traslado normal (Almacenes &gt; Traslados).
                        </p>
                        <select id="swal-almacen-origen" class="swal2-input" style="width:80%;">
                            ${options}
                        </select>`,
                    showCancelButton: true,
                    confirmButtonText: 'Crear traslado',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => document.getElementById('swal-almacen-origen').value,
                });
                if (!isConfirmed) return;

                const store = await axios.post(route('pedidos.reservas_web.cubrirStock', RESERVA_ID), {
                    almacen_origen_id: almacenOrigenId,
                });

                if (store.data.success) {
                    toastr.success(store.data.message, 'LISTO');
                    cargarReserva();
                } else {
                    toastr.error(store.data.message, 'ERROR');
                }
            } catch (e) {
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }
    </script>
@endpush
