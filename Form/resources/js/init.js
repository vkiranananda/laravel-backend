import fieldsList from './components/form/fields.vue'
import printField from './components/fields/field.vue'
import editHtmlForm from './components/form/edit-html.vue'
import showHtmlForm from './components/form/show-html.vue'
import listHtmlPosts from './components/list/list-html.vue'

export default {
    components: {
        'print-field': printField,
        'fields-list': fieldsList,
        'edit-html-form': editHtmlForm,
        'show-html-form': showHtmlForm,
        'list-html-posts': listHtmlPosts
    }
}
