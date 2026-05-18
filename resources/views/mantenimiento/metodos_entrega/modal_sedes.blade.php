<style>
    .col-center {
        vertical-align: middle !important;
    }

    .swal-container {
        z-index: 99999;
    }
</style>

<div class="modal inmodal" id="modal_sedes" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" @click.prevent="Cerrar">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>

                <i class="fas fa-city modal-icon"></i>
                <h4 class="modal-title">SEDES</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body content_cliente">
                @include('components.overlay_search')
                @include('components.overlay_save')

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" id="link-show-sede">
                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab"
                            aria-controls="tab1" aria-selected="true">LISTADO DE SEDES</a>
                    </li>
                    <li class="nav-item" id="link-create-sede">
                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab"
                            aria-controls="tab2" aria-selected="false">REGISTRAR SEDE</a>
                    </li>
                    <li class="nav-item d-none" id="link-edit-sede">
                        <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab"
                            aria-controls="tab3" aria-selected="false">EDITAR SEDE</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab"
                        style="padding-top: 15px;">
                        @include('mantenimiento.metodos_entrega.table-sedes')
                    </div>
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <form id="formSede" action="" method="post">
                            <input name="agencia" type="text" hidden id="agencia_id">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mt-3">
                                    <label for="direccion" style="font-weight: bold;">
                                        DIRECCIÓN EMPRESA<span
                                            style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                                    </label>
                                    <input class="form-control direccion_sede" id="direccion_sede_store" required
                                        name="direccion" placeholder="INGRESE UNA DIRECCIÓN"></input>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Departamento</label>
                                    <select required name="departamento" id="departamento_store" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                        @foreach (departamentos() as $departamento)
                                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Provincia</label>
                                    <select required name="provincia" id="provincia_store" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Distrito</label>
                                    <select required name="distrito" id="distrito_store" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button form="formSede" type="submit" id="btn-guardar-sede"
                                        class="btn btn-primary btn-sm" style="color:white;"><i
                                            class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                        <form id="formEditSede" action="" method="post">
                            <input type="hidden" id="sede_id" name="sede">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mt-3">
                                    <label for="direccion" style="font-weight: bold;">
                                        DIRECCIÓN EMPRESA<span
                                            style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                                    </label>
                                    <input class="direccion_sede form-control" id="direccion_sede_update" required
                                        name="direccion" placeholder="INGRESE UNA DIRECCIÓN"></input>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Departamento</label>
                                    <select required name="departamento" id="departamento_update" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                        @foreach (departamentos() as $departamento)
                                            <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Provincia</label>
                                    <select required name="provincia" id="provincia_update" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-xs-12 mt-3">
                                    <label class="required" style="font-weight: bold;">Distrito</label>
                                    <select required name="distrito" id="distrito_update" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger mr-2"
                                        id="btn-regresar-edit-sede">REGRESAR</button>
                                    <button form="formEditSede" type="submit" id="btn-update-sede"
                                        class="btn btn-primary btn-sm" style="color:white;"><i
                                            class="fa fa-save"></i> ACTUALIZAR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los
                        campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    let tsDepStore, tsPrvStore, tsDistStore;
    let tsDepUpdate, tsPrvUpdate, tsDistUpdate;

    function initSedesTomSelects() {
        const base = { create: false, allowEmptyOption: true };

        if (!document.getElementById('departamento_store').tomselect) {
            tsDepStore = new TomSelect('#departamento_store', {
                ...base,
                onChange: val => onDepChange(val, tsPrvStore, tsDistStore),
            });
        }
        if (!document.getElementById('provincia_store').tomselect) {
            tsPrvStore = new TomSelect('#provincia_store', {
                ...base,
                onChange: val => onPrvChange(val, tsDistStore),
            });
            tsPrvStore.disable();
        }
        if (!document.getElementById('distrito_store').tomselect) {
            tsDistStore = new TomSelect('#distrito_store', base);
            tsDistStore.disable();
        }

        if (!document.getElementById('departamento_update').tomselect) {
            tsDepUpdate = new TomSelect('#departamento_update', {
                ...base,
                onChange: val => onDepChange(val, tsPrvUpdate, tsDistUpdate),
            });
        }
        if (!document.getElementById('provincia_update').tomselect) {
            tsPrvUpdate = new TomSelect('#provincia_update', {
                ...base,
                onChange: val => onPrvChange(val, tsDistUpdate),
            });
            tsPrvUpdate.disable();
        }
        if (!document.getElementById('distrito_update').tomselect) {
            tsDistUpdate = new TomSelect('#distrito_update', base);
            tsDistUpdate.disable();
        }
    }

    async function onDepChange(departamentoId, tsPrv, tsDist) {
        tsPrv.clear(true); tsPrv.clearOptions();
        tsPrv.addOption({ value: '', text: 'SELECCIONAR' });
        tsPrv.disable();

        tsDist.clear(true); tsDist.clearOptions();
        tsDist.addOption({ value: '', text: 'SELECCIONAR' });
        tsDist.disable();

        if (!departamentoId) return;

        try {
            const res = await axios.post('{{ route('mantenimiento.ubigeo.provincias') }}', {
                _token: '{{ csrf_token() }}',
                departamento_id: departamentoId
            });
            if (!res.data.error && res.data.provincias) {
                res.data.provincias.forEach(p => tsPrv.addOption({ value: p.id, text: p.text }));
                tsPrv.refreshOptions(false);
                tsPrv.enable();
            }
        } catch { toastr.error('Error al cargar provincias', 'ERROR'); }
    }

    async function onPrvChange(provinciaId, tsDist) {
        tsDist.clear(true); tsDist.clearOptions();
        tsDist.addOption({ value: '', text: 'SELECCIONAR' });
        tsDist.disable();

        if (!provinciaId) return;

        try {
            const res = await axios.post('{{ route('mantenimiento.ubigeo.distritos') }}', {
                _token: '{{ csrf_token() }}',
                provincia_id: provinciaId
            });
            if (!res.data.error && res.data.distritos) {
                res.data.distritos.forEach(d => tsDist.addOption({ value: d.id, text: d.text }));
                tsDist.refreshOptions(false);
                tsDist.enable();
            }
        } catch { toastr.error('Error al cargar distritos', 'ERROR'); }
    }

    function eventsSedes() {
        initSedesTomSelects();

        document.querySelector('#tab1-tab').addEventListener('click', () => {
            document.querySelector('#btn-guardar-sede').disabled = true;
        })

        document.querySelector('#tab2-tab').addEventListener('click', () => {
            document.querySelector('#btn-guardar-sede').disabled = false;
        })

        //========== GUARDAR SEDE ==========
        document.querySelector('#formSede').addEventListener('submit', async (e) => {
            document.querySelector('#btn-guardar-sede').disabled = true;
            e.preventDefault();
            try {
                const formData = new FormData(e.target);
                const res = await axios.post(route('mantenimiento.metodo_entrega.createSede'), formData);
                console.log(res);
                if (res.data.success) {
                    clearFormStoreSedes();

                    //======== DIBUJANDO LA NUEVA SEDE =======
                    const nueva_sede = res.data.nueva_sede;
                    sedes_data_table.row.add([
                        nueva_sede.id,
                        nueva_sede.direccion,
                        nueva_sede.departamento,
                        nueva_sede.provincia,
                        nueva_sede.distrito
                    ]).draw();
                    //====== REDIRIGIENDO A LISTADO DE SEDES =====
                    document.querySelector('#tab1-tab').click();
                    //====== MOSTRANDO ALERTA =======
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                } else {
                    document.querySelector('#btn-guardar-sede').disabled = false;
                    toastr.error(`${res.data.message} - ${res.data.exception}`, 'ERROR');
                }
            } catch (error) {
                document.querySelector('#btn-guardar-sede').disabled = false;
            }
        })

        //=========== ACTUALIZAR SEDE ==========
        document.querySelector('#formEditSede').addEventListener('submit', async (e) => {
            document.querySelector('#btn-update-sede').disabled = true;
            e.preventDefault();
            try {
                //=========== AGREGANDO SEDE_ID AL FORMDATA ========
                const formData = new FormData(e.target);
                formData.append('agencia', document.querySelector('#agencia_id').value);
                const res = await axios.post(route('mantenimiento.metodo_entrega.updateSede'), formData);

                if (res.data.success) {

                    clearFormUpdateSedes();
                    clearFormStoreSedes();

                    console.log(res);
                    //======== DIBUJANDO LA SEDE ACTUALIZADA =======
                    const sede_actualizada = res.data.sede_actualizada;

                    //===== BUSCANDO LA FILA DEL DATATABLE CUYA COL 0 COINCIDA CON EL ID DE LA SEDE ACTUALIZADA ======
                    const rowIndex = sedes_data_table.row((idx, data) => data[0] == sede_actualizada.id)
                        .index()

                    if (rowIndex !== -1) {
                        //======== ACTUALIZANDO SEDE EN EL DATATABLE ========
                        const rowData = sedes_data_table.row(rowIndex).data();

                        rowData[0] = sede_actualizada.id;
                        rowData[1] = sede_actualizada.direccion;
                        rowData[2] = sede_actualizada.departamento;
                        rowData[3] = sede_actualizada.provincia;
                        rowData[4] = sede_actualizada.distrito;

                        sedes_data_table.row(rowIndex).data(rowData).draw();
                    }

                    //====== REDIRIGIENDO A LISTADO DE SEDES TAB =====
                    document.querySelector('#tab1-tab').click();
                    document.querySelector('#link-show-sede').classList.remove('d-none');
                    //========= MOSTRAR CREATE SEDES TAB ======
                    document.querySelector('#link-create-sede').classList.remove('d-none');

                    //======== OCULTANDO EDIT SEDES TAB ========
                    document.querySelector('#link-edit-sede').classList.add('d-none');

                    //====== MOSTRANDO ALERTA =======
                    toastr.success(res.data.message, 'OPERACIÓN COMPLETADA');
                } else {
                    document.querySelector('#btn-update-sede').disabled = false;
                    toastr.error(`${res.data.message} - ${res.data.exception}`, 'ERROR');
                }
            } catch (error) {
                document.querySelector('#btn-guardar-sede').disabled = false;
            }
        })

        //======== BTN EDIT SEDE ======
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-edit-sede')) {
                const sede_id = e.target.getAttribute('data-sede-id');

                //======== REDIRIGIR A TAB EDIT SEDE =========
                document.querySelector('#tab3-tab').click();
                //========= MOSTRAR EL ENCABEZADO DE  EDIT SEDE =======
                document.querySelector('#link-edit-sede').classList.remove('d-none');
                //======= OCULAR ENCABEZADOS SHOW Y CREATE SEDE =======
                document.querySelector('#link-create-sede').classList.add('d-none');
                document.querySelector('#link-show-sede').classList.add('d-none');

                //======= COLOCAR LA SEDE_ID EN EL FORMULARIO EDIT SEDE =======
                document.querySelector('#sede_id').value = sede_id;

                //======= HABILITAR BTN UPDATE SEDE ======
                document.querySelector('#btn-update-sede').disabled = false;
            }


            //========== REGRESAR DEL EDIT SEDE ========
            if (e.target.getAttribute('id') === "btn-regresar-edit-sede") {
                //========= VOLVEMOS AL LISTADO DE SEDES =====
                document.querySelector('#tab1-tab').click();
                document.querySelector('#link-show-sede').classList.remove('d-none');
                document.querySelector('#link-create-sede').classList.remove('d-none');
                //======= OCULTAMOS EL TAB EDIT =======
                document.querySelector('#link-edit-sede').classList.add('d-none');
            }

            if (e.target.classList.contains('btn-delete-sede')) {
                const sede_id = e.target.getAttribute('data-sede-id');
                Swal.fire({
                    title: "Está seguro(a) de eliminar la sede?",
                    text: "Esta acción no es reversible!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, eliminar!",
                    customClass: {
                        container: 'swal-container',
                    },
                    allowOutsideClick: false,
                    preConfirm: async () => {
                        Swal.showLoading();
                        const res = await eliminarSede(sede_id);
                        return res;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value.success) {
                            //========= ACTUALIZANDO DATATABLE =======
                            const rowIndex = sedes_data_table.row((idx, data) => data[0] == result.value
                                .sede_id).index();
                            if (rowIndex !== -1) {
                                sedes_data_table.row(rowIndex).remove().draw();
                            }
                            toastr.success(result.value.message, 'OPERACIÓN COMPLETADA');
                        } else {
                            toastr.error(`${result.value.exception}`, result.value.message);
                        }
                    } else {
                        toastr.warning('NO SE REALIZÓ NINGUNA ACCIÓN', 'OPERACIÓN CANCELADA');
                    }
                });

            }

        })

    }

    async function eliminarSede(sede_id) {
        try {
            const res = await axios.post(route('mantenimiento.metodo_entrega.deleteSede'), {
                sede_id
            });
            return res.data;
        } catch (error) {
            toastr.error('ERROR EN EL SERVIDOR', 'CONTACTARSE CON EL ADMINISTRADOR DEL SISTEMA');
        }
    }

    function clearFormUpdateSedes() {
        document.querySelector('#direccion_sede_update').value = '';
        tsDepUpdate?.setValue('', true);
        tsPrvUpdate?.clear(true); tsPrvUpdate?.clearOptions(); tsPrvUpdate?.addOption({ value: '', text: 'SELECCIONAR' }); tsPrvUpdate?.disable();
        tsDistUpdate?.clear(true); tsDistUpdate?.clearOptions(); tsDistUpdate?.addOption({ value: '', text: 'SELECCIONAR' }); tsDistUpdate?.disable();
    }

    function clearFormStoreSedes() {
        document.querySelector('#direccion_sede_store').value = '';
        tsDepStore?.setValue('', true);
        tsPrvStore?.clear(true); tsPrvStore?.clearOptions(); tsPrvStore?.addOption({ value: '', text: 'SELECCIONAR' }); tsPrvStore?.disable();
        tsDistStore?.clear(true); tsDistStore?.clearOptions(); tsDistStore?.addOption({ value: '', text: 'SELECCIONAR' }); tsDistStore?.disable();
    }

    //========= CARGAR DATATABLE ===========
    function dataTableSedes() {
        return new DataTable('#table-sedes', {
            language: {
                processing: "CARGANDO SEDES...",
                search: "BUSCAR: ",
                lengthMenu: "MOSTRAR _MENU_ SEDES",
                info: "MOSTRANDO _START_ A _END_ DE _TOTAL_ SEDES",
                infoEmpty: "MOSTRANDO 0 SEDES",
                infoFiltered: "(FILTRADO de _MAX_ SEDES)",
                infoPostFix: "",
                loadingRecords: "CARGA EN CURSO",
                zeroRecords: "NINGUNA SEDE ENCONTRADA",
                emptyTable: "NO HAY SEDES DISPONIBLES",
                paginate: {
                    first: "PRIMERO",
                    previous: "ANTERIOR",
                    next: "SIGUIENTE",
                    last: "ÚLTIMO"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            },
            "columnDefs": [{
                "targets": -1,
                "data": "id",
                "className": "col-center",
                "render": function(data, type, row) {
                    return `<i class="fas fa-edit btn btn-primary btn-edit-sede" data-sede-id="${row[0]}"></i>
                            <i class="fas fa-trash-alt btn btn-danger btn-delete-sede" data-sede-id="${row[0]}"></i>`;
                }
            }],
            "order": [
                [0, "desc"]
            ],
            "ordering": true
        });
    }

    //============ PINTAR SEDES ========
    function pintarSedes(sedes) {
        sedes_data_table.clear().draw();

        sedes.forEach((s) => {
            sedes_data_table.row.add([
                s.id,
                s.direccion,
                s.departamento,
                s.provincia,
                s.distrito,
            ]).draw();
        })
    }
</script>
