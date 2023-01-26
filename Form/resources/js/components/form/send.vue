<template>
    <div class="text-end form-buttons d-flex justify-content-end">
        <div class="col result-area">
            <span :class="statusText[status].class" v-if='status != ""'>{{ statusText[status].text }}</span>
        </div>
        <div class="me-4">
            <template v-for="btn in buttons">
                <button @click="btnClick(btn)"
                        :disabled="status == 'saveing' ? true : false"
                        type="button"
                        class="btn ms-2"
                        :class="btn.type ? 'btn-'+btn.type : 'btn-primary'">
                    {{ btn.label }}
                </button>
            </template>
        </div>
        <div class="float-buttons">
            <div class="result-area float-error-block" v-if='status != ""'>
                <span :class="statusText[status].class">{{ statusText[status].text }}</span>
            </div>
            <button class="button-main">
                <v-icon name="pencil" width="18" height="18"/>
            </button>
            <div class="action-con">
                <button v-for="btn in buttons" @click="btnClick(btn)"
                        :disabled="status == 'saveing' ? true : false"
                        type="button"
                        class="button-main button-action"
                        data-toggle="tooltip" data-placement="left" data-html="true" :title="btn.label"
                >
                    <v-icon :name="btn.icon" width="15" height="15"/>
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import formData from '../../store/form-data'

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
        store: {type: Boolean, default: true}
    },
    computed: {
        buttons: function () {
            return formData.config.value.buttons
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
            if (this.store) {
                // Получаем все value
                res.fields = getValuesFromTabs(formData.tabs.value);
                // Получаем скрытые поля
                res.hidden = formData.hiddenFields.value;
                // Получаем загруженные файлы
                res.files = formData.uploadFiles.value;
            }

            this.status = 'saveing';

            console.log('Sending editForm', res, formData.fields.value);

            axios({
                url: this.url,
                method: this.method,
                data: res
            })
                .then((response) => {

                    console.log(response);

                    let result = response.data;

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
                            formData.initData({
                                config: result.config,
                                fields: result.fields
                            });
                        }
                        // Инитим элементы конфига
                        else formData.initCustomConfig(result.config)
                    }

                    // Меняем урл если задано
                    if (result.replaceUrl != undefined) {
                        history.replaceState('data', '', result.replaceUrl);
                    }

                    this.status = 'saved'
                    setTimeout(() => {
                        this.status = ''
                    }, 3000);

                    formData.setErrors({});

                    // Общий хук сохранения
                    this.emitter.emit('FormSaved')

                    if (result.hook != undefined && result.hook.name) {
                        this.emitter.emit(result.hook.name, result.hook.data)
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status == 422) {
                        console.log(error.response.data.errors);
                        formData.setErrors(error.response.data.errors);
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
            status: '',
            statusText: {
                errorAny: {
                    class: 'error',
                    text: 'Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.'
                },
                errorFields: {class: 'error', text: 'Проверьте правильность заполнения данных'},
                saved: {class: 'success', text: 'Сохранено'},
                saveing: {class: 'text-warning', text: 'Сохраняю...'},
            }
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

    .float-buttons {
        position: fixed;
        top: 10px;
        right: 20px;
        text-align: right;
        z-index: 100;


        .float-error-block {
            position: fixed;
            background-color: white;
            top: 20px;
            right: 70px;
            padding-right: 20px;
            border-radius: 5px;
        }
        &:hover {
            .action-con {
                display: flex;
                opacity: 1;
            }

            > .button-main {
                transform: scale(1.1);
            }
        }

        .button-main {
            background-color: white;
            border-radius: 25px;
            background-color: #0d6efd;

            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border: none;
            -webkit-box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.75);
            box-shadow: 0px 0px 6px 0px rgba(0, 0, 0, 0.75);
            transition: transform .2s;
            .octicon-wrapper {
                fill: white;
            }
        }

        .action-con {
            margin-top: 20px;
            //width: 50px;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            display: none;
            transition: opacity 1s;

            .button-action {
                width: 40px;
                height: 40px;
                margin-bottom: 7px;
                background-color: white;

                &:hover {
                    transform: scale(1.1);
                }

                .octicon-wrapper {
                    fill: black;
                }
            }
        }
    }
}
</style>

