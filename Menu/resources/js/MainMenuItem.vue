<template functional>
    <li :class="[props.item['space-bottom'] ? 'pb-'+props.item['space-bottom'] : '', props.item['space-top'] ? 'pt-'+props.item['space-top'] : '', props.item['space-left'] ? 'pl-'+props.item['space-left'] : '']">
        <hr v-if="props.item.separator === true || props.item.type === 'separator'">

        <span v-if="props.item.type === 'title'" class="text-white title">
            <v-icon v-if='props.item.icon' :name="props.item.icon" class="me-2"/>
            {{ props.item.label }}
        </span>
        <a v-else-if="props.item.url != undefined" class="text-light" :href="props.item.url">
            <v-icon v-if='props.item.icon' :name="props.item.icon" class="me-2"/>
            {{ props.item.label }}
        </a>
    </li>
</template>
<style scoped>
.octicon-wrapper {
    width: 20px;
    display: inline-block;
    color: white;
}

</style>
