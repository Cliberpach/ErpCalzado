@extends('layout')
@section('content')
@section('almacenes-active', 'active')
@section('vehiculos-active', 'active')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2 style="text-transform:uppercase"><b>EDITAR VEHÍCULO</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('almacenes.producto.index') }}">Vehículos</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    @include('almacenes.vehiculos.forms.form_edit_vehiculo')
                </div>
                <div class="ibox-footer d-flex justify-content-between align-items-center">
                    <span style="color:rgb(219, 155, 35);font-size:14px;font-weight:bold;">Los campos con * son
                        obligatorios</span>

                    <div style="display:flex;">
                        <button class="btn btn-danger btnVolver" style="margin-right:5px;" type="button">
                            <i class="fa-solid fa-door-open"></i> VOLVER
                        </button>
                        <button class="btn btn-primary" type="submit" form="formActualizarVehiculo">
                            <i class="fa-solid fa-floppy-disk"></i> ACTUALIZAR
                        </button>
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
    document.addEventListener('DOMContentLoaded', () => {
        iniciarSelect2();
        events();
    })

    function events() {

        document.querySelector('#formActualizarVehiculo').addEventListener('submit', (e) => {
            e.preventDefault();
            actualizarVehiculo();
        })

        document.addEventListener('click', (e) => {
            if (e.target.closest('.btnVolver')) {
                const rutaIndex = '{{ route('almacenes.vehiculos.index') }}';
                window.location.href = rutaIndex;
            }
        })

    }

    function iniciarSelect2() {
        $('.select2_form').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

    }

    function actualizarVehiculo() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA ACTUALIZAR EL VEHÍCULO?",
            text: "Se cambiarán los datos!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, ACTUALIZAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                limpiarErroresValidacion('msgError');

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Actualizando vehiculo...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    const token = document.querySelector('input[name="_token"]').value;
                    const formActualizarVehiculo = document.querySelector('#formActualizarVehiculo');
                    const formData = new FormData(formActualizarVehiculo);
                    const id = @json($vehiculo->id);
                    let urlUpdateVehiculo = `{{ route('almacenes.vehiculos.update', ['id' => ':id']) }}`;
                    urlUpdateVehiculo = urlUpdateVehiculo.replace(':id', id);


                    const response = await fetch(urlUpdateVehiculo, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: formData
                    });

                    const res = await response.json();

                    console.log(res);

                    if (response.status === 422) {
                        if ('errors' in res) {
                            pintarErroresValidacion(res.errors, 'error');
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        const vehiculo_index = @json(route('almacenes.vehiculos.index'));
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        window.location.href = vehiculo_index;
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        Swal.close();
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN ACTUALIZAR VEHÍCULO');
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
@endpush
