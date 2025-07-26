import Vue from 'vue'
window.Vue = Vue

require('./bootstrap');

Vue.component('notify-component', () => import('./components/layout/notifyUser.vue'));

const appNotify = new Vue({
    el: '#appNotify',
});

