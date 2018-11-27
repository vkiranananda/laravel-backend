<template>
	<div class="form-check form-check-inline">
		<label v-for="(el, key) in field.options" class="form-check-label mr-2" :key="key" v-on:change="$emit('change', $event.target.value)"  :class='error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="field.attr" type="radio" :value="el.value" :name="field.name" :checked="el.value == field.value">
    		{{el.label}}
    	</label>
    </div>
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
