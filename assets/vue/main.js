import 'core-js/features/promise'
import 'core-js/features/string'
import 'core-js/features/array'
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from './views/App'
import router from './router'
import store from "./store";
import mixins from './components/mixins'
import './components/filters'


Vue.use(BootstrapVue);

Vue.mixin(mixins);

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: {
        App,
    }
});
