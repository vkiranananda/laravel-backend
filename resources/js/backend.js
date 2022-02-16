window.$ = window.jQuery = require('jquery')
require('bootstrap')
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


// require('tether');
import mitt from 'mitt'
const emitter = mitt()
// import store from "../../Form/resources/js/store/edit";
// // Подключаем быстрые функции для работы с алертами
import { vAlert, vConfirm } from './libs/alert'
// Обновляет страницу при history.back()
// Не работает в сафари
if (performance.navigation.type == 2) {
    location.reload(true);
}
// А вот этот код похоже работает в сафари но в хроме нет :).
window.onpopstate = (event) => {
    location.reload(true);
};

// Надо убрать
window.emitter = emitter

import { createApp } from 'vue'

const backend = createApp({})

backend.provide('msgConfirm', vConfirm)
backend.provide('msgAlert', vAlert)

backend.config.globalProperties.emitter = emitter
backend.config.globalProperties.msgConfirm = vConfirm
backend.config.globalProperties.msgAlert = vAlert
// backend.config.globalProperties.store = store

window.backend = backend

backend.component('v-icon', require('./components/octicons').default)
backend.component('v-alert', require('./components/alert').default)
backend.component('v-confirm', require('./components/modal').default)


// Модуль форм
require('../../Form/resources/js/init.js');
// // Меню
require('../../Menu/resources/js/init.js');
// // Пользовательский js
// require('../../../../../backend/resources/js/backend.js');

backend.mount('#backend-body')
// setTimeout(vAlert, 5000)
