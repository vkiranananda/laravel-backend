<template>
    <div>
        <form-field-autocomplete v-if="!field['max-items'] || field['max-items'] > value.length" :field="autoField" @select="select"></form-field-autocomplete>

        <div v-for="(el, key) in value" :key="key">
            {{ el.label }}
        </div>
    </div>
</template>

<script>
import FormFieldAutocomplete from './autocomplete.vue'

export default {
    name: "select-search",
    props: ['field'],
    components: {
        FormFieldAutocomplete
    },
    data() {
        return {
            value: []
        }
    },
    computed: {
        autoField() {
            return {type: 'autocomplete', url: this.field.url, 'clear-if-select': true}
        },
    },
    methods: {
        select: function (val) {
            this.value.push(val)
            this.$emit('change', this.value)
            console.log(val)
        }
    }
}
</script>

<style scoped>

</style>




