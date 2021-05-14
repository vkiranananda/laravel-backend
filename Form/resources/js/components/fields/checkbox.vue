<template functional>
	<div class="form-check" :class="props.field.inline ? 'form-check-inline': ''">
		<label v-for="el in props.field.options" class="form-check-label me-2" :key="el.value" :class='props.error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="props.field.attr" type="checkbox" :value="el.value"  :disabled="props.field.readonly ? true : false" v-on:input="listeners.change($event.target.value)" :checked="el.value == props.field.value">
    		{{el.label}}
    	</label>
    </div>
</template>

