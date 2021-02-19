require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';

import editForm from '../../Form/resources/js/store/edit';
// Подючаем быстрые фонкции для работы с алертами
import alert from './libs/alert'

const store = new Vuex.Store({
    modules: {
        editForm,
    }
});

// Обновляет страницу при history.back()
// Не работает в сафари
if (performance.navigation.type == 2) {
    location.reload(true);
}
// А вот этот код похоже работает в сафари но в хроме нет :).
window.onpopstate = (event) => {
    location.reload(true);
};

//module.exports = {store: store}

Vue.prototype.$bus = window.bus = new Vue()

Vue.prototype.store = store;

// Подключаем иконки они должы быть доступны всем.
Vue.component('v-icon', require('./components/octicons.vue').default)
// Модальные окна
Vue.component('v-modal', require('./components/modal').default)
// Alert
Vue.component('v-alert', require('./components/alert').default)
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
