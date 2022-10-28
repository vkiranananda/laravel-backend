<template>
    <the-mask v-bind="field.attr" v-model="value"
        :mask="field.mask" class="form-control"   :disabled="field.readonly ? true : false" :class='error ? "is-invalid" : "" ' />
</template>

<script>
	//v-on:input="change" field.value
    import TheMask from './libs/mask/component.vue'
    import fecha from "fecha";

    export default {
        props: [ 'field', 'error'],
        components: {TheMask},
        computed: {
            value: {
                get: function () {
                    return this.field.value
                },
                set: function (value) {
                    this.$emit('v-change', value)
                }
            },
        },
    }
</script>
