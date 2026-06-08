@extends('layout')

@section('seguridad-active', 'active')
@section('users-active', 'active')

@section('bread-module', 'Seguridad')
@section('bread-submodule', 'Seguridad')
@section('hero-title', 'Registrar Usuario')
@section('hero-subtitle', 'Seguridad')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <form id="formCreateUser">
                            @csrf
                            <input type="hidden" name="sede_id" id="sede_id" value="{{ $sede_id }}">

                            <div class="row">
                                <div class="col-lg-6 col-xs-12 b-r">
                                    <h4><b>Datos Generales</b></h4>
                                    <hr>

                                    <div class="form-group">
                                        <label class="required">Usuario</label>
                                        <input type="text" id="usuario" name="usuario"
                                            class="form-control text-uppercase"
                                            maxlength="50">
                                        <span class="text-danger small usuario_msgError msgError"></span>
                                    </div>

                                    <div class="form-group">
                                        <label class="required">Email</label>
                                        <input type="email" id="email" name="email"
                                            class="form-control text-uppercase">
                                        <span class="text-danger small email_msgError msgError"></span>
                                    </div>

                                    <div class="form-group">
                                        <label class="required">Colaborador</label>
                                        <select name="colaborador_id" id="colaborador_id"
                                            class="form-control select2_form">
                                            <option></option>
                                            @foreach ($colaboradores as $colaborador)
                                                <option value="{{ $colaborador->id }}">
                                                    {{ $colaborador->nombre }} -
                                                    {{ $colaborador->tipo_documento_nombre . ':' . $colaborador->nro_documento }}
                                                    - {{ $colaborador->sede_nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger small colaborador_id_msgError msgError"></span>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label class="required">Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password"
                                                    class="form-control text-uppercase">
                                                <span class="input-group-append">
                                                    <button type="button" id="btn_toggle_pass" class="btn btn-default">
                                                        <i id="pass" class="fa fa-eye"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <span class="text-danger small password_msgError msgError"></span>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="required">Confirmar contraseña</label>
                                            <div class="input-group">
                                                <input type="password" id="confirm_password" name="confirm_password"
                                                    class="form-control text-uppercase">
                                                <span class="input-group-append">
                                                    <button type="button" id="btn_toggle_confirm" class="btn btn-default">
                                                        <i id="passcon" class="fa fa-eye"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <span class="text-danger small confirm_password_msgError msgError"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xs-12">
                                    <h4><b>Roles</b></h4>
                                    <hr>
                                    <div class="form-group row" style="overflow-y: auto;height:300px;">
                                        <div class="col-lg-12 col-xs-12">
                                            <div class="row">
                                                @foreach ($roles as $role)
                                                    <div class="col-lg-4 col-xs-4">
                                                        <div class="checkbox">
                                                            <input type="checkbox" id="role{{ $role->id }}"
                                                                name="role[]" value="{{ $role->id }}">
                                                            <label for="role{{ $role->id }}"
                                                                title="{{ $role->description }}">
                                                                {{ $role->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
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
                                            <i class="fa fa-exclamation-circle leyenda-required"></i>
                                            <small class="leyenda-required">Los campos marcados con asterisco
                                                (<label class="required"></label>) son obligatorios.</small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <a href="{{ route('seguridad.user.index') }}"
                                                class="btn btn-w-m btn-default">
                                                <i class="fa fa-arrow-left"></i> Regresar
                                            </a>
                                            <button type="submit" id="btn_grabar" class="btn btn-w-m btn-primary">
                                                <i class="fa fa-save"></i> Grabar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
    <style>
        .my-swal { z-index: 3000 !important; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('Inspinia/js/plugins/steps/jquery.steps.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            events();
        });

        function events() {
            document.querySelector('#formCreateUser').addEventListener('submit', (e) => {
                e.preventDefault();
                storeUser(e.target);
            });

            document.querySelector('#btn_toggle_pass').addEventListener('click', () => {
                togglePass('password', 'pass');
            });

            document.querySelector('#btn_toggle_confirm').addEventListener('click', () => {
                togglePass('confirm_password', 'passcon');
            });

            $('#colaborador_id').select2({
                placeholder: 'SELECCIONAR',
                allowClear: true,
                width: '100%',
            });
        }

        async function storeUser(form) {
            toastr.clear();
            const usuario = document.querySelector('#usuario').value || '';

            Swal.fire({
                title: '¿Desea registrar el usuario?',
                html: `Usuario: <b>${usuario}</b>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                confirmButtonText: 'Sí, Confirmar',
                cancelButtonText: 'No, Cancelar',
            }).then(async (result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Registrando usuario...',
                        text: 'Por favor, espere.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => { Swal.showLoading(); },
                    });

                    try {
                        limpiarErroresValidacion('msgError');

                        const formData = new FormData(form);
                        const res = await axios.post('{{ route('seguridad.user.store') }}', formData);

                        if (res.data.success) {
                            toastr.success(res.data.message, 'Operación completada');
                            setTimeout(() => {
                                window.location.href = '{{ route('seguridad.user.index') }}';
                            }, 1200);
                        } else {
                            toastr.error(res.data.message, 'Error en el servidor');
                        }

                    } catch (error) {
                        if (error.response) {
                            if (error.response.status === 422) {
                                pintarErroresValidacion(error.response.data.errors, 'msgError');
                                toastr.error('Errores de validación encontrados', 'Error de validación');
                            } else {
                                toastr.error(error.response.data.message ?? 'Error desconocido', 'Error en el servidor');
                            }
                        } else if (error.request) {
                            toastr.error('No se pudo contactar al servidor', 'Error de conexión');
                        } else {
                            toastr.error(error.message, 'Error desconocido');
                        }
                    } finally {
                        Swal.close();
                    }

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Cancelado', 'La solicitud se ha cancelado.', 'error');
                }
            });
        }

        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endpush
