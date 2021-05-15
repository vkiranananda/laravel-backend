require('./bootstrap');


import Vue from 'vue'
import Vuex from 'vuex'
import mitt from 'mitt'

const emitter = mitt()

Vue.use(Vuex)


import editForm from '../../Form/resources/js/store/edit';
// Подключаем быстрые функции для работы с алертами
import { vAlert, vConfirm} from './libs/alert'

const store = new Vuex.Store({
    modules: {
        editForm,
    }
})

// Обновляет страницу при history.back()
// Не работает в сафари
if (performance.navigation.type == 2) {
    location.reload(true);
}
// А вот этот код похоже работает в сафари но в хроме нет :).
window.onpopstate = (event) => {
    location.reload(true);
};

Vue.prototype.store = store;
Vue.prototype.msgAlert = vAlert;
Vue.prototype.msgConfirm = vConfirm;
Vue.prototype.emitter = emitter

window.emitter = emitter

// Подключаем иконки они должы быть доступны всем.
Vue.component('v-icon', require('./components/octicons.vue').default)
// Модальные окна
Vue.component('v-modal', require('./components/modal').default)
// Alert
Vue.component('v-alert', require('./components/alert').default)


// Модуль форм
require('../../Form/resources/js/init.js');
// Меню
require('../../Menu/resources/js/init.js');

// Пользовательский js
require('../../../../../backend/resources/js/backend.js');

const backend = new Vue({
    el: '#backend-body'
    // store
});

window.backend = backend
