<template>
    <textarea v-bind="field.attr" :class='error ? "is-invalid" : "" '  class="form-control" v-model="value"></textarea>
</template>
<script>

    export default {
        props: ['field', 'error'],
        computed: {
            value: {
            	get() {	return this.field.value },
            	set(val) { this.$emit('change', val) }
            }
        },
    }
</script>
