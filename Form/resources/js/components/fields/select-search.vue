<template>
    <div class="select-search">
        <div v-show="maxItems == 0 || maxItems > value.length">
            <form-field-autocomplete @select="select" :field="autoField"></form-field-autocomplete>
        </div>

        <div v-for="(el, key) in value" :key="key" class="item border border-primary rounded">
            {{ getLabel(el) }}
            <a href='#' v-on:click.prevent="del(el)" class="delete" v-if="field.readonly != true">&times;</a>
        </div>
    </div>
</template>

<script>
import FormFieldAutocomplete from './autocomplete.vue'

export default {
    name: "select-search",
    props: ['field', 'fields'],
    components: {
        FormFieldAutocomplete
    },
    computed: {
        autoField() {
            return {type: 'autocomplete', url: this.field.url, 'clear-if-select': true}
        },
        maxItems() {
            if (this.field.multiple) return this.field['max-items'] ? this.field['max-items'] : 0
            return 1
        },
        value() {
            if (this.field.multiple) return Array.isArray(this.field.value) ? this.field.value.slice() : []
            else return this.field.value && this.field.value != '' ? [this.field.value] : []
        },
        options() {
            return Array.isArray(this.field.options) ? this.field.options.slice() : []
        },
    },
    methods: {
        select: function (val) {
            let options = this.options
            options.push(val)

            // Добавляем опцию в options
            this.store.dispatch('editForm/setFieldPropByField', {
                value: options,
                field: this.field,
                property: 'options',
            });

            // Если множественный выбор добавляем элемент в конец иначе создаем массив с одним элементом
            let value = this.value
            if (this.field.multiple) value.push(val.value)
            else value = [val.value]
            this.emit(value)
        },
        del: function (el) {
            // Удаляем элемент из массива
            let value = this.value
            this.$delete(value, value.indexOf(el))
            this.emit(value)
        },
        emit: function (value) {
            this.$emit('v-change', this.field.multiple ? value : (value[0] ? value[0] : ''))
        },
        getLabel: function (value) {
            for (let el of this.options) if (el.value == value) return el.label
        }
    }
}
</script>

<style lang="scss">
.select-search {
    >.item {
        position: relative;
        padding: 5px 7px;
        margin: 5px 10px 0 15px;
        display: inline-block;

        .delete {
            position: absolute;
            right: -15px;
            top: -8px;
            font-size: 18px;
            text-align: center;
            color: red;
            text-decoration: none;
            cursor: pointer;
        }
    }
}
</style>
