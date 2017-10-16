<template>
    <span class="form-control-feedback Forms-error-text">
        <slot></slot>
    </span>
</template>

<script>
    export default {
        props: ['errors'],
    }
</script>
