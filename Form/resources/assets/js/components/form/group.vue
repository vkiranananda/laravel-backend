<template>
    <fields-list  :fields='field.fields' :errors="errors" :store-name='storeName' v-bind="field.attr"></fields-list>
</template>

<script>
    // import fieldsList2 from '../form/fields.vue'
    export default {
        components: {
            // 'fields-list': fieldsList2, 
        },
        props: { 
        	field: {},
        	error: undefined,
            storeName: {
                type: String,
                default: ''
            }
        },
        computed: {
            errors: function () {
                if (this.error == undefined) return {};
                return this.error;
            }
        },
    }
</script>

