<template>
    <input type="text" v-mask="config" :value="display" @input="onInput">
</template>

<script>
import mask from './directive.js'
import tokens from './tokens.js'
import masker from './masker.js'

export default {
    name: 'TheMask',
    created() {
        console.log(this.value)
    },
    props: {
        modelValue: [String, Number],
        mask: {
            type: [String, Array],
            required: true
        },
        masked: { // by default emits the value unformatted, change to true to format with the mask
            type: Boolean,
            default: false // raw
        },
        tokens: {
            type: Object,
            default: () => tokens
        }
    },
    directives: {mask},
    data() {
        return {
            lastValue: null, // avoid unecessary emit when has no change
            display: this.modelValue
        }
    },
    watch: {
        value(newValue) {
            if (newValue !== this.lastValue) {
                this.display = newValue
            }
        },
        masked() {
            this.refresh(this.display)
        }
    },
    computed: {
        config() {
            return {
                mask: this.mask,
                tokens: this.tokens,
                masked: this.masked
            }
        }
    },
    methods: {
        onInput(e) {
            if (e.isTrusted) return // ignore native event
            this.refresh(e.target.value)
        },

        refresh(value) {
            this.display = value
            let newValue = masker(value, this.mask, this.masked, this.tokens)
            if (newValue !== this.lastValue) {
                this.lastValue = newValue
                this.$emit('update:modelValue', newValue)
            }
        }
    }
}
</script>
