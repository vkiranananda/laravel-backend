<template>
    <select v-bind="field.attr" :name="field.name" class="form-control"
            v-on:change="$emit('v-change', $event.target.value)" :class='classes'>
        <option v-for="(el, key) in field.options" :value="el.value" :disabled="field.readonly ? true : false"
                :selected="el.value == field.value" v-bind="el.attr" :key="key">{{ el.label }}
        </option>
    </select>
</template>
<script>
export default {
    props: ['field', 'error'],
    computed: {
        classes: function () {
            let res;
            if (this.error) res = "is-invalid"
            if (this.field.size === 'small') res += ' form-select-sm'

            return res
        }
    }
}
</script>
