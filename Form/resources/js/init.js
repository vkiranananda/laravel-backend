
// Vue.component('fields-list', require('./components/fields-list.vue'));

//Компоненты для фиелдсов
// Vue.component('print-field', require('./components/fields/field.vue'));
//!!!Компоненты для вывода списка полей. НАДО ПОРАБОТАТЬ ТУТ ЭТОГО БЫТЬ НЕ ДОЛЖНО!
Vue.component('fields-list', require('./components/form/fields.vue').default);


//Модальное окно
Vue.component('modal', require('./components/modal.vue').default);

//Полный вывод формы редактирование
Vue.component('edit-html-form', require('./components/form/edit-html.vue').default);
//
Vue.component('list-html-posts', require('./components/list/list-html.vue').default);



//С этим потом надо разбираться...
Vue.component('the-list-sortable', require('./components/the-list-sortable.vue').default);
