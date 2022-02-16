<template>
    <textarea v-bind="field.attr" :class='error ? "is-invalid" : "" '  class="form-control"  :disabled="field.readonly ? true : false" v-model="value"></textarea>
</template>
<script>

    export default {
        props: ['field', 'error'],
        computed: {
            value: {
            	get() {	return this.field.value },
            	set(val) { this.$emit('v-change', val) }
            }
        },
    }
</script>
