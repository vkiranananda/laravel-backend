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
            field.data.push({fields: cloneDeep(field['fields']), index: field['unique-index'] });
            field['unique-index']++;
        },
        // Сортируем репитед блоки
        sortRepeatedBlocks (state, data) { Vue.set( data.field, 'data', data.value ) },
        //Удаляем репитед блок 
        delRepeatedBlock (state, data) { data.block.splice(data.index, 1); }
     },
    actions: {
        // Добавляем новый репитед блок
        addRepeatedBlock ({ commit, state }, field) {
            commit('addRepeatedBlock', field);

            //Обновляем видимость полей
            setVShowData(commit, field.data[field.data.length - 1].fields, true );
        },
        //Устанавливаем value
        setFieldProp ({ commit, state }, data) {
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
    for ( var name in fields ) {
        if (fields[name]['show'] != undefined) {
            commit('setFieldProp', {
                field: fields[name],
                property: 'v-show',
                value: vShowCheck(fields[name]['show'], fields)
            });           
        }
        //Бежим по всему дереву вверх текущих полей
        if(all === true) {
            if (fields[name].type == 'repeated') {
                var repData = fields[name].data;
                for (var i = 0; i < repData.length; i++) {
                   setVShowData (commit, repData[i].fields, all) 
                }
            }
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