<!-- =========================================
MODAL DETALLE ROL
========================================= -->
<div class="modal fade" id="modalShowRole" tabindex="-1" role="dialog" aria-labelledby="modalShowRoleLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">

        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">

            <!--========================================
            HEADER
            =========================================-->
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg,#0b5ed7,#3d8bfd);">

                <div class="w-100">

                    <div class="d-flex align-items-center flex-wrap">

                        <div class="mr-3 mb-2 mb-md-0">

                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow"
                                style="width:60px;height:60px;">

                                <i class="fas fa-user-shield text-success fa-lg"></i>

                            </div>

                        </div>

                        <div>

                            <h4 class="modal-title font-weight-bold mb-1" id="modalShowRoleLabel">

                                DETALLE DEL ROL

                            </h4>

                            <small class="text-white-50">
                                Información general y permisos asignados
                            </small>

                        </div>

                    </div>

                </div>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>

            <!--========================================
            BODY
            =========================================-->
            <div class="modal-body bg-light p-3 p-md-4">

                <div class="row">

                    <!--========================================
                    DATOS GENERALES
                    =========================================-->
                    <div class="col-xl-4 col-lg-5 col-md-12 mb-4">

                        <div class="card border-0 shadow-sm rounded-lg h-100">

                            <div class="card-header bg-white border-bottom">

                                <h5 class="mb-0 font-weight-bold text-success">

                                    <i class="fas fa-info-circle mr-2"></i>
                                    DATOS GENERALES

                                </h5>

                            </div>

                            <div class="card-body">

                                <!-- NOMBRE -->
                                <div class="mb-4">

                                    <small class="text-muted font-weight-bold d-block mb-2">
                                        NOMBRE
                                    </small>

                                    <div class="border rounded px-3 py-3 bg-white">

                                        <h5 class="font-weight-bold text-dark mb-0 break-text" id="show_role_name">
                                            -
                                        </h5>

                                    </div>

                                </div>

                                <!-- SLUG -->
                                <div class="mb-4">

                                    <small class="text-muted font-weight-bold d-block mb-2">
                                        SLUG
                                    </small>

                                    <div class="border rounded px-3 py-3 bg-white">

                                        <code class="text-success font-weight-bold break-text" id="show_role_slug">

                                            -

                                        </code>

                                    </div>

                                </div>

                                <!-- FULL ACCESS -->
                                <div class="mb-4">

                                    <small class="text-muted font-weight-bold d-block mb-2">
                                        FULL ACCESS
                                    </small>

                                    <div>

                                        <span class="badge badge-success px-4 py-2 shadow-sm"
                                            id="show_role_full_access">

                                            -

                                        </span>

                                    </div>

                                </div>

                                <!-- PUNTO VENTA -->
                                <div class="mb-4">

                                    <small class="text-muted font-weight-bold d-block mb-2">
                                        PUNTO DE VENTA
                                    </small>

                                    <div>

                                        <span class="badge badge-info px-4 py-2 shadow-sm" id="show_role_punto_venta">

                                            -

                                        </span>

                                    </div>

                                </div>

                                <!-- DESCRIPCION -->
                                <div>

                                    <small class="text-muted font-weight-bold d-block mb-2">
                                        DESCRIPCIÓN
                                    </small>

                                    <div class="border rounded p-3 bg-white text-dark" id="show_role_description"
                                        style="min-height:120px;">

                                        -

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!--========================================
                    PERMISOS
                    =========================================-->
                    <div class="col-xl-8 col-lg-7 col-md-12">

                        <div class="card border-0 shadow-sm rounded-lg h-100">

                            <div class="card-header bg-white border-bottom">

                                <div
                                    class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                                    <h5 class="mb-3 mb-md-0 font-weight-bold text-success">

                                        <i class="fas fa-key mr-2"></i>
                                        PERMISOS ASIGNADOS

                                    </h5>

                                    <span class="badge badge-success px-4 py-2 shadow-sm" id="show_total_permissions">

                                        0 PERMISOS

                                    </span>

                                </div>

                            </div>

                            <div class="card-body">

                                <!-- TABLA -->
                                <div class="table-responsive border rounded">
                                    @include('seguridad.roles.table.tbl_show_permissions')
                                </div>

                                <!-- EMPTY -->
                                <div class="text-center py-5 d-none" id="empty_permissions">

                                    <div class="mb-3">

                                        <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center shadow"
                                            style="width:85px;height:85px;">

                                            <i class="fas fa-folder-open text-white fa-2x"></i>

                                        </div>

                                    </div>

                                    <h5 class="font-weight-bold text-secondary">
                                        EL ROL NO TIENE PERMISOS
                                    </h5>

                                    <small class="text-muted">
                                        No existen permisos asignados actualmente
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!--========================================
            FOOTER
            =========================================-->
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-danger px-4" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Cerrar
                </button>
            </div>

        </div>

    </div>

</div>

<style>
    #modalShowRole .break-text {
        word-break: break-word;
    }

    #modalShowRole .table td,
    #modalShowRole .table th {
        vertical-align: middle;
    }

    #modalShowRole .table thead th {
        border-color: rgba(255, 255, 255, .15);
        white-space: nowrap;
    }

    #modalShowRole .badge {
        font-size: .85rem;
        letter-spacing: .5px;
    }

    #modalShowRole .card {
        transition: .2s ease;
    }

    #modalShowRole .card:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 991px) {

        #modalShowRole .modal-dialog {
            margin: .7rem;
        }

    }

    @media (max-width: 768px) {

        #modalShowRole .modal-body {
            padding: 1rem;
        }

        #modalShowRole .modal-title {
            font-size: 1.2rem;
        }

        #modalShowRole .table {
            font-size: .83rem;
        }

        #modalShowRole .card-body {
            padding: 1rem;
        }

        #modalShowRole .badge {
            width: 100%;
            text-align: center;
        }

    }
</style>

<script>
    const paramsMdlShowRole = {
        dtPermissionRole: null
    };

    async function openMdlShowRole(id) {

        const role = await getRoleShow(id);
        if (!role) {
            return;
        }

        paintRoleShow(role);

        $('#modalShowRole').modal('show');
    }

    function paintRoleShow(role) {
        //========================================
        // DATOS GENERALES
        //========================================
        document.querySelector('#show_role_name').textContent =
            role.name || '-';

        document.querySelector('#show_role_slug').textContent =
            role.slug || '-';

        document.querySelector('#show_role_description').textContent =
            role.description || '-';

        document.querySelector('#show_role_full_access').textContent =
            role.full_access || 'NO';

        document.querySelector('#show_role_punto_venta').textContent =
            role.punto_venta || 'NO';

        //========================================
        // BADGES
        //========================================
        const badgeFullAccess = document.querySelector('#show_role_full_access');

        badgeFullAccess.classList.remove(
            'badge-success',
            'badge-secondary'
        );

        badgeFullAccess.classList.add(
            role.full_access === 'SI' ?
            'badge-success' :
            'badge-secondary'
        );

        const badgePuntoVenta = document.querySelector('#show_role_punto_venta');

        badgePuntoVenta.classList.remove(
            'badge-info',
            'badge-secondary'
        );

        badgePuntoVenta.classList.add(
            role.punto_venta === 'SI' ?
            'badge-info' :
            'badge-secondary'
        );

        paintRolePermissionsShow(role);
    }

    function paintRolePermissionsShow(role) {

        //========================================
        // TABLA PERMISOS
        //========================================
        const tbody = document.querySelector('#tbody_show_permissions');
        const empty = document.querySelector('#empty_permissions');

        paramsMdlShowRole.dtPermissionRole = destruirDataTable(paramsMdlShowRole.dtPermissionRole);
        tbody.innerHTML = '';

        const permissions = role.permissions || [];

        document.querySelector('#show_total_permissions').textContent = `${permissions.length} PERMISOS`;

        //========================================
        // EMPTY
        //========================================
        if (permissions.length === 0) {

            empty.classList.remove('d-none');

        } else {

            empty.classList.add('d-none');

            permissions.forEach(permission => {

                tbody.innerHTML += `
                    <tr>

                        <td class="text-center font-weight-bold text-secondary">
                            ${permission.id}
                        </td>

                        <td>
                            <span class="badge badge-success px-3 py-2">
                                <i class="fas fa-layer-group mr-1"></i>
                                ${permission.modulo ?? '-'}
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-info px-3 py-2">
                                <i class="fas fa-cube mr-1"></i>
                                ${permission.submodulo ?? '-'}
                            </span>
                        </td>

                        <td>
                            <code class="text-success font-weight-bold">
                                ${permission.slug}
                            </code>
                        </td>

                        <td class="font-weight-bold text-dark">
                            ${permission.name}
                        </td>

                    </tr>
                `;

            });
        }
        paramsMdlShowRole.dtPermissionRole = iniciarDataTable('tbl_show_permissions', 50, true);

    }

    function closeMdlShowRole() {
        $('#modalShowRole').modal('hide');
    }

    async function getRoleShow(id) {
        try {
            mostrarAnimacion();
            const res = await axios.get(route('seguridad.role.show', {
                id: id
            }));

            if (res.data.success) {
                toastr.info(res.data.message, 'Operación completada');
                return res.data.data;
            } else {
                toastr.error(res.data.message, 'Error en el servidor');
                return null;
            }
        } catch (error) {
            toastr.error(error, 'Error en la petición ver rol');
            return null;
        } finally {
            ocultarAnimacion();
        }
    }
</script>
