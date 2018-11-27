<template>
    <input v-bind="field.attr" :type="field.type" :value="field.value"
      v-on:input="$emit('change', $event.target.value)" class="form-control"  :class='error ? "is-invalid" : "" '>
</template>

<script>
    export default {
        props: { 
        	field: {},
        	error: {
        		type: String,
        		default: ''
        	} 
        }
    }
</script>