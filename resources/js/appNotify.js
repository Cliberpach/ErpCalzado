import Vue from 'vue'
window.Vue = Vue

require('./bootstrap');
// import ElementUI from 'element-ui'
import Axios from 'axios'

var moment = require('moment');
// import lang from 'element-ui/lib/locale/lang/es'
// import locale from 'element-ui/lib/locale'
// locale.use(lang)

// ElementUI.Select.computed.readonly = function () {
//     const isIE = !this.$isServer && !Number.isNaN(Number(document.documentMode));
//     return !(this.filterable || this.multiple || !isIE) && !this.visible;
// };

// export default ElementUI;

// Vue.use(ElementUI, { size: 'small' })
Vue.prototype.$eventHub = new Vue()
Vue.prototype.$http = Axios;
Vue.prototype.$moment = moment;
Vue.prototype.$fechaActual = moment().format("YYYY-MM-DD");
Vue.prototype.$fechaStartMhont= moment().startOf('month').format('YYYY-MM-DD');


Vue.component('notify-component', require('./components/layout/notifyUser.vue').default);
//Vue.component('kardex-products', require('./views/kardex/products.vue').default);

const appNotify = new Vue({
    el: '#appNotify',
});

const appTables = new Vue({
    el: '#appTables',
});
