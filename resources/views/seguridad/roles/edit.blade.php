@extends('layout')

@section('seguridad-active', 'active')
@section('roles-active', 'active')

@section('bread-module', 'Seguridad')
@section('bread-submodule', 'Seguridad')
@section('hero-title', 'Editar Rol')
@section('hero-subtitle', 'Seguridad')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight" style="zoom: 90%;">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        @include('seguridad.roles.forms.form_edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        .logo {
            width: 190px;
            height: 190px;
            border-radius: 10%;
            position: absolute;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let dtPermisos = null;
        let permisosSeleccionados = @json($permission_role);

        document.addEventListener('DOMContentLoaded', () => {
            dtPermisos = iniciarDataTable('tbl-permissions', 50, true);
            events();
            cargarPermisosMarcados();
        });

        //========================================
        // EVENTOS
        //========================================
        function events() {

            //====== SUBMIT FORM ======
            document.querySelector('#form_edit_role').addEventListener('submit', updateRole);

            //====== EVENTOS CHANGE ======
            document.addEventListener('change', (e) => {

                //========================================
                // CHECKBOX PERMISOS
                //========================================
                if (e.target.classList.contains('permission-checkbox')) {

                    const permisoId = e.target.dataset.id;
                    const checked = e.target.checked;

                    if (checked) {
                        agregarPermiso(permisoId);
                    } else {
                        eliminarPermiso(permisoId);
                    }

                    actualizarCheckAll();
                }

                //========================================
                // CHECK GENERAL
                //========================================
                if (e.target.id === 'checkAllPermisos') {

                    const checked = e.target.checked;

                    const checks = document.querySelectorAll('.permission-checkbox');

                    checks.forEach(check => {

                        check.checked = checked;

                        const permisoId = check.dataset.id;

                        if (checked) {
                            agregarPermiso(permisoId);
                        } else {
                            eliminarPermiso(permisoId);
                        }

                    });

                    toastr.clear();

                    if (checked) {
                        toastr.success('Todos los permisos fueron seleccionados');
                    } else {
                        toastr.info('Todos los permisos fueron removidos');
                    }

                }

                //========================================
                // FULL ACCESS
                //========================================
                if (e.target.name === 'full-access') {

                    const valor = e.target.value;

                    bloquearPermisos(valor === 'SI');

                }

            });

        }

        //========================================
        // AGREGAR PERMISO
        //========================================
        function agregarPermiso(permisoId) {

            permisoId = permisoId.toString();

            const existe = permisosSeleccionados.includes(permisoId);

            if (!existe) {
                permisosSeleccionados.push(permisoId);
            }

        }

        //========================================
        // ELIMINAR PERMISO
        //========================================
        function eliminarPermiso(permisoId) {

            permisoId = permisoId.toString();

            const index = permisosSeleccionados.indexOf(permisoId);

            if (index !== -1) {
                permisosSeleccionados.splice(index, 1);
            }

        }

        //========================================
        // CARGAR PERMISOS MARCADOS
        //========================================
        function cargarPermisosMarcados() {

            //========================================
            // MARCAR CHECKS SEGUN ARRAY INICIAL
            //========================================
            document.querySelectorAll('.permission-checkbox')
                .forEach(check => {

                    const permisoId = check.dataset.id.toString();

                    check.checked = permisosSeleccionados.includes(permisoId);

                });

            console.log(
                'PERMISOS INICIALES:',
                permisosSeleccionados
            );

            actualizarCheckAll();

            //========================================
            // VALIDAR FULL ACCESS
            //========================================
            const fullAccess = document.querySelector(
                'input[name="full-access"]:checked'
            );

            if (fullAccess) {
                bloquearPermisos(fullAccess.value === 'SI');
            }

        }

        //========================================
        // CHECK GENERAL
        //========================================
        function actualizarCheckAll() {

            const totalChecks = document.querySelectorAll(
                '.permission-checkbox'
            ).length;

            const totalMarcados = document.querySelectorAll(
                '.permission-checkbox:checked'
            ).length;

            const checkAll = document.querySelector(
                '#checkAllPermisos'
            );

            if (checkAll) {
                checkAll.checked = (
                    totalChecks > 0 &&
                    totalChecks === totalMarcados
                );
            }

        }

        //========================================
        // BLOQUEAR PERMISOS
        //========================================
        function bloquearPermisos(fullAccess = false) {
            toastr.clear();
            const checks = document.querySelectorAll(
                '.permission-checkbox'
            );

            const cardPermisos = document.querySelector(
                '#cardPermisos'
            );

            if (fullAccess) {

                //====================================
                // LIMPIAR ARRAY
                //====================================
                permisosSeleccionados = [];

                //====================================
                // DESMARCAR CHECKS
                //====================================
                checks.forEach(check => {
                    check.checked = false;
                });

                //====================================
                // OCULTAR CARD
                //====================================
                if (cardPermisos) {
                    cardPermisos.style.display = 'none';
                }

                toastr.info(
                    'Full Access activado. No se requieren permisos específicos.'
                );

            } else {

                //====================================
                // MOSTRAR CARD
                //====================================
                if (cardPermisos) {
                    cardPermisos.style.display = 'block';
                }

            }

            actualizarCheckAll();

            console.log(
                'PERMISOS ACTUALES:',
                permisosSeleccionados
            );

        }

        //========================================
        // UPDATE ROLE
        //========================================
        async function updateRole(e) {

            e.preventDefault();

            Swal.fire({
                title: '¿Desea modificar el rol?',
                text: 'Se actualizará la información del rol.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(async (result) => {

                if (!result.isConfirmed) {
                    return;
                }

                const formData = new FormData(e.target);

                formData.append('_method', 'PUT');

                //====== AGREGAR PERMISOS ======
                formData.append(
                    'permissions',
                    JSON.stringify(permisosSeleccionados)
                );

                try {

                    Swal.fire({
                        title: 'Procesando...',
                        html: 'Actualizando Rol',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    limpiarErroresValidacion();

                    const id = @json($role->id);

                    const response = await axios.post(
                        route('seguridad.role.update', {
                            id: id
                        }),
                        formData
                    );

                    const res = response.data;

                    Swal.close();

                    //====== SUCCESS ======
                    if (res.success) {

                        toastr.success(
                            res.message || 'Rol actualizado correctamente'
                        );

                        window.location.href =
                            "{{ route('seguridad.role.index') }}";

                    } else {

                        toastr.error(
                            res.message || 'Ocurrió un error'
                        );

                    }

                } catch (error) {

                    Swal.close();

                    //====== VALIDACIONES ======
                    if (
                        error.response &&
                        error.response.status === 422
                    ) {

                        const errors = error.response.data.errors;

                        limpiarErroresValidacion();

                        if (errors) {
                            pintarErroresValidacion(errors);
                        }

                        toastr.error(
                            'Existen errores de validación'
                        );

                        return;
                    }

                    toastr.error(
                        'Error en la petición'
                    );

                    console.error(error);

                }

            });

        }
    </script>
@endpush
