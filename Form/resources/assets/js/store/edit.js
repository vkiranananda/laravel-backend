import cloneDeep from 'lodash.clonedeep'

export default {
  	namespaced: true,

  	state: {
        fields: {},
        tabs: {},
        tabActive: '',
        errors: {},
        config: {},
        show: {}  //Показываем скрываем элементы
    },

  	mutations: {
        initData (state, data) {
            state.fields = data.fields.fields;
            state.tabs = data.fields.tabs;
            state.config = data.config;

            //Наполняем табы реальными полями
            for (var name in state.tabs) {
                var tabFields = state.tabs[name].fields;
                var resFields = {};
                for (var index = 0; index < tabFields.length; index++) {
                    var fieldName = tabFields[index];
                    resFields[ fieldName ] = state.fields[ fieldName ];
                }
                state.tabs[name].fields = resFields;
            }
        },
        //Инитим отдельные опции конфига.
        initCustomConfig (state, data) { for (var key in data) state.config[key] = data[key] },
        //Устанавливаем свойство в поле property
        setFieldProp (state, data) { Vue.set (data.field, data.property, data.value ) },
        //Устанавливаем уникальный идентификатор для некоторых полей. Нужно для ретитед полей, что бы обозначить уникальность поля с одним именем
        setUniqueKey (state) { state.uniqueKey++ },
        setTabVShow (state, data) { Vue.set(state.tabs[data.name], 'v-show', data.value) },
        //Устанавливаем свойства v-show для списка полей в табе по ссылке на поле
        setTabFieldVShow (state, data) { Vue.set( data.field, 'v-show', data.value) },
        //Выставляем активную табу
        setTabActive (state, value) { state.tabActive = value },
        //Выставляем ошибки
        setErrors (state, errors) { state.errors = errors },
        // Добавляем новый репитед блок
        addRepeatedBlock (state, field) { 
            field.value.push({fields: cloneDeep(field['fields']), key: field['unique-index'] });
            field['unique-index']++;
        },
        //Удаляем репитед блок 
        delRepeatedBlock (state, data) { data.block.splice(data.index, 1); }
     },
    actions: {
        // Добавляем новый репитед блок
        addRepeatedBlock ({ commit, state }, field) {
            commit('addRepeatedBlock', field);

            //Обновляем видимость полей
            setVShowData(commit, field.value[field.value.length - 1].fields, true );
        },
        //Устанавливаем value
        setFieldProp ({ commit, state }, data) {
            // console.log(data);
            var field = data.fields[data.name];
            //Устанавливаем значение в поле.
            commit('setFieldProp', { field, property: data.property, value: data.value });
            //For value
            if(data.property == 'value') {
                //Если тип селект или радио обрабатываем отображние полей.
                if (field.type == 'select' || field.type == 'radio') {
                    if (data.fieldsType == 'tab' ) setVShowDataRoot(commit, state);
                    else setVShowData(commit, data.fields); 
                }

                beforeClose();
            }
        },

        //Инитим данные
        initData({ commit, dispatch, state }, data) {
            commit('initData', data);
            setVShowDataRoot(commit, state, true);

            //set active tab
            for (let key in state.tabs) {
                if( state.tabs[key]['v-show'] !== false ) {
                    commit('setTabActive', key);
                    break;
                }
            }
        }
    }
};

//-------------------------Код для отбражения скрития элементов----------------------------------


//Выставляем значения видимости репитед полей.
function setVShowData (commit, fields, all) {
    //Перебираем все поля 
    for (let key in fields ) {
        let field = fields[key];
        if (field.show != undefined) {
            commit('setFieldProp', {
                field: field,
                property: 'v-show',
                value: vShowCheck(field.show, fields)
            });           
        }
        //Бежим по всему дереву вверх текущих полей
        if(all === true) {
            if (field.type == 'repeated') {
                for ( let repDataBlock of field.value ) setVShowData (commit, repDataBlock.fields, all) 
            }
            if (field.type == 'group') setVShowData (commit, field.fields, all)
        }
    }
}

//Выставляем значения видимости таба или поля начиная с корня, если опция all стоит, 
//то функция пробежиться по всему дереву элементов
function setVShowDataRoot (commit, state, all) {
    //Начинае обработку с табов
    for ( var tabName in state.tabs) {
        //Если есть show выставляем значение
        if (state.tabs[tabName]['show'] != undefined) {            
            commit('setTabVShow', { name: tabName, value: vShowCheck(state.tabs[tabName]['show'], state.fields) });
        }
    }
    //Выставляем данные для полей
    setVShowData(commit, state.fields, all);
}

//Проверка условий на видимость.
function vShowCheck (show, fields) {

    if ( ! Array.isArray(show) ) return true;
    
    var res = false;
    
    for (var i = 0; i < show.length; i++) {
        if(i != 0) { //не первая запись
            //Оператор &&, если предыдущее условие ошибка тогда сл тоже ошибка, проверку не делаем
            if(show[i].operator == '&&' && res == false)continue; 
            //Опертор ||, если предыдущее истинно, тогда возвращем истину, если ложно делаем проверки дальше.
            if(show[i].operator == '||' && res == true)return res;    
        }
  
        var showField = show[i]['field'];        

        //Проверяем соответсвия условиям
        if (show[i].type == '==') {
            if ( fields[showField].value == show[i].value) res = true;
            else res = false;  
        } else { //!=
            if ( fields[showField].value != show[i].value) res = true;
            else res = false;  
        }
    }

    return res;
}

function beforeClose () {
    window.onbeforeunload = function(e) {
        return 'Данные формы не сохранены. Для сохранения, останьтесь на странице и нажмите кнопку [Сохранить]';
    };
}
           