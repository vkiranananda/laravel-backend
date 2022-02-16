
// import Vue from 'vue'

// Vue.component('fields-list', require('./components/fields-list.vue'));

// Компоненты для фиелдсов
backend.component('print-field', require('./components/fields/field').default)
backend.component('fields-list', require('./components/form/fields').default)
backend.component('modal', require('./components/modal').default)
backend.component('edit-html-form', require('./components/form/edit-html').default)
backend.component('show-html-form', require('./components/form/show-html').default)
backend.component('list-html-posts', require('./components/list/list-html').default)

