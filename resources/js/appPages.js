require('./bootstrap');

import axios from 'axios';
import Vue from 'vue';
import VueAxios from "vue-axios";
import vSelect from 'vue-select';
import BootstrapVue from 'bootstrap-vue';

window.Vue = require('vue');
var moment = require('moment');
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.post["X-CSRF-TOKEN"] = $('meta[name=csrf-token]').attr("content");
axios.defaults.headers.post["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = location.origin;
Vue.use(VueAxios, axios);
Vue.prototype.$moment = moment;
Vue.prototype.$fechaActual = moment().format("YYYY-MM-DD");

Vue.use(BootstrapVue);


//modulos
Vue.component("ventas-app", require("./views/Ventas/documentos/appVenta.vue").default);
Vue.component("venta-lista", require("./views/Ventas/documentos/Index.vue").default);
// Vue.component("venta-create", require("./views/Ventas/documentos/create.vue").default);
Vue.component("venta-create", () => import("./views/Ventas/documentos/create.vue"));
Vue.component('v-select', vSelect)

const appPages = new Vue({
    el: '#content-system',
});
