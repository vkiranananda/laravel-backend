<template>
    <div>
        <div class="row text-right form-buttons">
            <div class="col result-area">
                <span class="error" v-if='errorHaveAny'>Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.</span>
                <span class="error" v-if='errorHave'>Проверьте правильность заполнения данных</span>
                <span class="success" v-if='saved'>Сохранено</span>
            </div>
            <div class="mr-4">
                <button class="btn btn-secondary mr-3" v-on:click.stop.prevent="close" role="button">{{closeLabel}}</button>
                <button type="button" class="btn btn-primary" v-on:click.stop.prevent="submit" :disabled="saveing">
                    <span v-if='saveing'>Сохраняю...</span>
                    <span v-else='saveing'>Сохранить</span>
                </button>
            </div>
        </div>

        <upload-file-modal></upload-file-modal>
        <upload-edit-file url=""></upload-edit-file>
    </div>
</template>


<script>
    export default {
        props: {
            url: { default: '' },
            method: { default: 'POST' },
            modal: { default: false },
            closeUrl: { default: false },
            storeName: { type: String, default: '' }
        },
        computed: {
            closeLabel: function() {
                return (this.modal) ? 'Закрыть' : 'Назад'
            },
        },
        methods: {
            submit()  
            {
                var res = {};
                if(this.storeName != ''){
                    //Получаем все value
                    res = getValuesFromTabs(this.store.state[this.storeName].tabs);
                }

                this.errorHaveAny = false;
                this.errorHave = false;
                this.saveing = true;

                console.log('Sending editForm', res);


                axios({
                    url: this.url,
                    method: this.method,
                    data: res
                })
                .then( (response) => {
                    console.log(response);
                    this.saved = true;
                    this.saveing = false;
                    this.store.commit('editForm/setErrors', {});

                    setTimeout(() => {
                        this.saved = false;
                    }, 3000);
                })
                .catch( (error) => {
                    this.saveing = false;
                    if(error.response.status == 422){
                        var errors = error.response.data.errors;
                        console.log(error.response.data);
                        var resErrors = {};
                        //Set errors
                        for (var key in errors) {
                            resErrors[key] = '';
                            for (var i = 0; i < errors[key].length; i++) {
                              resErrors[key] += errors[key][i] + ' ';
                            }
                        }
                        this.store.commit('editForm/setErrors', resErrors);

                        this.errorHave = true;

                    } else {
                        this.errorHaveAny = true;
                        console.log(error.response);
                    }
                }); 
            },

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
        },
        data() {
            return {
                errorHaveAny: false,
                errorHave: false,
                saved: false,
                saveing: false
            }
        }
    }


//Начинаем получать данные с табов
function getValuesFromTabs(tabs)
{
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
//Получаем данные со списка полей
function getValuesFromFields (fields)
{
    var res = {};
    for (var fieldName in fields) {
        var currentField = fields[fieldName];
        
        if (currentField['v-show'] === false) continue; //Пропускаем если скрыто поле.
        
        if (currentField['type'] == 'repeated') { //Для повторителей
            res[fieldName] = {};
            //Перебираем блоки полей
            for (var repIndex = 0; repIndex < currentField.data.length; repIndex++) {
                res[fieldName][repIndex] = getValuesFromFields (currentField.data[repIndex].fields);
            }
            continue;
        }
        if (currentField['type'] == 'group') { //Для групп полей
            res[fieldName] =  getValuesFromFields (currentField['fields']);
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

