<!-- 
    status
        errorFields -   Ошибки в полях 
        errorAny    -   Просто ошибки
        saveing     -   В процессе сохранения
        saved       -   Сохранено

    hooks - submit
 -->
<template>
    <div class="row text-right form-buttons">
        <div class="col result-area">
            <span class="error" v-if='currentStatus == "errorAny"'>Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.</span>
            <span class="error" v-if='currentStatus == "errorFields"'>Проверьте правильность заполнения данных</span>
            <span class="success" v-if='currentStatus == "saved"'>Сохранено</span>
        </div>
        <div class="mr-4">
            <button class="btn btn-secondary mr-3" v-on:click="close" role="button">{{closeLabel}}</button>
            <button type="button" class="btn btn-primary" v-on:click="$emit('submit')" :disabled="currentStatus == 'saveing' ? true : false">
                <span v-if='currentStatus == "saveing"'>Сохраняю...</span>
                <span v-else>Сохранить</span>
            </button>
        </div>
    </div>
</template>

<script>
    //import forOwn from 'lodash.forown'
    export default {
        props: {
            status: { default: '' },
            modal: { default: false },
            closeUrl: { default: false }
        },
        data() { return { currentStatus: '' } },
        
        watch: {
            status: function (val) {
                this.currentStatus = this.status;

                //Если статус окей, скрываем текст через 3 секунды                
                if (val == 'saved') setTimeout(() => {
                    if (this.currentStatus == 'saved') this.currentStatus = ''
                }, 3000);
            }
        },
        computed: {
            closeLabel: function() { return (this.modal) ? 'Закрыть' : 'Назад' },
        },
        methods: {
            close() {
                if(this.closeUrl) window.location = this.closeUrl;
                else {
                    if(this.modal) $(this.modal).modal('hide')
                    else history.back()
                }
                return false;
            },
        }
                    

    }
</script>