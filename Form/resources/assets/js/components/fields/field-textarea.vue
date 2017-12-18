<template>
    <textarea v-bind="data.attr" :name="data.name">{{data.value}}</textarea>
</template>

<script>
    export default {
        props: [ 'data' ],
    }
</script>