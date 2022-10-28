import emitter from './mitt'

export default {
    show: (modalName) => {
        emitter.emit(modalName, {action: 'show'})
    },
    hide: (modalName) => {
        emitter.emit(modalName, {action: 'hide'})
    },
}

