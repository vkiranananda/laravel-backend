<template>
    <input v-bind="field.attr" :type="field.type" :value="field.value"
      v-on:input="$emit('v-change', $event.target.value)" :disabled="field.readonly ? true : false" class="form-control"  :class='error ? "is-invalid" : "" '>
</template>
<script>
export default {
    props: ['field', 'error']
}
</script>
