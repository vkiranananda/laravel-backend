<!-- Для использования в других модулях кроме основного, repeated и group поля работать не будут! -->
<template>
    <div>
        <div v-for="(field, key) in fields" :key="key" v-if="field['v-show'] !== false">
            <div v-if="field.type == 'html'" v-html="field.html"></div>
            <div v-else-if="field.type == 'html-title'">
                <h5 v-html="field.title" class="key == 0 ? 'mt-4' : '' "></h5><hr>
            </div>
            <div v-else-if="field.type == 'repeated'"  class="form-group">
                <label v-if="field.label" v-html="field.label"></label>
                <repeated-field :field='field' :store-name='storeName' :error='currentErrors[field.name]' v-on:change="onChange($event, field.name)"></repeated-field>
                <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
            </div>
            <group-field v-else-if="field.type == 'group'" :field='field' :store-name='storeName' :error='currentErrors[field.name]'></group-field>
            
            <field-wrapper v-else :error="currentErrors[field.name]" :field="field" @back="onBack(field.name)">
                <print-field :field='field' :error='currentErrors[field.name]' v-on:change="onChange($event, field.name)"></print-field>
            </field-wrapper>                   
        </div>
    </div>
</template>
<script>
    import repeatedField from './repeated.vue'
    import groupField from './group.vue'
    import printField from '../fields/field.vue'
    import fieldWrapper from '../fields/wrapper.vue'

    export default {
        created() {
            console.log('fields:', this.fields)
        },
        components: {
            'repeated-field': repeatedField, 
            'group-field': groupField, 
            'print-field': printField,
            'field-wrapper': fieldWrapper
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
            },
            errors: undefined
        },
        computed: {
            currentErrors () {
                if (this.errors != undefined) return this.errors; 
                else return {};
            }
        },
        methods: {
            onChange: function (value, name) {
                if (this.storeName == '') this.$emit ('change', value, name)
                else {
                    let changed = true

                    // Возвращается более сложный объект
                    if (value.value != undefined) {
                        // Не сохраняем это изменение
                        if (value.changed === false) changed = false
                        value = value.value
                    }

                    this.store.dispatch(this.storeName + '/setFieldProp', { 
                        name, 
                        value, 
                        changed,
                        fields: this.fields, 
                        property: 'value', 
                        fieldsType: this.fieldsType,
                    });
                }
            },
            onBack: function (name) {
                this.store.dispatch(this.storeName + '/setFieldBack', { 
                    name, 
                    fields: this.fields, 
                });
            }
        }
    }
</script>
