<template functional>
	<div class="form-check form-check-inline">
		<label v-for="(el, key) in props.field.options" class="form-check-label mr-2" :key="key" v-on:change="listeners.change($event.target.value)"  :class='props.error ? "is-invalid" : "" '>
    		&nbsp;<input v-bind="props.field.attr" type="radio" :value="el.value" :checked="el.value == props.field.value">
    		{{el.label}}
    	</label>
    </div>
</template>
