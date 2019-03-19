<template functional>
	<li :class="[props.item['space-bottom'] ? 'pb-'+props.item['space-bottom'] : '', props.item['space-top'] ? 'pt-'+props.item['space-top'] : '']">
		<hr v-if="props.item.separator === true">
		<a v-else-if="props.item.url != undefined" class="text-light" :href="props.item.url">
			<span v-if='props.item.icon != undefined && props.item.icon != ""' :class="'mr-2 octicon octicon-'+props.item.icon"></span>
			{{ props.item.label }}
		</a>
		<slot></slot>
	</li>
</template>