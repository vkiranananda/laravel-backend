<template>
    <div class="field-block d-flex align-items-center">
        <div class="field">
            <component v-if="component" :is="'form-field-'+component" :field="fieldChanged" :error='error' v-on:change="$emit('change', $event)"></component>
        </div>
        <div v-if="field.protected === true && blocked"  class="protected octicon octicon-key" @click="unblock"></div>
    </div>
</template>

<script>
    import input from './input.vue'
    import date from './date.vue'
    import textarea from './textarea.vue'
    import mce from './mce.vue'
    import files from './files.vue'
    import radio from './radio.vue'
    import select from './select.vue'
    import checkbox from './checkbox.vue'
    import MultiSelect from './multiselect.vue'
    import MaskField from './mask.vue'

    const cloneDeep = require('clone-deep')

    var myComponents = {
            'form-field-input': input, 
            'form-field-date': date, 
            'form-field-mce': mce, 
            'form-field-textarea': textarea, 
            'form-field-radio': radio, 
            'form-field-select': select, 
            'form-field-checkbox': checkbox, 
            'form-field-files': files,
            'form-field-multiselect': MultiSelect,
            'form-field-mask': MaskField,
    }

    export default {
        props: {
            field: {},
            error: {
                default: '',
                type: String
            }
        },
        data(){
            return {
                blocked: true
            }
        },
        components: myComponents,
        computed: {
            component(){
                if ( [ 'text', 'email', 'password', 'tel' ].indexOf(this.field.type) != -1 ) return 'input';
                if ( [ 'gallery', 'files' ].indexOf(this.field.type) != -1 ) return 'files';

                if (myComponents['form-field-' + this.field.type] == undefined) {
                    console.log('Field type '+ this.field.type + ' not found')
                    return false
                }
                return this.field.type;
            },
            fieldChanged() {
                if (this.field.protected === true && this.blocked) {
                    let res = cloneDeep(this.field)
                    if (res.attr == undefined) res.attr = {}
                    res.attr.disabled = true
                    return res
                } else return this.field
            }
        },
        methods: {
            unblock: function () {
                if (confirm('Разблокировать элемент?')) this.blocked = false
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