<template>
    <div>
        <div class="row justify-content-center align-items-end">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="control-label">Fecha inicio </label>
                    <el-date-picker v-model="search.d_start" type="date" style="width: 100%;" placeholder="Buscar"
                        value-format="yyyy-MM-dd" @change="changeDisabledDates">
                    </el-date-picker>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="control-label">Fecha término</label>

                    <el-date-picker v-model="search.d_end" type="date" style="width: 100%;" placeholder="Buscar"
                        value-format="yyyy-MM-dd" :picker-options="pickerOptionsDates" @change="changeEndDate">
                    </el-date-picker>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <el-button class="submit" type="primary" @click.prevent="getRecordsByFilter"
                        :loading="loading_submit" icon="el-icon-search">Buscar</el-button>
                </div>
            </div>
            <div class="col-12">
                <vue-toastr ref="toastr"></vue-toastr>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <input placeholder="Buscar por producto" v-model="search.input" class="form-control" type="text" />
                </div>
            </div>
            <div class="col-12 col-md-4 align-items-center d-flex">
                <el-radio v-model="radio" label="0">Stock 0</el-radio>
                <el-radio v-model="radio" label="1">Stock mayor a 0</el-radio>
            </div>
            <div class="col-12 col-md-2">
                <el-button class="submit" type="primary" icon="el-icon-download" @click.prevent="downloadExcel">
                    Descargar Excel
                </el-button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive" id="scroll2" style="overflow-x:auto;">
                    <table class="table">
                        <thead>
                            <slot name="heading"></slot>
                        </thead>
                        <tbody>
                            <slot v-for="(row, index) in records" :row="row" :index="customIndex(index)"></slot>
                            <slot v-if="records.length == 0" name="message"></slot>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination @current-change="getRecords" layout="total, prev, pager, next"
                            :total="pagination.total" :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page">
                        </el-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
import queryString from 'query-string'
import $ from 'jquery'
import { totalmem } from 'os'
import VueToastr from "../components/VueToastr.vue"
export default {
    components: {
        VueToastr
    },
    props: {
        resource: String,
    },
    data() {
        return {
            loading_submit: false,
            records: [],
            pagination: {},
            search: {
                input: '',
                d_start: null,
                d_end: null,
                stock: '0'
            },
            pickerOptionsDates: {
                disabledDate: (time) => {
                    time = moment(time).format('YYYY-MM-DD')
                    return this.search.d_start > time
                }
            },
            radio: '0'
        }
    },
    watch: {
        radio(value) {
            this.search.stock = value;
            this.reloadDatos();
        },
        search:{
            handler(value){
                if(value.input==""){
                    this.$nextTick(this.reloadDatos);
                }
            },
            deep:true
        }
    },
    created() {
        this.search = {
            input: "",
            d_start: this.$fechaStartMhont,
            d_end: this.$fechaActual,
            stock: this.radio
        }
        this.reloadDatos();
    },
    methods: {
        reloadDatos() {
            this.initForm()
            this.$eventHub.$on('reloadData', () => {
                this.getRecords()
            });
        },
        initForm() {
            this.getRecordsByFilter();
        },
        getRecords() {
            return this.$http.get(`/${this.resource}/getTable?${this.getQueryParameters()}`).then((response) => {
                let pagination = {
                    current_page: response.data.current_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                }
                this.records = response.data.data
                this.pagination = pagination
                this.pagination.per_page = parseInt(response.data.per_page)
                this.loading_submit = false
            });
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page,
                limit: this.limit,
                ...this.search
            })
        },
        customIndex(index) {
            return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1
        },
        changeDisabledDates() {
            this.search.date_of_issue = null
            if (this.search.d_end < this.search.d_start) {
                this.search.d_end = this.search.d_start
            }
        },
        changeEndDate() {
            this.search.date_of_issue = null
        },
        async getRecordsByFilter() {
            if (this.search.d_start != null && this.search.d_end != null) {
                this.loading_submit = await true
                await this.getRecords()
                this.loading_submit = await false
            } else {
                this.$toastr.e(
                    "Error\n Completar los campos"
                );
            }
        },
        downloadExcel() {

            let url = route('consultas.kardex.producto.DonwloadExcel') + '?' + this.getQueryParameters().split(' ').join("&");
            window.open(url,'_blank');
        }
    },
    async mounted() {
        // await this.getRecords()
        this.$toastr.defaultTimeout = 10000; // default timeout : 5000
        this.$toastr.defaultClassNames = ["animated", "zoomInUp"];
        this.$toastr.defaultClassNames = ["animated", "bounceInLeft"];
        this.$toastr.defaultPosition = "toast-top-right";
        console.log(this.$fechaActual);
        console.log(this.$fechaStartMhont)
    }
}
</script>