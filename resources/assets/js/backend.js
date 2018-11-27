require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'vuex';


import uploadStore from '../../../Upload/resources/assets/js/store';
import editForm from '../../../Form/resources/assets/js/store/edit';


const store = new Vuex.Store({
	modules: {
		editForm,
		// formEdit,
	} 
});


//module.exports = {store: store}

Vue.prototype.$bus = new Vue();

Vue.prototype.store = store;

require('../../../Form/resources/assets/js/init-components.js');
require('../../../Upload/resources/assets/js/init-components.js');


Vue.component('modal', require('../../../Site/resources/assets/js/Modal.vue'));

const backend = new Vue({
    el: '#backend-body',
    // data: {
    // 	bus: vueBus
    // },
//    store,
    // bus: vueBus
});


require('../../../Form/resources/assets/js/gui.js');
require('../../../Category/resources/assets/js/tree.js');
