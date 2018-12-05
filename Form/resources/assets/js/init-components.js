
// Vue.component('fields-list', require('./components/fields-list.vue'));

//Компоненты для фиелдсов
// Vue.component('print-field', require('./components/fields/field.vue'));
//!!!Компоненты для вывода списка полей. НАДО ПОРАБОТАТЬ ТУТ ЭТОГО БЫТЬ НЕ ДОЛЖНО!
Vue.component('fields-list', require('./components/form/fields.vue'));



Vue.component('modal', require('./components/modal.vue'));


//Полный вывод формы редактирование
Vue.component('edit-html-form', require('./components/form/edit-html.vue'));



//С этим потом надо разбираться...
Vue.component('the-list-sortable', require('./components/the-list-sortable.vue'));
