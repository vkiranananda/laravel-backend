<template>
    <div>
        <h3 class="mb-4">{{ myData.config.title }}</h3>
        <the-loading :loading="loading"></the-loading>

        <!-- buttons -->

        <div class="mb-3">
            <button v-for="(el, key) in myData.config.menu" class="btn me-3"
                    :class="el['btn-type'] ? 'btn-' + el['btn-type'] : 'btn-outline-secondary'" role="button"
                    v-on:click.stop.prevent="menuActionClick(el)">
                {{ el['label'] }}
            </button>
        </div>

        <!-- end buttons -->

        <the-list-components v-if="myData.components['after-buttons']" components='after-buttons'
                             :data="myData"></the-list-components>
        <the-search v-if="myData.search != undefined" :fields="myData.search" @change="searchChange"></the-search>
        <the-list-components v-if="myData.components['after-search']" components='after-search'
                             :data="myData"></the-list-components>
        <the-breadcrumbs v-if="myData.breadcrumbs" :data="myData.breadcrumbs"></the-breadcrumbs>
        <the-list :fields="myData.fields" :items="myData.items" :itemMenu="myData.itemMenu"
                  @change="listChange"></the-list>

        <the-sortable @change="pageReload"></the-sortable>
        <v-alert></v-alert>
    </div>
</template>

<script>
import theList from './list.vue'
import theListComponents from './list-components'
import theLoading from '../loading.vue'
import theSearch from './search.vue'
import theSortable from './sortable.vue'
import theBreadcrumbs from './breadcrumbs.vue'

export default {
    created() {
        history.replaceState(window.location.href, '', window.location.href)
        // Данных хук вызовет переданную в параметрах функцию перед отправкой формы,
        // Результат данной функции должен быть объект ключ значение
        this.emitter.on('ListAddCustomParamsMethod', this.addCustomParamMethod)
        // Данных хук запустит поиск
        this.emitter.on('ListSend', this.send)
    },
    beforeDestroy() {
        this.emitter.off('ListAddCustomParamsMethod', this.addCustomParamMethod)
    },
    beforeDestroy() {
        this.emitter.off('ListSend', this.send)
    },

    destroyed() {
        window.onpopstate = null
    },
    props: ['data'],
    components: {theList, theLoading, theSearch, theSortable, theBreadcrumbs, theListComponents},
    data() {
        return {
            myData: this.data,
            customParamMethods: [],
            loading: false
        }
    },
    methods: {
        addCustomParamMethod(method) {
            if (this.customParamMethods.indexOf(method) === -1) {
                console.log('ListAddCustomParamsMethod register new method')
                this.customParamMethods.push(method)
            }
        },
        // Меню клик
        menuActionClick(el) {
            if (el.type == 'sortable') {
                this.emitter.emit('ListSortableShow', el)
                return
            }

            if (el.target) window.open(el.url, el.target)
            else document.location.href = el.url
        },
        itemsSortable(url) {

        },
        // Отправляем запрос
        send() {
            // Параметры к урлу по умолчанию
            let params = this.myData.config.urlPostfix;

            // Для страниц
            if (this.myData.items.currentPage > 1) {
                params = this.genUrl(params, 'page', this.myData.items.currentPage);
            }

            // Для сортировки
            for (let index in this.myData.fields) {
                let field = this.myData.fields[index];
                if (field.sortable == 'asc' || field.sortable == 'desc') {
                    params = this.genUrl(params, 'order', index);
                    params = this.genUrl(params, 'order-type', field.sortable);
                    break;
                }
            }

            // Для поиска
            if (this.myData.search != undefined) {
                for (let field of this.myData.search) if (field.value != '') {
                    params = this.genUrl(params, field.name, field.value);
                }
            }

            for (let customParamMethod of this.customParamMethods) {
                let customParams = customParamMethod()
                for (let customParamName in customParams) {
                    if (customParams[customParamName] != '') {
                        params = this.genUrl(params, customParamName, customParams[customParamName]);
                    }
                }
            }

            this.axiosSend(this.myData.config.indexUrl + params)

        },

        axiosSend(url, pushState = true) {
            this.loading = true
            // console.log(url)
            axios.get(url, {
                params: {
                    _ajax: Math.random()
                }
            })
                .then((response) => {
                    console.log(response.data)
                    this.myData = response.data
                    if (pushState) history.pushState(url, '', url)
                })
                .catch((error) => {
                    console.log(error.response)
                })
                .then(() => {
                    this.loading = false
                })
        },
        //Добавляем параметр к урлу
        genUrl(url, key, value) {
            return ((url == '') ? '?' : url + '&') + key + '=' + value
        },

        // Измения в списке
        listChange(data) {
            let changeType = ''

            if (data.currentPage != undefined) {
                this.myData.items.currentPage = data.currentPage
                changeType = 'nextPage'
            }
            //Выставляем значени сортировки
            else if (data.sortable != undefined) {
                //Очищаем предыдущие значения
                for (let field of this.myData.fields) {
                    if (field.sortable != undefined) field.sortable = true;
                }
                this.myData.fields[data.sortable].sortable = data.orderType
            }
            //Удаляем запись
            else if (data.destroy != undefined) {
                if (data.destroy == 'begin') {
                    this.loading = true
                    return
                } else {
                    this.pageReload()
                    return
                }
            }

            this.send(changeType);
        },
        searchChange(data) {
            // Выставляем значния
            for (let field of this.myData.search) {
                if (data[field.name] != undefined) field.value = data[field.name]
            }
            this.send();
        },
        pageReload() {
            this.loading = true

            axios.get(window.location.href, {params: {_ajax: Math.random()}})
                .then((response) => {
                    this.myData = response.data
                })
                .catch((error) => {
                    console.log(error.response)
                })
                .then(() => {
                    this.loading = false
                })
        }
    }
}
</script>
