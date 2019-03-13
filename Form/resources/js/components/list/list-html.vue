<template>
	<div>
        <h3 class="mb-4">{{ myData.config.title }}</h3>
        <the-loading :loading="loading"></the-loading>
        <the-menu :menu="myData.config.menu"></the-menu>
        <the-search v-if="myData.search != undefined" :fields="myData.search" @change="searchChange"></the-search>
        <the-list :fields="myData.fields" :items="myData.items" :itemMenu="myData.itemMenu" @change="listChange"></the-list>

	<!--       	@if (isset($params['conf']['breadcrumb']) && $params['conf']['breadcrumb'] == true )
	        	@component('Form::components.breadcrumb', ['params' => $params ]) @endcomponent
	     	@endif -->
	</div>	
</template>

<script>
    import list from './list.vue'
    import menu from './menu.vue'
    import loading from '../loading.vue'
    import search from './search.vue'
    export default {
        created() {

            history.replaceState(window.location.href, '', window.location.href)
           
            window.onpopstate = (event) => {
                this.axiosSend(event.state, false )
            };
        },
        destroyed() {
            window.onpopstate = null
        },
        props: [ 'data' ],
        components: {
            'the-list': list, 
            'the-menu': menu,
            'the-loading': loading,
            'the-search': search,
        },        
        data() {
            return {
                lastUrl: '',
                myData: this.data,
                loading: false
            }
        },
        methods: {
            // Отправляем запрос
            send(changeType) {
                //Параметры к урлу по умолчанию
                let params = this.myData.config.urlPostfix;

                //Для страниц
                if (this.myData.items.currentPage > 1) {
                    params = this.genUrl(params, 'page', this.myData.items.currentPage);
                }

                //Для сортировки
                for (let index in this.myData.fields) {
                    let field = this.myData.fields[index];
                    if (field.sortable == 'asc' || field.sortable == 'desc') {
                        params = this.genUrl(params, 'order', index);
                        params = this.genUrl(params, 'order-type', field.sortable);
                        break;
                    }
                }

                //Для поиска
                if(this.myData.search != undefined){
                    for (let field of this.myData.search) if (field.value != ''){
                        params = this.genUrl(params, field.name, field.value);
                    }
                }

                this.axiosSend(this.myData.config.indexUrl + params)

            },
            axiosSend(url, pushState = true) {

                if (this.lastUrl == url) return

                this.loading = true
                this.lastUrl = url

                axios.get(url, { 
                    params: {
                        _ajax: true
                    }
                })
                .then( (response) => {
                    console.log(response.data)
                    this.myData = response.data
                    if(pushState) history.pushState(url, '', url)
                })
                .catch( (error) => { console.log(error.response) })
                .then( () => { this.loading = false }  )

            },
            //Добавляем параметр к урлу
            genUrl (url, key, value ) { return ( (url == '') ? '?' : url + '&') + key + '=' + value },
            
            // Измения в списке
            listChange (data) {
                let changeType = ''

                if (data.currentPage != undefined) {
                    this.myData.items.currentPage = data.currentPage
                    changeType = 'nextPage'
                }
                //Выставляем значени сортировки
                else if (data.sortable != undefined) {
                    //Очищаем предыдущие значения
                    for (let field of this.myData.fields){
                        if (field.sortable != undefined) field.sortable = true;
                    }
                    this.myData.fields[data.sortable].sortable = data.orderType
                } 
                //Удаляем запись
                else if (data.destroy != undefined) { 
                    if (data.destroy == 'begin') {
                        this.loading = true;
                        return;
                    } else {
                        this.loading = false;
                        if (data.destroy == 'error') return;
                    } 
                }

                this.send(changeType);
            },
            searchChange (data) { 
                //Выставляем значния
                for (let field of this.myData.search) {
                    if (data[ field.name ] != undefined) field.value = data[ field.name ]
                }
                this.send();
            }
        }
    }
</script>
