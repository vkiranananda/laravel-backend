<template>
    <div>
        <div v-for="(field, key) in fields" :key="key" v-if="field['v-show'] !== false">
            <div v-if="field.type == 'html'" v-html="field.html"></div>
            <div v-else-if="field.type == 'html-title'">
                <h5 v-html="field.title" class="key == 0 ? 'mt-4' : '' "></h5><hr>
            </div>
            <div v-else-if="field.type == 'repeated'"  class="form-group">
                <label v-if="field.label" v-html="field.label"></label>
                <repeated-field :field='field' :store-name='storeName' :error='errors[field.name]'></repeated-field>
                <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
            </div>
            <group-field v-else-if="field.type == 'group'" :field='field' :store-name='storeName' :error='errors[field.name]'></group-field>
            <div v-else class="form-group">
                <label v-if="field.label" v-html="field.label"></label>
                <print-field :field='field' :error='errors[field.name]' v-on:change="onChange($event, field.name)"></print-field>
                <div class="invalid-feedback">{{errors[field.name]}}</div>
                <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
            </div>                   
        </div>
    </div>
</template>
<script>
    // import merge from 'lodash.merge'
    import cloneDeep from 'lodash.clonedeep'
    import repeatedField from '../fields/repeated.vue'
    import groupField from '../fields/group.vue'
    import printField from '../fields/field.vue'

    export default {
        components: {
            'repeated-field': repeatedField, 
            'group-field': groupField, 
            'print-field': printField
        },
        props: {
            fields: {},
            storeName: {
                type: String,
                default: ''
            },
            fieldsType: {
                type: String,
                default: ''
            }
        },
        computed: {
            errors () {
                if(this.storeName != '') {
                    var errors = this.store.state[this.storeName].errors;

                    // if (this.repeated.name != undefined && errors[this.repeated.name] == undefined) return {}
                    //else
                    return errors; 
                }
            }
        },
        methods: {
            onChange: function (value, name) {
                this.store.dispatch(this.storeName + '/setFieldProp', { 
                    name, 
                    value, 
                    fields: this.fields, 
                    property: 'value', 
                    fieldsType: this.fieldsType
                });
            }
        }
    }
</script>
