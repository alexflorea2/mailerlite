import SimpleBox from '@/components/core/SimpleBox';
import StandardBox from '@/components/core/StandardBox';
import Sidebar from '@/components/core/Sidebar';
import Alert from '@/components/core/Alert';

export default {
    install (Vue) {
        Vue.component('ui-sidebar', Sidebar);
        Vue.component('ui-simple-box', SimpleBox);
        Vue.component('ui-box', StandardBox);
        Vue.component('ui-alert', Alert);
    }
}

export { SimpleBox, Alert, Sidebar };