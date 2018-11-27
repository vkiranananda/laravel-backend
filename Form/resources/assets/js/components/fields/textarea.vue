<template>
    <textarea v-bind="field.attr" :class='error ? "is-invalid" : "" ' v-on:input="$emit('change', $event.target.value)" class="form-control">{{field.value}}</textarea>
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