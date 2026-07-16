@extends('layout')

@section('pedidos-active', 'active')
@section('reservas-web-active', 'active')

@section('bread-module', 'Pedidos')
@section('bread-submodule', 'Reservas Web')
@section('hero-title', 'Reservas Web')
@section('hero-subtitle', 'Pedidos confirmados en ecommerceMerris, pendientes de validar pago')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="d-flex align-items-center mb-3" style="gap:10px;">
                            <strong>Modo confirmación:</strong>
                            <span id="rwm-badge" class="badge badge-secondary">Cargando...</span>
                            <button class="btn btn-xs btn-default" onclick="cambiarModoRWM()">
                                <i class="fa fa-exchange-alt"></i> Cambiar modo
                            </button>
                            <span class="text-muted small">DEMO emite Nota de Venta (no fiscal, no SUNAT) — PRODUCCION emite Boleta/Factura real.</span>
                        </div>
                        <div class="alert alert-info" style="font-size:0.85rem;">
                            <p class="mb-2"><i class="fas fa-info-circle mr-1"></i> El stock ya se descontó al crear la reserva.</p>
                            <ul class="mb-0 pl-3">
                                <li><i class="fa fa-check text-success mr-1"></i> <strong>Confirmar</strong>: usar cuando el pago llegó (banco/Yape/lo que sea, se valida fuera de este sistema) — pasa al flujo normal de despachos.</li>
                                <li><i class="fa fa-times text-danger mr-1"></i> <strong>Anular</strong>: devuelve el stock.</li>
                                <li><i class="fa fa-clock text-muted mr-1"></i> Sin expiración automática — las reservas <strong>PENDIENTE</strong> se acumulan hasta que alguien las revise.</li>
                                <li><i class="fa fa-truck text-warning mr-1"></i> Si la sede de recojo elegida no alcanzaba, la fila queda marcada <strong>"Falta cubrir stock"</strong> — entra al detalle y usa <strong>Cubrir stock</strong> para trasladar desde otra sede.</li>
                                <li><i class="fa fa-lock text-muted mr-1"></i> <strong>Confirmar</strong> se habilita solo cuando ya no falta nada por cubrir.</li>
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="dt-reservas-web"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Pedido</th>
                                        <th>Cliente</th>
                                        <th>Email</th>
                                        <th>Sede recojo</th>
                                        <th>Total</th>
                                        <th>Items</th>
                                        <th>Estado</th>
                                        <th>Estado envío</th>
                                        <th>Comprobante</th>
                                        <th>Acción</th>
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
@endsection

@push('scripts')
    <script>
        let dtReservasWeb = null;

        document.addEventListener('DOMContentLoaded', () => {
            cargarModoRWM();

            dtReservasWeb = new DataTable('#dt-reservas-web', {
                processing: true,
                serverSide: true,
                ajax: '{{ route('pedidos.reservas_web.getTable') }}',
                order: [[0, 'desc']],
                columns: [
                    { data: 'fecha_reserva', render: (d) => d ? new Date(d).toLocaleString('es-PE') : '-' },
                    { data: 'codigo_pedido_ecommerce' },
                    { data: 'cliente_nombre' },
                    { data: 'cliente_email' },
                    { data: 'sede_recojo_nombre', render: (d) => d ?? '<span class="text-muted">-</span>' },
                    { data: 'total', render: (d) => 'S/ ' + Number(d).toFixed(2) },
                    { data: 'detalle_count', orderable: false, searchable: false },
                    {
                        data: 'estado',
                        render: (estado, type, row) => {
                            const clase = estado === 'PENDIENTE' ? 'warning' : (estado === 'CONFIRMADO' ? 'success' : 'danger');
                            const badge = `<span class="badge badge-${clase}">${estado}</span>`;
                            const pendiente = row.tiene_pendiente
                                ? ' <span class="badge badge-danger" title="Alguna línea no alcanzó stock en la sede elegida">Falta cubrir stock</span>'
                                : '';
                            return badge + pendiente;
                        }
                    },
                    {
                        data: 'estado_envio',
                        render: (d) => d ? `<span class="badge badge-info">${d}</span>` : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'comprobante_numero',
                        render: (d) => d ? `<span class="badge badge-success">${d}</span>` : '<span class="text-muted">-</span>'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (row) => `
                            <a href="${route('pedidos.reservas_web.show', row.id)}" class="btn btn-info btn-xs">
                                <i class="fa fa-eye"></i> Ver detalle
                            </a>
                            ${row.estado !== 'PENDIENTE' ? '<span class="text-muted small ml-1">Resuelta</span>' : ''}`
                    },
                ],
                language: { url: "{{ asset('Spanish.json') }}" },
            });
        });

        function pintarModoRWM(modo) {
            const badge = document.getElementById('rwm-badge');
            badge.textContent = modo;
            badge.className = 'badge ' + (modo === 'DEMO' ? 'badge-warning' : 'badge-success');
        }

        async function cargarModoRWM() {
            try {
                const res = await axios.get(route('pedidos.reservas_web.getModo'));
                pintarModoRWM(res.data.modo);
            } catch (e) {
                document.getElementById('rwm-badge').textContent = 'ERROR';
            }
        }

        async function cambiarModoRWM() {
            const modoActual = document.getElementById('rwm-badge').textContent;
            const nuevoModo = modoActual === 'DEMO' ? 'PRODUCCION' : 'DEMO';

            const result = await Swal.fire({
                title: `¿Cambiar a modo ${nuevoModo}?`,
                text: nuevoModo === 'DEMO'
                    ? 'Las próximas confirmaciones emitirán Nota de Venta (no fiscal, no va a SUNAT).'
                    : 'Las próximas confirmaciones emitirán Boleta o Factura real (sí va a SUNAT).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: nuevoModo === 'DEMO' ? '#f8ac59' : '#1ab394',
            });
            if (!result.isConfirmed) return;

            try {
                const res = await axios.post(route('pedidos.reservas_web.setModo'), { modo: nuevoModo });
                if (res.data.success) {
                    pintarModoRWM(res.data.modo);
                    toastr.success(res.data.message, 'LISTO');
                } else {
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                toastr.error(e.response?.data?.message || e.message, 'ERROR');
            }
        }
    </script>
@endpush
