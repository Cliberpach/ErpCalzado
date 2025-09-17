@extends('layout')
@section('content')

    @csrf

@section('mantenimiento-active', 'active')
@section('tipo_pago-active', 'active')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
        <h2 style="text-transform:uppercase"><b>ASIGNAR CUENTAS BANCARIAS</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Tipos de Pago</strong>
            </li>

        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content">
                    <div class="col-12 mb-3">
                        <div class="table-responsive">
                            @include('mantenimiento.tipo_pago.tables.tbl_asignar_cuentas')
                        </div>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button class="btn btn-danger btn-volver mr-1">
                            <i class="fas fa-arrow-left"></i> VOLVER
                        </button>
                        <button class="btn btn-success btn-asignar-cuentas">
                            <i class="fas fa-save"></i> REGISTRAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    const lstCuentasAsignadas = [];
    const cuentasBD = @json($cuentas);
    let dtCuentasAsignar = null;

    document.addEventListener('DOMContentLoaded', () => {
        dtCuentasAsignar = iniciarDataTable('tbl_asignar_cuentas');
        agregarCuentasPrevias();
        events();
    })

    function events() {
        document.addEventListener('click', function(e) {

            if (e.target.closest('.chk-cuenta')) {

                const checkbox = e.target.closest('.chk-cuenta');
                const cuentaId = parseInt(checkbox.getAttribute('data-id'));

                if (checkbox.checked) {
                    if (!lstCuentasAsignadas.includes(cuentaId)) {
                        lstCuentasAsignadas.push(cuentaId);
                    }
                } else {
                    const index = lstCuentasAsignadas.indexOf(cuentaId);
                    if (index !== -1) {
                        lstCuentasAsignadas.splice(index, 1);
                    }
                }

            }

            if (e.target.closest('.btn-asignar-cuentas')) {
                asignarCuentas();
            }

            if (e.target.closest('.btn-volver')) {
                window.location.href = @json(route('mantenimiento.tipo_pago.index'));
            }

        });
    }

    function agregarCuentasPrevias() {
        const cuentas_asignadas = @json($cuentas_asignadas);
        cuentas_asignadas.forEach((ca) => {
            lstCuentasAsignadas.push(ca.cuenta_id);
        })
    }

    function asignarCuentas() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA ASIGNAR LAS CUENTAS BANCARIAS AL MÉTODO DE PAGO?",
            text: "Se realizará la asignación!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, ASIGNAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                limpiarErroresValidacion('msgError');
                const token = document.querySelector('input[name="_token"]').value;

                const formData = new FormData();
                formData.append('lstCuentasAsignadas', JSON.stringify(lstCuentasAsignadas));
                formData.append('tipo_pago_id', @json($tipo_pago->id));

                const urlAsignarCuentas = @json(route('mantenimiento.tipo_pago.asignarCuentasStore'));

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Asignando cuentas bancarias...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(urlAsignarCuentas, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = @json(route('mantenimiento.tipo_pago.index'));
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ASIGNAR CUENTAS BANCARIAS');
                } finally {
                    Swal.close();
                }

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "OPERACIÓN CANCELADA",
                    text: "NO SE REALIZARON ACCIONES",
                    icon: "error"
                });
            }
        });
    }
</script>
