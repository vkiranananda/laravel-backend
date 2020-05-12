exports.vAlert = function (msg, func = undefined, btns = undefined) {
    window.bus.$emit('AlertModalShow', {type: 'alert', msg, func, btns})
}

exports.vConfirm = function (msg, func = undefined, btns = undefined) {
    window.bus.$emit('AlertModalShow', {type: 'confurm', msg, func, btns})
}
