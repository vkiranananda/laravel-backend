import cloneDeep from 'lodash.clonedeep'
import {readonly, ref} from 'vue';

const data = {
    fields: ref({}),
    tabs: ref({}),
    hiddenFields: ref({}),
    uploadFiles: ref([]),
    tabActive: ref(''),
    // Уникальный ключ, обновляется при обновлении данных формы, для того что бы перезагрузить компоненты.
    dataKey: ref(0),
    errors: ref({}),
    config: ref({}),
    show: ref({}),  // Показываем скрываем элементы
}

export default {
    fields: readonly(data.fields),
    tabs: readonly(data.tabs),
    hiddenFields: readonly(data.hiddenFields),
    uploadFiles: readonly(data.uploadFiles),
    tabActive: readonly(data.tabActive),
    errors: readonly(data.errors),
    config: readonly(data.config),
    dataKey: readonly(data.dataKey),
    show: readonly(data.show),
    setFieldProp,
    setFieldBack,
    addRepeatedBlock,
    initData,
    setTabActive,
    initCustomConfig,
    setErrors,
    addUploadFile,
    delUploadFile,
    delRepeatedBlock,
    moveRepeatedBlock
}

let indexesOfFields = [];

// Добавляем индекс поля если его нет
function addFieldIndex(field) {
    // console.log(field.value)
    if (!field['_index']) field['_index'] = indexesOfFields.push(field) - 1
}

// Индексируем поля
function indexingFields(fields) {
    for (let name in fields) {
        addFieldIndex(fields[name])
        // Обрабатываем группу полей
        if (fields[name].type == 'group') {
            indexingFields(fields[name].fields)
            console.log(fields[name].fields)
        } else if (fields[name].type == 'repeated') {
            for (let rFields of fields[name].value) indexingFields(rFields.fields)
        }
    }
}

function setTabActive(value) {
    data.tabActive.value = value
}

function initData({fields, config}) {
    data.dataKey.value ++
    data.fields.value = fields.fields
    data.hiddenFields.value = fields.hidden
    data.tabs.value = fields.tabs
    data.config.value = config
    data.uploadFiles.value = (config['clone-files']) ? config['clone-files'] : []

    // Обнуляем индексы полей
    indexesOfFields = []
    // Индексируем
    indexingFields(data.fields.value)

    // Наполняем табы реальными полями
    for (let name in data.tabs.value) {
        let resFields = {};
        for (let fieldName of data.tabs.value[name].fields) if (data.fields.value[fieldName] != undefined) {
            resFields[fieldName] = data.fields.value[fieldName];
        }
        data.tabs.value[name].fields = resFields;
    }

    setVShowDataRoot(true);

    // set active tab
    let tabActive = false;
    if (data.tabActive.value != '') {
        // Если активная таба есть, ничего не меняем.
        if (data.tabs.value[data.tabActive.value] != undefined && data.tabs.value[data.tabActive.value]['v-show'] !== false) {
            tabActive = data.tabActive.value;
        }
    }
    // Если таба еще не выбрана выбираем первую
    if (!tabActive) {
        for (let key in data.tabs.value) {
            if (data.tabs.value[key]['v-show'] !== false) {
                data.tabActive.value = key
                break;
            }
        }
    }
}


// Устанавливаем value
function setFieldProp({fields, name, property, value, fieldsType, changed}) {
    // Берем из индексов что бы можно было править.
    let field = _getField(fields[name])
    // Старое значение
    let oldValue = field[property]

    // Устанавливаем значение в поле.
    _setFieldProp(field, property, value)

    if (property == 'value') {
        // Если тип селект или радио обрабатываем отображние полей.
        if (field.type == 'select' || field.type == 'radio') {
            if (fieldsType == 'tab') setVShowDataRoot();
            else setVShowData(fields);
        }

        if (field.autosave == true) {
            if (field.type == 'select') emitter.emit('FormSave')
        }
        // Обрабатываем изменения только если поле без autosave
        else {
            // Добавляем первоначальное значение при изменении...
            if (field._changed == undefined && changed === true)
                _setFieldProp(field, '_changed', oldValue)
            else if (field.value == field._changed) _setFieldProp(field, '_changed', undefined)
        }
        beforeClose();
    }
}

// Добавляем новый репитед блок
function addRepeatedBlock(field) {
    let lField = _getField(field)

    lField.value.push({fields: cloneDeep(lField['fields']), key: lField['unique-index']});
    lField['unique-index']++;

    let fields = lField.value[lField.value.length - 1].fields
    indexingFields(fields)
    // Обновляем видимость полей
    setVShowData(fields, true);
}

// Удаляем репитед блок
function delRepeatedBlock({field, index}) {
    // Можно бы удалить конечно индексы из indexesOfFields, но тогда надо менять способ добавления, но смысла в этом не вижу
    // Какой то глобальной утечки памяти тут реально достич трудно :)
    indexesOfFields[field['_index']].value.splice(index, 1)
}
// Перемещаем блоки
function moveRepeatedBlock({field, newIndex, oldIndex}) {
    // Меняем местами
    let els = indexesOfFields[field['_index']].value
    let tmp = els[newIndex]
    els[newIndex] = els[oldIndex]
    els[oldIndex] = tmp
}

// Возвращаем первоначальное значение
function setFieldBack({fields, name, fieldsType}) {
    console.log({fields, name, fieldsType})
    let field = fields[name]

    setFieldProp({name, property: 'value', value: field['_changed'], fields, fieldsType})
    _setFieldProp(field, '_changed', undefined)
    // setFieldProp({name, property: '_changed', value: undefined, fields})
}

function initCustomConfig(data) {
    for (var key in data) data.config[key] = data[key]
}

// //Устанавливаем уникальный идентификатор для некоторых полей. Нужно для репитед полей, что бы обозначить уникальность поля с одним именем
// setUniqueKey(state) {
//     state.uniqueKey++
// },

// //Устанавливаем свойства v-show для списка полей в табе по ссылке на поле
// setTabFieldVShow(state, data) {
//     data.field['v-show'] = data.value
//     // Vue.set(data.field, 'v-show', data.value)
// },


function addUploadFile(id) {
    data.uploadFiles.value.push(id)
}

function delUploadFile(id) {
    let elId = data.uploadFiles.value.indexOf(id)
    if (elId != -1) data.uploadFiles.value.splice(elId, 1)
}

function setErrors(errors) {
    data.errors.value = errors
}


// Получаем поле из индекса
function _getField(field) {
    return indexesOfFields[field['_index']]
}

// Устанавливаем свойство полю
function _setFieldProp(field, prop, value) {
    _getField(field)[prop] = value
}

//-------------------------Код для отбражения скрития элементов----------------------------------

// Выставляем значения видимости(v-show) репитед полей.
function setVShowData(fields, all) {
    // Перебираем все поля
    for (let key in fields) {
        let field = _getField(fields[key]);
        if (field.show != undefined) {
            _setFieldProp(field, 'v-show', vShowCheck(field.show, fields))
        }
        // Проходим по всему дереву вверх текущих полей
        if (all === true) {
            if (field.type == 'repeated') {
                for (let repDataBlock of field.value) setVShowData(repDataBlock.fields, all)
            }
            if (field.type == 'group') setVShowData(field.fields, all)
        }
    }
}

// Выставляем значения видимости таба или поля начиная с корня, если опция all стоит,
// то функция пробежиться по всему дереву элементов
function setVShowDataRoot(all) {
    // Начинае обработку с табов
    for (let tabName in data.tabs.value) {
        // Если есть show выставляем значение
        if (data.tabs.value[tabName]['show'] != undefined) {
            data.tabs.value[data.name]['v-show'] = {
                name: tabName,
                value: vShowCheck(data.tabs.value[tabName]['show'], data.fields.value)
            }
        }
    }
    // Выставляем данные для полей
    setVShowData(data.fields.value, all);
}

// Проверка условий на видимость.
function vShowCheck(show, fields) {

    if (!Array.isArray(show)) return true;

    var res = false;

    for (var i = 0; i < show.length; i++) {
        if (i != 0) { //не первая запись
            //Оператор &&, если предыдущее условие ошибка тогда сл тоже ошибка, проверку не делаем
            if (show[i].operator == '&&' && res == false) continue;
            //Опертор ||, если предыдущее истинно, тогда возвращем истину, если ложно делаем проверки дальше.
            if (show[i].operator == '||' && res == true) return res;
        }

        let showField = show[i]['field'];
        let field = _getField(fields[showField])

        // Проверяем соответсвия условиям
        if (show[i].type == '==') {
            if (field.value == show[i].value) res = true;
            else res = false;
        } else { //!=
            if (field.value != show[i].value) res = true;
            else res = false;
        }
    }

    return res;
}

// Сообщение перед закрытием окна если данные были изменены
function beforeClose() {
    window.onbeforeunload = function (e) {
        return 'Данные формы не сохранены. Для сохранения, останьтесь на странице и нажмите кнопку [Сохранить]';
    };
}
