<template>
	<div>
        <h3 class="mb-4">{{ myData.config.title }}</h3>
        <the-loading :loading="loading"></the-loading>
        <the-menu :menu="myData.config.menu"></the-menu>
        <the-search v-if="myData.search != undefined" :fields="myData.search" @change="searchChange"></the-search>
        <the-list :fields="myData.fields" :items="myData.items" @change="listChange"></the-list>

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
        props: [ 'data' ],
        components: {
            'the-list': list, 
            'the-menu': menu,
            'the-loading': loading,
            'the-search': search,
        },        
        data() {
            return {
                myData: this.data,
                loading: false
            }
        },
        methods: {
            // Отправляем запрос
            send() {
                this.loading = true;

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

                let url = this.myData.config.indexUrl + params;

                axios.get(url)
                .then( (response) => {
                    this.myData = response.data;
                    //push
                    history.replaceState('data', '', url);
                    this.loading = false;
                })
                .catch( (error) => { console.log(error.response); this.loading = false; }); 

            },
            //Добавляем параметр к урлу
            genUrl (url, key, value ) { return ( (url == '') ? '?' : url + '&') + key + '=' + value },
            
            // Измения в списке
            listChange (data) {
                if (data.currentPage != undefined) this.myData.items.currentPage = data.currentPage
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

                this.send();
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
