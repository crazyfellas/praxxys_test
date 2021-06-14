

require('./bootstrap');

window.Vue = require('vue').default;

// import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
// import axios from 'axios';
import routes from './routes';

// Vue.use(VueAxios, axios);
Vue.use(VueRouter);
Vue.component('pagination', require('laravel-vue-pagination'));

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
Vue.use(VueToast, {
    position: 'top-right'
});


import Swal from 'sweetalert2'
window.Swal = Swal;



import Vue2Editor from "vue2-editor";
Vue.use(Vue2Editor);

import datePicker from 'vue-bootstrap-datetimepicker';
Vue.use(datePicker);
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';

import moment from 'moment';


Vue.filter('formatDate', function(text){
return moment(text).format('MMMM D YYYY');
});


window.Fire =  new Vue();


Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    components: {
        datePicker
    }
   
});
