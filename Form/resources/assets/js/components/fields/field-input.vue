<template>
    <input v-bind="data.attr" :type="data.type" :value="data.value" :name="data.name">
</template>

<script>
    export default {
        props: [ 'data' ],
    }
</script>