require('./bootstrap');

import axios from 'axios';
import Vue from 'vue';
import VueAxios from "vue-axios";
import vSelect from 'vue-select';
window.Vue = require('vue');
var moment = require('moment');
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
axios.defaults.headers.post["X-CSRF-TOKEN"] = $('meta[name=csrf-token]').attr("content");
axios.defaults.headers.post["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.baseURL = location.origin;
Vue.use(VueAxios, axios);
Vue.prototype.$moment = moment;
Vue.prototype.$fechaActual = moment().format("YYYY-MM-DD");

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//modulos
Vue.component("ventas-app", require("./views/Ventas/documentos/appVenta.vue").default);
Vue.component("venta-lista", require("./views/Ventas/documentos/Index.vue").default);
Vue.component("venta-create", require("./views/Ventas/documentos/create.vue").default);
Vue.component('v-select', vSelect)

//  Vue.component("modal-ventas",require("./views/Ventas/ModalVentas.vue").default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const appPages = new Vue({
  el: '#content-system',
});