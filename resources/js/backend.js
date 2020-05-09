require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';

import editForm from '../../Form/resources/js/store/edit';

const store = new Vuex.Store({
    modules: {
        editForm,
    }
});


//module.exports = {store: store}

Vue.prototype.$bus = window.bus = new Vue()

Vue.prototype.store = store;

// Подключаем иконки они должы быть доступны всем.
Vue.component('v-icon', require('./components/octicons.vue').default)
// Модальные окна
Vue.component('v-modal', require('./components/modal').default)
// Alert
Vue.component('v-alert', require('./components/alert').default)
// Подючаем быстрые фонкции для работы с алертами
import alert from './libs/alert'
window.vAlert = alert.vAlert
window.vConfirm = alert.vConfirm


// Модуль форм
require('../../Form/resources/js/init.js');
// Меню
require('../../Menu/resources/js/init.js');

// Пользовательский js
require('../../../../../backend/resources/js/backend.js');


const backend = new Vue({
    el: '#backend-body',
});
