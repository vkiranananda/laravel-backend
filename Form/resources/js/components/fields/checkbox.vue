<template>
    <div class="field-checkbox d-flex align-items-center">
        <div>
            <div v-for="el in field.options" class="form-check" :class="field.inline ? 'form-check-inline': ''">
                <label class="form-check-label" :key="el.value"
                       :class='error ? "is-invalid" : ""'>
                    &nbsp;<input class="form-check-input" v-bind="field.attr" type="checkbox" :value="el.value"
                                 :disabled="field.readonly ? true : false"
                                 v-on:input="change(el.value, $event.target.checked)" :checked="value[el.value]">{{
                        el.label
                    }}
                </label>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['field', 'error'],
    computed: {
        value: function () {
            if (this.field.multiselect !== true) {
                return typeof this.field.value !== 'string' ? {} : {[this.field.value]: true}
            } else {
                // multiselect
                // Если не массив, возвращаем пустую структуру
                if (!Array.isArray(this.field.value)) return {}

                let res = {}
                for (let opt of this.field.value) res[opt] = true

                return res
            }
        },
    },
    methods: {
        change: function (key, state) {
            let res
            if (this.field.multiselect !== true) {
                res = state ? key : ''
            } else {
                // multiselect
                let newVal = Object.assign({}, this.value)
                if (state) newVal[key] = true
                else delete newVal[key]
                res = Object.keys(newVal)
            }
            this.$emit('v-change', res)
        }
    }
}
</script>

<style lang="scss">
.field-checkbox {
    min-height: 38px;
}
</style>
