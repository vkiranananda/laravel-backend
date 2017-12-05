require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'vuex';
// import { mapState } from 'vuex';



//Vue.use(Vuex);


// require('../../../Form/resources/assets/js/vue-forms.js');


import uploadStore from '../../../Upload/resources/assets/js/Store';
Vue.component('upload-file', require('../../../Upload/resources/assets/js/components/UploadFile.vue'));
Vue.component('upload-edit-file', require('../../../Upload/resources/assets/js/components/EditFile.vue'));
Vue.component('modal', require('../../../Site/resources/assets/js/Modal.vue'));
// Vue.component('field-block', require('../../../Form/resources/assets/js/components/FieldBlock.vue'));


const store = new Vuex.Store({
    modules: {
    	uploadStore
    }
});

const backend = new Vue({
    el: '#backend-body',
    store
});


require('../../../Upload/resources/assets/js/uploads.js');
require('../../../Form/resources/assets/js/gui.js');

require('../../../Category/resources/assets/js/tree.js');
