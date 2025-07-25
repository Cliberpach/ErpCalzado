require('./bootstrap');

import Vue from 'vue';

window.Vue = Vue;

Vue.component('ventas-component', require('./components/caja/VentasComponent.vue').default);
// Vue.component('modal-cliente', require('./components/ventas/ModalCliente.vue').default);
Vue.component('v-select', () => import('vue-select'));

const app = new Vue({
    el: '#app',
});
