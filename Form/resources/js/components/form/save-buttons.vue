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
        <div class="me-4">
            <button v-if="modal" class="btn btn-secondary me-3" v-on:click="close" role="button" :disabled="disableCloseButton">{{closeLabel}}</button>
            <button v-if="showBackButton" type="button" class="btn btn-primary me-2" v-on:click="submit(true)" :disabled="currentStatus == 'saveing' ? true : false">Сохранить и выйти</button>
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
                    if (this.saveExit) {
                        window.history.back();
                    }
                }

            }
        },
        computed: {
            closeLabel: function() { return (this.modal) ? 'Закрыть' : 'Назад' },
            disableCloseButton: function() {
                if (!this.closeUrl && !this.modal && document.referrer == '') return true
                else false
            },
            showBackButton: function() {
                return (!this.modal && document.referrer != '') ? true : false
            }
        },
        methods: {
            submit(saveAndExit = false) {
                this.saveExit = saveAndExit
                this.$emit('submit', saveAndExit)
            },
            close() {
                if (this.modal) $(this.modal).modal('hide')
            },
        }


    }
</script>
