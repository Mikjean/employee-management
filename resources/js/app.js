require('./bootstrap');

window.Vue = require('vue').default;

import router from './router'
import store from './store'

const app = new Vue({
    el: '#app',
    router:router,
    store:store
});