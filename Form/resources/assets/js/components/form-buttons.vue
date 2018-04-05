<template>
    <div class="row text-right form-buttons">
        <div class="col result-area">
            <span class="error error-any">Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.</span>
            <span class="error error-422">Проверьте правильность заполнения данных</span>
            <span class="success">Сохранено</span>
        </div>
        <div class="mr-4">
			<button class="btn btn-secondary mr-3" v-on:click.stop.prevent="close" role="button">{{closeLabel}}</button>
        	<button type="button" class="btn btn-primary" role="submit" data-send-text="Сохраняю...">Сохранить</button>
        </div>
    </div>
</template>


<script>
    export default {
        props: {
            modal: { default: false },
            closeUrl: { default: false }
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