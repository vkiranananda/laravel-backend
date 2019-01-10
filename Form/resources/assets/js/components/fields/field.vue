<template>
    <component v-if="component" :is="component+'-field'" :field="field" :error='error' v-on:change="$emit('change', $event)"></component>
</template>

<script>
    import input from './input.vue'
    import date from './date.vue'
    import textarea from './textarea.vue'
    import mce from './mce.vue'
    import files from './files.vue'
    import radio from './radio.vue'
    import select from './select.vue'

    var myComponents = {
            'input-field': input, 
            'date-field': date, 
            'mce-field': mce, 
            'textarea-field': textarea, 
            'radio-field': radio, 
            'select-field': select, 
            'files-field': files,
    }

    export default {
        props: {
            field: {},
            error: {
                default: '',
                type: String
            }
        },
        components: myComponents,
        computed: {
            component(){
                if ( [ 'text', 'email', 'password', 'tel' ].indexOf(this.field.type) != -1 ) return 'input';
                if ( [ 'gallery', 'files' ].indexOf(this.field.type) != -1 ) return 'files';

                if (myComponents[this.field.type + '-field'] == undefined) {
                    console.log('Field type '+ this.field.type + 'not found')
                    return false
                }
                return this.field.type;
            }
        }
    }
</script>