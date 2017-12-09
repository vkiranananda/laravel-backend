require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'vuex';


//https://github.com/SortableJS/Vue.Draggable
// import draggable from 'vuedraggable';
// require('../../../Form/resources/assets/js/vue-forms.js');
// Vue.component('field-block', require('../../../Form/resources/assets/js/components/FieldBlock.vue'));



import uploadStore from '../../../Upload/resources/assets/js/store';

const store = new Vuex.Store({
	modules: {
		uploadStore
	} 
});

console.log(store.state.uploadStore);

require('../../../Upload/resources/assets/js/init-components.js');
Vue.component('modal', require('../../../Site/resources/assets/js/Modal.vue'));

const backend = new Vue({
    el: '#backend-body',
    store
});


require('../../../Form/resources/assets/js/gui.js');
require('../../../Category/resources/assets/js/tree.js');
