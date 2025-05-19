import {createApp} from 'vue/dist/vue.esm-bundler.js'
import {vAlert, vConfirm} from './libs/alert'
import modalFunc from './libs/modal'
import emitter from './libs/mitt'
import alert from "./components/alert.vue"
// Надо сделать это основным модальным окном и убрать из Form
import modal from "./components/modal.vue"
import octicons from "./components/octicons.vue"
import dropdown from "./components/dropdown.vue"
import formInit from '../../Form/resources/js/init.js'
import menuInit from '../../Menu/resources/js/init.js'
import customInit from '../../../../../backend/resources/js/backend.js'

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

app.provide('msgConfirm', vConfirm)
app.provide('msgAlert', vAlert)

app.config.globalProperties.modal = modalFunc

app.config.globalProperties.emitter = emitter

addComponents({'v-icon': octicons, 'v-alert': alert, 'v-modal': modal, 'v-dropdown': dropdown})

addComponents(formInit.components)

addComponents(menuInit.components)

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



