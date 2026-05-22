@extends('layout')
@section('mantenimiento-active', 'active')
@section('empresas-active', 'active')

@section('bread-module', 'Mantenimiento')
@section('bread-submodule', 'Empresa')
@section('hero-title', 'Facturacion')
@section('hero-subtitle', 'Empresa')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">

                    <div class="ibox-content">
                        @include('mantenimiento.empresas.forms.form_facturacion')
                    </div>

                    <!-- FOOTER DEL IBOX -->
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
                                <a href="{{ route('mantenimiento.empresas.index') }}" id="btn_cancelar"
                                    class="btn btn-w-m btn-default">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>

                                <button form="formFacturacion" type="submit" id="btn_grabar"
                                    class="btn btn-w-m btn-success">
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
        .logo {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }

        .custom-file-label::after {
            content: "Buscar";
        }
    </style>
    <link href="{{ mix('css/filepond.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ mix('js/filepond.js') }}"></script>
    <script src="{{ mix('js/tomselect.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {

        })


    </script>
@endpush
