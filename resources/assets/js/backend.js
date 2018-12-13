require('./bootstrap');

window.Vue = require('vue');
import Vuex from 'vuex';


import uploadForm from '../../../Form/resources/assets/js/store/upload';
import editForm from '../../../Form/resources/assets/js/store/edit';


const store = new Vuex.Store({
	modules: {
		editForm,
		uploadForm
	} 
});


//module.exports = {store: store}

Vue.prototype.$bus = new Vue();

Vue.prototype.store = store;

require('../../../Form/resources/assets/js/init-components.js');

Vue.component('the-left-menu', require('../../../Menu/resources/assets/js/components/left-menu.vue'));


const backend = new Vue({
    el: '#backend-body',
    // data: {
    // 	bus: vueBus
    // },
//    store,
    // bus: vueBus
});

require('../../../Category/resources/assets/js/tree.js');
