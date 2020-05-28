<template functional>
	<li :class="[props.item['space-bottom'] ? 'pb-'+props.item['space-bottom'] : '', props.item['space-top'] ? 'pt-'+props.item['space-top'] : '']">
		<hr v-if="props.item.separator === true">
		<a v-else-if="props.item.url != undefined" class="text-light" :href="props.item.url">
			<v-icon v-if='props.item.icon' :name="props.item.icon" class="mr-2" />
			{{ props.item.label }}
		</a>
		<slot></slot>
	</li>
</template>
<style scoped>
    .octicon-wrapper {
        width: 20px;
        display: inline-block;
    }
</style>
