<template functional>
	<div class="form-check" :class="props.field.inline ? 'form-check-inline': ''">
		<label v-for="el in props.field.options" class="form-check-label mr-2" :key="el.value" v-on:change="console.log($event.target.value); listeners.change($event.target.value)" :class='props.error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="props.field.attr" type="checkbox" :value="props.field.value[el.value]" :checked="el.value == props.field.value">
    		{{el.label}}
    	</label>
    </div>
</template>

