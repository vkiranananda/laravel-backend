<template>
	<div>
		<show-uploads-button url="data.filesUrl" v-if="data.upload"></show-uploads-button>
    	<textarea v-bind="data.attr" :name="data.name" data-role="tinyMCE" v-html="data.value"></textarea>
	</div>
</template>

<script>
    export default {
        props: [ 'data' ],
    }
</script>