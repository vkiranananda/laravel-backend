<template>
	<select v-bind="field.attr" :name="field.name" class="form-control" v-on:change="$emit('change', $event.target.value)" :class='error ? "is-invalid" : "" '>
		<option v-for="(el, key) in field.options" :value="el.value" :selected="el.value == field.value" v-bind="el.attr" :key="key">{{el.label}}</option>
    </select>
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
