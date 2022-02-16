<template>
	<div class="form-check" :class="field.inline ? 'form-check-inline': ''">
		<label v-for="el in field.options" class="form-check-label me-2" :key="el.value" :class='error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="field.attr" type="checkbox" :value="el.value"  :disabled="field.readonly ? true : false" v-on:input="$emit('v-change', $event.target.value)" :checked="el.value == field.value">
    		{{el.label}}
    	</label>
    </div>
</template>
<script>
export default {
    props: ['field']
}
</script>

