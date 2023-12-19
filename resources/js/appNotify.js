/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import vSelect from 'vue-select';
import ElementUI from 'element-ui'
import Axios from 'axios'

window.Vue = require('vue');
var moment = require('moment');
import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'
locale.use(lang)

ElementUI.Select.computed.readonly = function () {
    const isIE = !this.$isServer && !Number.isNaN(Number(document.documentMode));
    return !(this.filterable || this.multiple || !isIE) && !this.visible;
};

export default ElementUI;

//Vue.use(ElementUI)
Vue.use(ElementUI, { size: 'small' })
Vue.prototype.$eventHub = new Vue()
Vue.prototype.$http = Axios;
Vue.prototype.$moment = moment;
Vue.prototype.$fechaActual = moment().format("YYYY-MM-DD");
Vue.prototype.$fechaStartMhont= moment().startOf('month').format('YYYY-MM-DD');

 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */

 // const files = require.context('./', true, /\.vue$/i)
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('notify-component', require('./components/layout/notifyUser.vue').default);
Vue.component('kardex-products', require('./views/kardex/products.vue').default);
 Vue.component('v-select', vSelect)

 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */

 const appNotify = new Vue({
     el: '#appNotify',
 });

const appTables = new Vue({
    el: '#appTables',
});