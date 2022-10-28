import emitter from './mitt'

export function vAlert(msg, func = undefined, btns = undefined) {
    emitter.emit('AlertModalShow', {type: 'alert', msg, func, btns})
}

export function vConfirm (msg, func = undefined, btns = undefined) {
    emitter.emit('AlertModalShow', {type: 'confurm', msg, func, btns})
}
