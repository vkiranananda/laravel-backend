import {createApp} from 'vue/dist/vue.esm-bundler.js'

// Обновляет страницу при history.back()
// Не работает в сафари
if (performance.navigation.type == 2) {
    location.reload(true);
}
// А вот этот код похоже работает в сафари но в хроме нет :).
window.onpopstate = (event) => {
    location.reload(true);
};

const app = createApp({})

import {vAlert, vConfirm} from './libs/alert'
app.provide('msgConfirm', vConfirm)
app.provide('msgAlert', vAlert)

import mitt from 'mitt'
app.config.globalProperties.emitter = mitt()

import alert from "./components/alert.vue"
// Надо сделать это основным модальным окном и убрать из Form
// import modal from "./components/modal.vue"
import octicons from "./components/octicons.vue"
addComponents({'v-icon': octicons, 'v-alert': alert})

import formInit from '../../Form/resources/js/init.js'
addComponents(formInit.components)

import menuInit from '../../Menu/resources/js/init.js'
addComponents(menuInit.components)

import customInit from '../../../../../backend/resources/js/backend.js'
addComponents(customInit.components)

// Генерим массив для Vue.
function addComponents(components) {
    if (components) {
        for (let key in components) {
            app.component(key, components[key])
        }
    }
}
app.config.globalProperties.msgConfirm = vConfirm
app.config.globalProperties.msgAlert = vAlert
app.mount('#backend-body')



