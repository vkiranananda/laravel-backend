<template>
    <div class="row text-end form-buttons">
        <div class="col result-area">
            <span class="error" v-if='status == "errorAny"'>Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.</span>
            <span class="error" v-if='status == "errorFields"'>Проверьте правильность заполнения данных</span>
            <span class="success" v-if='status == "saved"'>Сохранено</span>
            <span class="text-warning" v-else-if='status == "saveing"'>Сохраняю...</span>
        </div>
        <div class="me-4">
            <button v-for="btn in buttons"
                    @click="btnClick(btn)"
                    :disabled="status == 'saveing' ? true : false"
                    type="button"
                    class="btn ms-2"
                    :class="btn.type ? 'btn-'+btn.type : 'btn-primary'">
                {{ btn.label }}
            </button>
        </div>
    </div>
</template>

<script>

export default {
    //Создаем слушателей событий
    created() {
        this.emitter.on('FormSave', this.submit)
    },
    beforeDestroy() {
        this.emitter.off('FormSave', this.submit)
    },
    props: {
        url: {default: ''},
        method: {default: 'POST'},
        modal: {default: false},
        closeUrl: {default: false},
        storeName: {type: String, default: ''}
    },
    computed: {
        buttons: function () {
            return this.store.state.editForm.config.buttons
        },
    },
    methods: {
        btnClick(btn) {
            if (btn.link) window.location = btn.link
            if (!btn.hook) return
            switch (btn.hook) {
                case 'FormSend':
                    this.submit()
                    break
                case 'FormSendAndExit':
                    this.submit(true)
                    break
                case 'FormBack':
                    window.history.back()
                    location.reload();
                    break
                default:
                    this.emitter.emit(btn.hook, btn)
                    break
            }
        },
        submit(saveAndExit = false) {
            var res = {};
            if (this.storeName != '') {
                //Получаем все value
                res.fields = getValuesFromTabs(this.store.state[this.storeName].tabs);
                // Получаем скрытые поля
                res.hidden = this.store.state[this.storeName].hiddenFields;
                //Получаем загруженные файлы
                res.files = this.store.state[this.storeName].uploadFiles;
            }

            this.status = 'saveing';

            console.log('Sending editForm', res, this.store.state[this.storeName].fields);

            axios({
                url: this.url,
                method: this.method,
                data: res
            })
                .then((response) => {

                    console.log(response);

                    var result = response.data;

                    // Отменяем предупреждающее окно при закрытии страницы
                    window.onbeforeunload = null;

                    // Если нажата кнопка "Сохранить и выйти"
                    if (saveAndExit) {
                        // проверяем есть ли кастомный редирект, если нет дисаблим редирект
                        result.redirect = (result['redirect-save-and-exit'] != undefined) ?
                            result['redirect-save-and-exit'] : undefined
                    }

                    // Если нужно редиректим
                    if (result.redirect != undefined) {
                        if (result.redirect == 'back') {
                            window.history.back()
                            location.reload();
                        } else {
                            if (result.replace) document.location.replace(result.redirect)
                            else window.location = result.redirect
                        }
                        return
                    }

                    if (saveAndExit) {
                        window.history.back();
                        // window.location=document.referrer
                    }

                    // Инитим конфиг
                    if (result.config != undefined) {
                        if (result.fields != undefined) {//Полный инит
                            this.store.dispatch('editForm/initData', {
                                config: result.config,
                                fields: result.fields
                            });
                        }
                        // Инитим элементы конфига
                        else this.store.commit('editForm/initCustomConfig', result.config)
                    }

                    // Меняем урл если задано
                    if (result.replaceUrl != undefined) {
                        history.replaceState('data', '', result.replaceUrl);
                    }

                    this.status = 'saved'
                    setTimeout(() => {
                        this.status = ''
                    }, 3000);

                    this.store.commit('editForm/setErrors', {});

                    // Общий хук сохранения
                    this.emitter.emit('FormSaved')

                    if (result.hook != undefined && result.hook.name) {
                        this.emitter.emit(result.hook.name, result.hook.data)
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status == 422) {

                        console.log(error.response.data.errors);

                        this.store.commit('editForm/setErrors', error.response.data.errors);

                        this.status = 'errorFields';

                    } else {
                        this.status = 'errorAny';
                        console.log(error.response);
                    }
                });
        }
    },
    data() {
        return {
            status: ''
        }
    }
}


// Начинаем получать данные с табов
function getValuesFromTabs(tabs) {
    var allFields = {};
    for (var tabName in tabs) { //Получаем список всех активных корневых полей
        var currentTab = tabs[tabName];
        if (currentTab['v-show'] === false) continue; //Пропускаем если скрыта.

        for (var fieldName in currentTab.fields) {
            allFields[fieldName] = currentTab.fields[fieldName];
        }
    }
    return getValuesFromFields(allFields);
}

// Получаем данные со списка полей
function getValuesFromFields(fields) {
    var res = {};
    for (var fieldName in fields) {
        var currentField = fields[fieldName];

        if (currentField['v-show'] === false) continue; //Пропускаем если скрыто поле или

        if (currentField.type == 'repeated') { //Для повторителей
            res[fieldName] = [];
            //Перебираем блоки полей
            var currentFieldValue = currentField.value;

            for (var repIndex = 0; repIndex < currentFieldValue.length; repIndex++) {
                //Добавляем ключ сортировки, нужен для грамотного отображения ошибок.
                res[fieldName][repIndex] = {
                    key: currentFieldValue[repIndex].key,
                    value: getValuesFromFields(currentFieldValue[repIndex].fields)
                };
            }
            continue;
        }
        if (currentField.type == 'group') { //Для групп полей
            res[fieldName] = getValuesFromFields(currentField['fields']);
            continue;
        }
        //Обрабатываем галлереи и файлы
        if (currentField.type == 'gallery' || currentField.type == 'files') {
            res[fieldName] = [];
            for (var currentFieldValue of currentField.value) res[fieldName].push(currentFieldValue.id);
            continue;
        }
        res[fieldName] = currentField['value'];
    }
    return res;
}

</script>

<style lang='scss'>
.form-buttons {
    .result-area {
        font-style: italic;
        margin-top: auto;
        margin-bottom: auto;

        span {
            color: #d9534f;

            &.success {
                color: green !important;
            }
        }
    }
}
</style>

