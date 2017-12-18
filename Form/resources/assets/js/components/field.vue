<template>
    <component :is="'field-'+component" :data="data"></component>
</template>

<script>
    export default {
        props: {
            data: {},
        },
        computed: {
            component(){
                if( [ 'text', 'email', 'password', 'tel', 'date' ].indexOf(this.data.type) != -1 ) return 'input';
                if( [ 'gallery', 'files' ].indexOf(this.data.type) != -1 ) return 'attached-files';
                return this.data.type;
            }
        },
                data() {
            return {

            }
        },
    }
</script>