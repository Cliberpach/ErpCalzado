@extends('layout')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Empresa')
@section('hero-title', 'Facturación')
@section('hero-subtitle', 'Empresa')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">

                    <div class="ibox-content">
                        @include('mantenimiento.empresas.forms.form_facturacion')
                    </div>

                    <div class="ibox-footer">
                        <div class="row">

                            <div class="col-md-6 text-left" style="color:#fcbc6c">
                                <i class="fa fa-exclamation-circle"></i>
                                <small>
                                    Los campos marcados con asterisco
                                    (<label class="required"></label>) son obligatorios.
                                </small>
                            </div>

                            <div class="col-md-6 text-right">
                                <a href="{{ route('mantenimiento.empresas.index') }}" class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                                <button type="button" id="btn_grabar" class="btn btn-w-m btn-success" onclick="grabarFacturacion()">
                                    <i class="fa fa-save"></i> Grabar
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .custom-file-label::after { content: "Buscar"; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('certificado').addEventListener('change', function () {
                const name = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
                document.getElementById('lbl-certificado').textContent = name;
                actualizarIndicadorContra(name.split('.').pop().toLowerCase());
            });
        });

        function actualizarIndicadorContra(ext) {
            const esPfxP12 = ext === 'pfx' || ext === 'p12';
            document.getElementById('span-contra-requerida').style.display = esPfxP12 ? 'inline' : 'none';
            document.getElementById('span-contra-opcional').style.display  = esPfxP12 ? 'none'   : 'inline';
        }

        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon  = btn.querySelector('i');
            input.type  = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        async function grabarFacturacion() {
            limpiarErroresValidacion('error');

            const confirm = await Swal.fire({
                title: 'Guardar Configuración',
                text: '¿Confirmar guardar la configuración de facturación?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1ab394',
                confirmButtonText: 'Sí, Guardar',
                cancelButtonText: 'Cancelar',
            });

            if (!confirm.isConfirmed) return;

            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            const btn = document.getElementById('btn_grabar');
            btn.disabled = true;

            try {
                const form     = document.getElementById('formFacturacion');
                const formData = new FormData(form);

                const res = await axios.post(route('mantenimiento.empresas.facturacionStore'), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                if (res.data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        text: res.data.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    window.location.href = route('mantenimiento.empresas.index');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.data.message });
                }
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data.errors) {
                    Swal.close();
                    pintarErroresValidacion(error.response.data.errors, 'error');
                } else {
                    const msg = error.response?.data?.message || error.message || 'Error desconocido';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            } finally {
                btn.disabled = false;
            }
        }
    </script>
@endpush
