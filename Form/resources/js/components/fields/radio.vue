<template>
	<div class="form-check" :class="field.inline? 'form-check-inline': ''">
		<label v-for="(el, key) in field.options" class="form-check-label me-2" :key="key" v-on:change="$emit('v-change', $event.target.value)" :class='error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="field.attr" type="radio"  :disabled="field.readonly ? true : false" :value="el.value" :checked="el.value == field.value">
    		{{el.label}}
    	</label>
    </div>
</template>
<script>
export default {
    props: ['field', 'error']
}
</script>
