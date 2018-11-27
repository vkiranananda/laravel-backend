<template>
    <fields-list  :fields='field.fields' :store-name='storeName'></fields-list>
</template>

<script>
    // import fieldsList2 from '../form/fields.vue'
    export default {
        components: {
            // 'fields-list': fieldsList2, 
        },
        props: { 
        	field: {},
        	error: {
        		type: String,
        		default: ''
        	},
            storeName: {
                type: String,
                default: ''
            }
        }
    }
</script>

