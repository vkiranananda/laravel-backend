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
            <span class="text-warning" v-else-if='currentStatus == "saveing"'>Сохраняю...</span>
        </div>
        <div class="mr-4">
            <button v-if="modal" class="btn btn-secondary mr-3" v-on:click="close" role="button" :disabled="diableCloseButton">{{closeLabel}}</button>

            <button v-if="!modal" type="button" class="btn btn-primary mr-2" v-on:click="submit(true)" :disabled="currentStatus == 'saveing' ? true : false">Сохранить и выйти</button>
            <button type="button" class="btn btn-secondary" v-on:click="submit()" :disabled="currentStatus == 'saveing' ? true : false">Сохранить</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            status: { default: '' },
            modal: { default: false },
            closeUrl: { default: false }
        },
        data() { return { 
            currentStatus: '',
            saveExit: false
        } },
        
        watch: {
            status: function (val) {
                this.currentStatus = this.status;

                // Если статус окей, скрываем текст через 3 секунды                
                if (val == 'saved') {
                    setTimeout(() => {
                        this.currentStatus = ''
                    }, 3000);
                    
                    if (this.modal) $(this.modal).modal('hide')
                    if (this.saveExit) window.location = document.referrer
                }

            }
        },
        computed: {
            closeLabel: function() { return (this.modal) ? 'Закрыть' : 'Назад' },
            diableCloseButton: function() { 
                if (!this.closeUrl && !this.modal && document.referrer == '') return true
                else false
            }
        },
        methods: {
            submit(close = false) {
                this.saveExit = close
                this.$emit('submit')
            },
            close() {
                if (this.modal) $(this.modal).modal('hide')
            },
        }
                    

    }
</script>