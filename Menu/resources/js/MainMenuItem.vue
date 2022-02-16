<template>
    <li :class="[item['space-bottom'] ? 'pb-'+item['space-bottom'] : '', item['space-top'] ? 'pt-'+item['space-top'] : '', item['space-left'] ? 'pl-'+item['space-left'] : '']">
        <hr v-if="item.separator === true || item.type === 'separator'">

        <span v-if="item.type === 'title'" class="text-white title">
            <v-icon v-if='item.icon' :name="item.icon" class="me-2"/>
            {{ item.label }}
        </span>
        <a v-else-if="item.url != undefined" class="text-light" :href="item.url">
            <v-icon v-if='item.icon' :name="item.icon" class="me-2"/>
            {{ item.label }}
        </a>
        <slot></slot>
    </li>
</template>

<script>
export default {
    props: ['item']
}
</script>
<style scoped>
.octicon-wrapper {
    width: 20px;
    display: inline-block;
    color: white;
}
</style>
