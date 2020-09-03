window.axios = require('axios');

import Vue from 'vue'
import App from './App.vue'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faFolder, faWrench, faFileContract, faUsers, faUserPlus, faStream, faAngleLeft, faAngleRight } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
library.add(faFolder, faWrench, faFileContract, faUsers, faUserPlus, faStream, faAngleLeft, faAngleRight)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.config.productionTip = false

import FrontRouter from "@/router/front";

import UiCore from './UiCore';
Vue.use(UiCore);

import BaseLayout from "@/layouts/Base.vue";
import ExtendedLayout from "@/layouts/Extended.vue";

Vue.component('default-layout', ExtendedLayout);
Vue.component('base-layout', BaseLayout);

new Vue({
    el: "#app",
    router: FrontRouter,
    render: (h) => h(App)
});