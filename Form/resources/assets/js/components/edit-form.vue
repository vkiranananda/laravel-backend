<template>
    <form role="formAjax" :method="method" :action="action">
        <slot></slot>
    </form>
</template>


<script>
    export default {
        props: {
            action: { default: '' },
            method: { default: 'POST' }
        },
        computed: {
            closeLabel: function() {
                return (this.modal) ? 'Закрыть' : 'Назад'
            },
        },
        methods: {
            close()
            {
                if(this.closeUrl){
                    window.location = this.closeUrl;
                } else {
                    if(this.modal) {
                        $(this.modal).modal('hide');
                    } else {
                        history.back();
                    }
                }
                return false;
            },
        }
    }
</script>

<style lang='scss'>
.form-buttons {
    .result-area {
        font-style: italic;
            margin-top: auto;
            margin-bottom: auto;
        span {

            display: none;
            color: #d9534f;

            &.success {
                color: green !important;
            }
        }
    }
}
</style>