<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="ibox ">
                    <div class="ibox-content" style="font-size: 10px;">
                        <data-table :resource="resource">
                            <tr slot="heading">
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center" style="width: 20%;">PRODUCTO</th>
                                <th class="text-center" style="width: 10%;">CATEGORIA</th>
                                <th class="text-center" style="width: 10%;">MARCA</th>
                                <th class="text-center" style="width: 10%;">MEDIDA</th>
                                <th class="text-center" style="width: 10%;">STOCK INI.</th>
                                <th class="text-center" style="width: 10%;">COMPRAS</th>
                                <th class="text-center" style="width: 10%;">INGRESOS</th>
                                <th class="text-center" style="width: 10%;">VENTAS</th>
                                <th class="text-center" style="width: 10%;">DEVOLUCIONES</th>
                                <th class="text-center" style="width: 10%;">SALIDAS</th>
                                <th class="text-center" style="width: 10%;">STOCK</th>
                            </tr>
                            <tr slot-scope="{ index, row }">
                                <td class="text-center">{{ index }}</td>
                                <td class="text-center">{{ row.nombre }}</td>
                                <td class="text-center">{{ row.categoria }}</td>
                                <td class="text-center">{{ row.marca }}</td>
                                <td class="text-center">{{ row.medida }}</td>
                                <td class="text-center">{{ row.STOCKINI }}</td>
                                <td class="text-center">{{ row.COMPRAS }}</td>
                                <td class="text-center">{{ row.INGRESOS }}</td>
                                <td class="text-center">{{ row.VENTAS }}</td>
                                <td class="text-center">{{ row.DEVOLUCIONES }}</td>
                                <td class="text-center">{{ row.SALIDAS }}</td>
                                <td class="text-center">{{ row.STOCK }}</td>
                            </tr>
                            <tr slot="message">
                                <td colspan="12" class="text-center">No hay datos para mostrar</td>
                            </tr>
                        </data-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import DataTable from '../../components/DataTableKardex.vue'
    export default {
        components:{DataTable},
        data() {
            return {
                resource: 'consultas/kardex/producto',
            }
        },
        created() {
            
        },
        methods: {
            
        }
    }
</script>