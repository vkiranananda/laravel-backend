<template>
    <div class="field-block d-flex align-items-center">
        <div class="field">
            <component v-if="component" :is="'form-field-'+component" :field="fieldChanged" :fields="fields" :error='error'
                       v-on:v-change="$emit('v-change', $event)"></component>
        </div>
        <v-icon v-if="field.protected === true && blocked" name="key" class="back" @click="unblock"/>
    </div>
</template>

<script>
    import FormFieldInput from './input.vue'
    import FormFieldDate from './date.vue'
    import FormFieldTextarea from './textarea.vue'
    // import FormFieldMce from './mce.vue'
    import FormFieldFiles from './files.vue'
    import FormFieldRadio from './radio.vue'
    import FormFieldSelect from './select.vue'
    import FormFieldCheckbox from './checkbox.vue'
    import FormFieldMultiselect from './multiselect.vue'
    import FormFieldMask from './mask.vue'
    import FormFieldEditor from './editor.vue'
    import FormFieldMarkdown from './markdown.vue'
    import FormFieldAutocomplete from './autocomplete.vue'
    import FormFieldSelectSearch from './select-search.vue'


    const cloneDeep = require('clone-deep')

    export default {
        props: {
            field: {},
            fields: {},
            error: {
                default: '',
                type: String
            }
        },
        data() {
            return {
                blocked: true
            }
        },
        components: {
            FormFieldInput,
            FormFieldDate,
            FormFieldTextarea,
            // FormFieldMce,
            FormFieldFiles,
            FormFieldRadio,
            FormFieldSelect,
            FormFieldCheckbox,
            FormFieldMultiselect,
            FormFieldMask,
            FormFieldEditor,
            FormFieldMarkdown,
            FormFieldAutocomplete,
            FormFieldSelectSearch
        },
        computed: {
            component() {
                if (['text', 'email', 'password', 'tel'].indexOf(this.field.type) != -1) return 'input';
                if (['gallery', 'files'].indexOf(this.field.type) != -1) return 'files';

                return this.field.type;
            },
            fieldChanged() {
                if (this.field.protected === true && this.blocked) {
                    let res = cloneDeep(this.field)
                    if (res.attr == undefined) res.attr = {}
                    res.readonly = true
                    return res
                } else return this.field
            }
        },
        methods: {
            unblock: function () {
                let msg = (this.field['protected-message']) ? this.field['protected-message'] : 'Разблокировать элемент?'
                this.msgConfirm(msg, () => {
                    this.blocked = false
                })
            }
        }
    }
</script>

<style lang='scss'>
    .field-block {
        .field {
            width: 100%;
        }

        .protected {
            cursor: pointer;
            padding-left: 12px;
        }
    }
</style>
