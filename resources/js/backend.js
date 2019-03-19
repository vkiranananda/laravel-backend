require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';

import uploadForm from '../../Form/resources/js/store/upload';
import editForm from '../../Form/resources/js/store/edit';

const store = new Vuex.Store({
	modules: {
		editForm,
		uploadForm
	} 
});


//module.exports = {store: store}

Vue.prototype.$bus = new Vue();

Vue.prototype.store = store;

// Модуль форм
require('../../Form/resources/js/init.js');
// Меню
require('../../Menu/resources/js/init.js');

// Пользовательский js
require('../../../../../backend/resources/js/backend.js');


const backend = new Vue({
    el: '#backend-body',
});
