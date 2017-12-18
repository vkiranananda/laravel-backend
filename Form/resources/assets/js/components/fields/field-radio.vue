<template>
	<div>
		<label v-for="(el, key) in data.options" class="form-check-label" :key="key">
    		&nbsp;<input v-bind="data.attr" type="radio" :value="el.value" :name="data.name" :checked="el.value == data.value">
    		{{el.label}}
    	</label>
    </div>
</template>

<script>
    export default {
        props: [ 'data' ],
    }
</script>
