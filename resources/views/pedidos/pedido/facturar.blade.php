@extends('layout')
@section('content')

@section('pedidos-active', 'active')
@section('pedido-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>Facturar Pedido</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Pedidos</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-12">
                            @include('pedidos.pedido.forms.form_facturar')
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4><b>Detalle del Pedido</b></h4>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        @include('pedidos.pedido.tables.tbl_pedido_detalle_ver')
                                    </div>
                                </div>
                                <div class="panel-footer panel-primary">
                                    <div class="table-responsive">
                                        @include('pedidos.pedido.tables.tbl_montos_ver')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-6 text-left">
                                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small
                                        class="leyenda-required">Los campos marcados con asterisco
                                        (<label class="required"></label>) son obligatorios.</small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('pedidos.pedido.index') }}" id="btn_cancelar"
                                        class="btn btn-w-m btn-default">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>

                                    <button type="submit" form="form-pedido-facturar" class="btn btn-w-m btn-primary">
                                        <i class="fa fa-save"></i> Grabar
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('styles')
@endpush

@push('scripts')
<script>
    let dtPedidoDetalle = null;
    document.addEventListener('DOMContentLoaded', function() {
        loadSelect2();
        pintarDetallePedido(@json($detalle));
        dtPedidoDetalle = iniciarDataTable('table-detalle-pedido-ver');
        pintarMontos(@json($pedido));
        events();
        //====== CARGANDO EMBALAJE Y ENVÍO PREVIO =======
        tfootEmbalaje.value = @json($pedido->monto_embalaje);
        tfootEnvio.value = @json($pedido->monto_envio);
    });

    function events() {
        document.addEventListener('submit', (e) => {
            e.preventDefault();
            facturarPedido(e.target);
        })
    }

    function facturarPedido(formFacturarPedido) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: `¿Facturar pedido PE-${@json($pedido->id)}?`,
            html: `
            <div style="text-align: center;">
                <p class="m-0"><i class="fas fa-file-invoice"></i> <b>Comprobante:</b> ${document.querySelector('#comprobante option:checked')?.textContent || '-'}</p>
                <p class="m-0"><i class="fas fa-user-shield"></i> <b>Registrador:</b> ${document.getElementById('registrador')?.value || '-'}</p>
                <p class="m-0"><i class="fas fa-calendar-alt"></i> <b>Fecha Registro:</b> ${document.getElementById('fecha_registro')?.value || '-'}</p>
                <p class="m-0"><i class="fas fa-warehouse"></i> <b>Almacén:</b> ${document.getElementById('almacen')?.value || '-'}</p>
                <p class="m-0"><i class="fas fa-file-signature"></i> <b>Condición:</b> ${document.getElementById('condicion_id')?.value || '-'}</p>
                <p class="m-0"><i class="fas fa-calendar-day"></i> <b>Fecha Propuesta:</b> ${document.getElementById('fecha_propuesta')?.value || '-'}</p>
                <p class="m-0"><i class="fas fa-user"></i> <b>Cliente:</b> ${document.getElementById('cliente')?.value || '-'}</p>
            </div>
        `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí",
            cancelButtonText: "Cancelar",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Facturando...',
                    text: 'Por favor, espera un momento',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const formData = new FormData(formFacturarPedido);
                    formData.append('pedido_id', @json($pedido->id));
                    const res = await axios.post(route('pedidos.pedido.facturar-store'), formData);
                    if (res.data.success) {
                        let url_open_pdf = '{{ route('ventas.documento.comprobante', [':id1', ':size']) }}'
                            .replace(':id1', res.data.documento_id)
                            .replace(':size', 80);
                        window.open(url_open_pdf, 'Comprobante SISCOM',
                            'location=1, status=1, scrollbars=1,width=900, height=600');
                        window.location.href = '{{ route('ventas.documento.index') }}';
                    } else {
                        Swal.close();
                        toastr.error(res.data.message, 'ERROR EN EL SERVIDOR');
                        return;
                    }
                } catch (error) {
                    Swal.close();
                    toastr.error('Error al facturar el pedido. Por favor, intente nuevamente.', 'Error');
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cancelado',
                    text: 'No se realizaron cambios.',
                    confirmButtonText: 'Entendido',
                    customClass: {
                        confirmButton: "btn btn-primary"
                    },
                    buttonsStyling: false
                });
            }
        });
    }

    function loadSelect2() {
        $(".select2_form").select2({
            placeholder: "SELECCIONAR",
            allowClear: true,
            height: '200px',
            width: '100%',
        });
    }

    //========= PINTAR DETALLE PEDIDO =======
    function pintarDetallePedido(lstItems) {

        let fila = ``;
        let htmlTallas = ``;
        const bodyDetalleTable = document.querySelector('#table-detalle-pedido-ver tbody');
        const tallas = @json($tallas);

        limpiarTabla('table-detalle-pedido-ver');

        lstItems.forEach((c) => {
            htmlTallas = ``;
            fila += `<tr>

                            <th>
                                <div style="width:120px;">${c.producto_nombre}</div>
                            </th>
                            <th>${c.color_nombre}</th>`;


            //==== TALLAS ====
            tallas.forEach((t) => {
                let cantidad = c.tallas.filter((ct) => {
                    return t.id == ct.talla_id;
                });
                cantidad.length != 0 ? cantidad = cantidad[0].cantidad : cantidad = '';
                htmlTallas += `<td>${cantidad}</td>`;
            })


            htmlTallas += `
                <td style="text-align: right;">
                    <div style="width:100px;">
                        <span class="precio_venta_${c.producto_id}_${c.color_id}">
                            ${formatoMoneda(c.porcentaje_descuento === 0 ? c.precio_venta : c.precio_venta_nuevo)}
                        </span>
                    </div>
                </td>
                <td class="td-subtotal" style="text-align: right;">
                    <span class="subtotal_${c.producto_id}_${c.color_id}">
                        ${formatoMoneda(c.subtotal)}
                    </span>
                </td>
                <td style="text-align: center;">
                    <input
                        disabled
                        data-producto-id="${c.producto_id}"
                        data-color-id="${c.color_id}"
                        style="width:130px; margin: 0 auto;"
                        value="${formatoNumero(c.porcentaje_descuento)}"
                        class="form-control detailDescuento">
                    </input>
                </td>
            </tr>`;

            fila += htmlTallas;
            bodyDetalleTable.innerHTML = fila;
        })
    }

    function pintarMontos(pedido) {
        document.querySelector('.subtotal').innerHTML           =   formatoMoneda(pedido.sub_total);
        document.querySelector('.embalaje').innerHTML           =   formatoMoneda(pedido.monto_embalaje);
        document.querySelector('.envio').innerHTML              =   formatoMoneda(pedido.monto_envio);
        document.querySelector('.descuento').innerHTML          =   formatoMoneda(pedido.monto_descuento);
        document.querySelector('.total-pagar').innerHTML        =   formatoMoneda(pedido.total_pagar);
        document.querySelector('.igv').innerHTML                =   formatoMoneda(pedido.total_igv);
        document.querySelector('.total').innerHTML              =   formatoMoneda(pedido.total);
    }
</script>
@endpush
