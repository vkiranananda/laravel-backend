<template>
    <div class="card form-edit">
        <div class="card-header">
            <span v-html="conf.title"></span>
            <br>
            <small id="post-link-area" v-if="conf.viewUrl">
                <a :href="conf.viewUrl" v-html="conf.viewUrl"></a>
            </small>
        </div>
        <div class="card-body">
            <div v-if="countTabs > 1">
                <ul class="nav nav-tabs" role="tablist">
                  <li v-for="(tab, key) in tabs" :key="key" class="nav-item" v-if="tab['v-show'] !== false">
                    <a class="nav-link" :class="activeTab == key ? 'active' : ''"   data-toggle="tab" :href="'#tab-'+key" role="tab" v-on:click='changeTab(key)'>
                        <span :class="errorsTab[key]">{{ tab.label }}</span>
                    </a>
                  </li>
                </ul>
                <!-- Tabs content -->
                <div class="tab-content pt-3 pb-3">
                    <div v-for="(tab, key) in tabs" class="tab-pane" :class="activeTab == key ? 'active' : ''" :id="'tab-'+key" role="tabpanel" :key="key" v-if="tab['v-show'] !== false">
                        <fields-list :fields='tab.fields' :errors="errors" fields-type='tab' store-name='editForm'></fields-list>
                    </div>
                </div>
            </div>
            <div v-else>
                <fields-list :fields='tabs[activeTab].fields' :errors="errors" store-name='editForm'></fields-list>
            </div>
            <send-form store-name='editForm' :url='conf.url' :method='conf.method'></send-form>
        </div>
    </div>
</template>


<script>
//    import assign from 'lodash.assign'
    import size from 'lodash.size';
    import fieldsList from './fields.vue'
    export default {
        components: {
            'fields-list': fieldsList, 
        },
        props: {
            fields: {},
            config: {}
        },        
        created: function () {
            this.store.dispatch('editForm/initData', { fields: this.fields, config: this.config } );
        },
        computed: {
            tabs () {
                return this.store.state.editForm.tabs;
            },
            activeTab () {
                return this.store.state.editForm.tabActive;
            },
            countTabs () {
                return size(this.tabs);
            },
            conf () {
                return this.store.state.editForm.config;
            },
            errors () {
                return this.store.state.editForm.errors; 
            },
            //Реализуем подсветку табов при ошибке
            errorsTab () {
                var res = {};
                for (var tabName in this.tabs) {
                    for (var fieldName in this.tabs[tabName].fields) {
                        if (this.errors[fieldName] != undefined) res[tabName] = 'text-danger';
                    }
                }
                return res;
            }
        },
        methods: {
            changeTab (tab) {
                this.store.commit('editForm/setTabActive', tab );
            }
        }
    }
</script>
