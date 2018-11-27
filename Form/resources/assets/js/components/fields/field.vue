<template>
    <component :is="component+'-field'" :field="field" :error='error' v-on:change="$emit('change', $event)"></component>
</template>

<script>

    import input from './input.vue'
    import date from './date.vue'
    import textarea from './textarea.vue'
    import mce from './mce.vue'
    import attachedFiles from './attached-files.vue'
    import radio from './radio.vue'
    import select from './select.vue'
    import repeated from './repeated.vue'
    import group from './group.vue'

    export default {
        props: {
            field: {},
            error: {
                default: '',
                type: String
            }
        },
        components: {
            'input-field': input, 
            'date-field': date, 
            'mce-field': mce, 
            'textarea-field': textarea, 
            'radio-field': radio, 
            'select-field': select, 
            'attached-files-field': attachedFiles,
            'repeated-field': repeated
        },
        computed: {
            component(){
                if( [ 'text', 'email', 'password', 'tel' ].indexOf(this.field.type) != -1 ) return 'input';
                if( [ 'gallery', 'files' ].indexOf(this.field.type) != -1 ) return 'attached-files';
                return this.field.type;
            }
        }
    }
</script>