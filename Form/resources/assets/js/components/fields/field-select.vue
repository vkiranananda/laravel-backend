<template>
	<select v-bind="data.attr" :name="data.name">
		<option v-for="(el, key) in data.options" :value="el.value" :selected="el.value == data.value" v-bind="el.attr" :key="key">{{el.label}}</option>
    </select>
</template>

<script>
    export default {
        props: [ 'data' ],
    }
</script>
