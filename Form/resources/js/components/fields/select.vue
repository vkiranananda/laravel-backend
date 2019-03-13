<template functional>
	<select v-bind="props.field.attr" :name="props.field.name" class="form-control" v-on:change="listeners.change($event.target.value)" :class='props.error ? "is-invalid" : "" '>
		<option v-for="(el, key) in props.field.options" :value="el.value" :selected="el.value == props.field.value" v-bind="el.attr" :key="key">{{el.label}}</option>
    </select>
</template>
