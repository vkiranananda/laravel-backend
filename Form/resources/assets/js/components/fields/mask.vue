<template>
    <the-mask v-bind="field.attr" :value="field.value" @input="change"
        :mask="field.mask" class="form-control"  :class='error ? "is-invalid" : "" ' />
</template>

<script>
	//v-on:input="change" field.value
	import {TheMask} from 'vue-the-mask'

    export default {
        props: [ 'field', 'error'],
        components: {TheMask},
        methods: {
        	change: function(data) {
        		this.$emit('change', data)
        	}
        }
    }
</script>