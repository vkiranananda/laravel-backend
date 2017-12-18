<template>
    <div class="show-uploads-button">
        <button type="button" class="text-button btn btn-secondary btn-sm mb-1" @click="show()">Выбрать файл</button>
    </div>
</template>

<script>
    export default {
        props: {
            url: { default: '' },
            'field-name': { default: 'mce' },
            'data-type': { default: 'all' },
        },

        computed: {
            conf() {
                var conf = {};

                conf['type'] = this.type;
                conf['dataType'] = this['dataType'];
                conf['url'] = this.url;
                conf['fieldName'] = this['fieldName'];

                return conf;
            }
        },
        methods: {
            show(fileType) {
                this.$bus.$emit('UploadFileModalButton', this.conf);
            },
        }
    }
</script>