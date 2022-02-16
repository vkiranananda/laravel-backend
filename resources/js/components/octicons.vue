<script>

import octicons from '@primer/octicons'
import {h} from 'vue'

export default {
    props: {
        width: {
            type: String,
        },
        height: {
            type: String,
        },
        className: {
            type: String,
        },
        name: {
            type: String,
            required: true
        },
    },
    render() {
        // Если нет иконки
        if (!octicons[this.name]) {
            console.warn('Icon ' + this.name + ' not found.')
            return
        }

        let params = {}
        if (this.width) params.width = this.width
        if (this.height) params.height = this.height

        return h('span', {
            class: 'octicon-wrapper', innerHTML: octicons[this.name].toSVG(params)
        })
    }
}
</script>

<style lang='scss'>
.octicon {
    margin-bottom: 0.16rem;
    fill: inherit;
}
</style>

