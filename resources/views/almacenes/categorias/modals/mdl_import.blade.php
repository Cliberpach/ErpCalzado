<div class="modal fade" id="mdlImportCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Importar Categoría</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12 mb-3">
                        <button class="btn btn-danger" onclick="downloadFormatExcel();"><i
                                class="fa-solid fa-download"></i> Descargar Formato</button>
                    </div>
                    <div class="col-12">
                        @include('category.forms.form_import')
                    </div>
                    <div class="col-12 mt-3">
                        @include('category.tables.tbl_import_categories')
                    </div>
                </div>

            </div>
            <div
                class="modal-footer d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <span class="text-warning fw-bold small">
                    * Los campos marcados son obligatorios
                </span>
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button class="btn btn-primary" type="submit" form="formImportarCategorias">
                        <i class="fa-solid fa-upload"></i> Importar
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let dtImportCategorias = null;

    function eventsImport() {
        document.querySelector('#formImportarCategorias').addEventListener('submit', (e) => {
            e.preventDefault();
            importarCategoriasExcel();
        })

        $('#mdlImportCategoria').on('hidden.bs.modal', function() {
            $('#formImportarCategorias')[0].reset();
            destroyDataTable(dtImportCategorias);
            clearTable('tbl_import_categories');
            loadDtImport();
        });

    }

    function openMdlImportCategoria() {
        $('#mdlImportCategoria').modal('show');
    }

    function loadDtImport() {
        dtImportCategorias = new DataTable('#tbl_import_categories', {
            language: {
                "lengthMenu": "Mostrar _MENU_ categorias por página",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ categorias",
                "infoEmpty": "Mostrando 0 a 0 de 0 categorias",
                "infoFiltered": "(filtrado de _MAX_ categorias totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "emptyTable": "No hay datos disponibles en la tabla",
                "aria": {
                    "sortAscending": ": activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": activar para ordenar la columna de manera descendente"
                }
            }
        });
    }


    function downloadFormatExcel() {
        const ruta = @json(route('tenant.inventario.categorias.get-format-excel'));
        window.location.href = ruta;
    }

    function importarCategoriasExcel() {

        const inputImportExcelCategorias = document.querySelector('#inputImportExcelCategorias');

        if (inputImportExcelCategorias.files.length === 0) {
            toastr.error('DEBE CARGAR UN EXCEL PARA PROCEDER CON LA IMPORTACIÓN');
            return;
        }

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "DESEA IMPORTAR EL LISTADO DE CATEGORÍAS?",
            text: "Esta operación producirá cambios en el listado de categorías!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "SÍ, IMPORTAR!",
            cancelButtonText: "NO, CANCELAR!",
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: 'Cargando...',
                    html: 'Importando categorías ...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {

                    clearValidationErrors('msgError');
                    const token = document.querySelector('input[name="_token"]').value;
                    const formImportarCategorias = document.querySelector('#formImportarCategorias');
                    const formData = new FormData(formImportarCategorias);
                    const urlImportarCategorias = @json(route('tenant.inventario.categorias.import-categories-excel'));

                    formData.append('categorias_import_excel', inputImportExcelCategorias.files[0]);

                    const response = await fetch(urlImportarCategorias, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (response.status === 422) {
                        if ('errors' in res) {
                            clearValidationErrors(res.errors);
                        }
                        Swal.close();
                        return;
                    }

                    if (res.success) {
                        toastr.success(res.message, 'OPERACIÓN COMPLETADA');
                        if ('resultado' in res) {
                            console.log(res);
                            destroyDataTable(dtImportCategorias);
                            clearTable('tbl_import_categories');
                            pintarTableImportCategorias(res.resultado.listadoCategorias);
                            loadDtImport();
                        }
                        dtCategories.ajax.reload(null, false);
                    } else {
                        toastr.error(res.message, 'ERROR EN EL SERVIDOR');
                        if ('resultado' in res) {
                            console.log(res);
                            destroyDataTable(dtImportCategorias);
                            clearTable('tbl_import_categories');
                            pintarTableImportCategorias(res.resultado.listadoCategorias);
                            loadDtImport();
                        }
                    }


                } catch (error) {
                    toastr.error(error, 'ERROR EN LA PETICIÓN IMPORTAR EXCEL');
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

    function pintarErroresValidacion(objErroresValidacion) {
        for (let clave in objErroresValidacion) {
            const pError = document.querySelector(`.${clave}_error`);
            pError.textContent = objErroresValidacion[clave][0];
        }
    }

    function pintarTableImportCategorias(lstCategorias) {
        const tbody = document.querySelector('#tbl_import_categories tbody');
        let filas = ``;
        lstCategorias.forEach((lc) => {
            filas += `<tr>
                            <th>${lc.fila}</th>
                            <td>${lc.nombre}</td>
                            <td>${lc.error}</td>
                        </tr>`;
        })

        tbody.innerHTML = filas;
    }
</script>
