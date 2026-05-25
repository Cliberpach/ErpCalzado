<style>
#tbl_stock_disponible thead th {
    background-color: #2e86c1 !important;
    color: #ffffff !important;
    white-space: nowrap;
    font-size: 12px;
    text-align: center;
    border: 1px solid #1a5276 !important;
}
#tbl_stock_disponible thead th:last-child {
    background-color: #1e8449 !important;
}
#tbl_stock_disponible tbody td {
    font-size: 12px;
    vertical-align: middle;
}
</style>

<template>
    <div>
        <div class="modal inmodal" id="modal_consultar_stock" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 95%; width: 95%;">
                <div class="modal-content animated bounceInRight">

                    <div class="modal-header" style="background:#1a5276; color:#fff; padding: 12px 20px;">
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1; font-size:22px;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fas fa-boxes"></i> CONSULTAR STOCKS DISPONIBLES
                        </h4>
                    </div>

                    <div class="modal-body" style="padding: 16px;">

                        <!-- Filtros -->
                        <div class="row mb-2">
                            <div class="col-12 col-md-2">
                                <label class="font-weight-bold mb-1" style="font-size:11px;">TALLA</label>
                                <v-select
                                    v-model="filtros.talla_id"
                                    :options="lst_tallas"
                                    :reduce="t => t.id"
                                    label="descripcion"
                                    placeholder="Todas"
                                >
                                </v-select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="font-weight-bold mb-1" style="font-size:11px;">COLOR</label>
                                <v-select
                                    v-model="filtros.color_id"
                                    :options="lst_colores"
                                    :reduce="c => c.id"
                                    label="descripcion"
                                    placeholder="Todos"
                                >
                                </v-select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="font-weight-bold mb-1" style="font-size:11px;">MARCA</label>
                                <v-select
                                    v-model="filtros.marca_id"
                                    :options="lst_marcas"
                                    :reduce="m => m.id"
                                    label="descripcion"
                                    placeholder="Todas"
                                >
                                </v-select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="font-weight-bold mb-1" style="font-size:11px;">CATEGORÍA</label>
                                <v-select
                                    v-model="filtros.categoria_id"
                                    :options="lst_categorias"
                                    :reduce="c => c.id"
                                    label="descripcion"
                                    placeholder="Todas"
                                >
                                </v-select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="font-weight-bold mb-1" style="font-size:11px;">ALMACÉN</label>
                                <v-select
                                    v-model="filtros.almacen_id"
                                    :options="lst_almacenes"
                                    :reduce="a => a.id"
                                    label="descripcion"
                                    placeholder="Todos">
                                </v-select>
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end" style="gap:4px;">
                                <button type="button" class="btn btn-primary btn-sm flex-fill"
                                    @click="recargarTabla">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm flex-fill"
                                    @click="limpiarFiltros">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>

                        <!-- Leyenda -->
                        <div class="mb-2 d-flex align-items-center" style="gap:16px; font-size:12px;">
                            <span>
                                <span style="display:inline-block; width:14px; height:14px; background:#fde8e8; border:1px solid #e9a0a0; vertical-align:middle; margin-right:4px;"></span>
                                Sin stock lógico
                            </span>
                        </div>

                        <!-- Tabla DataTable -->
                        <div class="table-responsive">
                            <table id="tbl_stock_disponible"
                                class="table table-bordered table-hover table-sm nowrap"
                                style="width:100%; text-transform:uppercase;">
                                <thead>
                                    <tr>
                                        <th>PRODUCTO</th>
                                        <th>COLOR</th>
                                        <th>TALLA</th>
                                        <th>ALMACÉN</th>
                                        <th>MARCA</th>
                                        <th>CATEGORÍA</th>
                                        <th>STOCK</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'datatables.net-responsive-bs4';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css';

export default {
    name: 'ModalConsultarStock',
    data() {
        return {
            tabla: null,
            filtros: {
                talla_id:     null,
                color_id:     null,
                marca_id:     null,
                categoria_id: null,
                almacen_id:   null,
            },
            lst_tallas:     [],
            lst_colores:    [],
            lst_marcas:     [],
            lst_categorias: [],
            lst_almacenes:  [],
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.initDataTable();
            this.cargarAlmacenes();
            this.cargarCatalogos();

            const vm = this;
            document.getElementById('modal_consultar_stock')
                .addEventListener('shown.bs.modal', function () {
                    if (vm.tabla) vm.tabla.columns.adjust();
                });
        });
    },
    methods: {
        initDataTable() {
            const vm = this;
            this.tabla = $('#tbl_stock_disponible').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 50,
                ajax: {
                    url: route('ventas.documento.getStockDisponible'),
                    type: 'GET',
                    data(d) {
                        d.talla_id     = vm.filtros.talla_id     || '';
                        d.color_id     = vm.filtros.color_id     || '';
                        d.marca_id     = vm.filtros.marca_id     || '';
                        d.categoria_id = vm.filtros.categoria_id || '';
                        d.almacen_id   = vm.filtros.almacen_id   || '';
                    },
                },
                columns: [
                    { data: 'producto_nombre',  name: 'producto_nombre', searchable: true  },
                    { data: 'color_nombre',     name: 'co.descripcion',  searchable: true  },
                    { data: 'talla_nombre',     name: 't.descripcion',   searchable: true  },
                    { data: 'almacen_nombre',   name: 'al.descripcion',  searchable: true  },
                    { data: 'marca_nombre',     name: 'ma.marca',        searchable: true  },
                    { data: 'categoria_nombre', name: 'ca.descripcion',  searchable: true  },
                    {
                        data: 'stock',
                        name: 'pct.stock',
                        searchable: false,
                        className: 'text-center font-weight-bold',
                        render(data) {
                            const color = data > 0 ? '#1e8449' : '#c0392b';
                            return `<span style="color:${color}">${data}</span>`;
                        },
                    },
                    { data: 'stock_logico', name: 'pct.stock_logico', visible: false, searchable: false },
                ],
                createdRow(row, data) {
                    if (parseInt(data.stock_logico) === 0) {
                        $(row).css('background-color', '#fde8e8');
                    }
                },
                language: {
                    processing:   'Procesando...',
                    search:       'Buscar (producto, color, talla, marca, categoría):',
                    lengthMenu:   'Mostrar _MENU_ registros',
                    info:         'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    infoEmpty:    'Mostrando 0 a 0 de 0 registros',
                    infoFiltered: '(filtrado de _MAX_ total)',
                    zeroRecords:  'No se encontraron resultados.',
                    emptyTable:   'Sin datos disponibles.',
                    paginate: {
                        first:    'Primero',
                        previous: 'Anterior',
                        next:     'Siguiente',
                        last:     'Último',
                    },
                },
                order: [[1, 'asc']],
            });
        },

        async cargarCatalogos() {
            try {
                const [tallas, colores, marcas, categorias] = await Promise.all([
                    this.axios.get(route('utilidades.getTallas')),
                    this.axios.get(route('utilidades.getColores')),
                    this.axios.get(route('utilidades.getMarcas')),
                    this.axios.get(route('utilidades.getCategorias')),
                ]);
                this.lst_tallas     = tallas.data;
                this.lst_colores    = colores.data;
                this.lst_marcas     = marcas.data;
                this.lst_categorias = categorias.data;
            } catch (e) {
                // silent
            }
        },

        async cargarAlmacenes() {
            try {
                const res = await this.axios.get(route('utilidades.getAlmacenes'));
                this.lst_almacenes = res.data;
            } catch (e) {
                // silent
            }
        },

        recargarTabla() {
            if (this.tabla) this.tabla.ajax.reload(null, false);
        },

        limpiarFiltros() {
            this.filtros = {
                talla_id:     null,
                color_id:     null,
                marca_id:     null,
                categoria_id: null,
                almacen_id:   null,
            };
            this.recargarTabla();
        },
    },
};
</script>
