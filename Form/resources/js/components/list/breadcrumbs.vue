<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" :class="el.url ? '' : ' active'" v-for="el in data">
                <a :href="el.url" v-if="el.url">{{ el.label }}</a>
                <template v-else>{{ el.label }}</template>
            </li>
        </ol>
    </nav>
</template>

<script>
export default {
    props: ['data']
}
</script>
