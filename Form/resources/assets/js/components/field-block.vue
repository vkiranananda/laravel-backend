<template>
    <div class="form-group">
        <label :for="fieldId" v-if="data.label != ''" v-html="data.label"></label>
        <div class="Forms-field-con">
            <slot>
                <field :data="normalizeData"></field>
            </slot>
            <span class="form-control-feedback Forms-error-text"></span>
            <small class="form-text text-muted" v-if="data.desc != ''" v-html="data.desc"></small>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            data: {},
        },
        computed: {
            fieldId(){
                if(this.normalizeData.attr && this.normalizeData.attr.id) return this.normalizeData.attr.id;
            },
            normalizeData() {
                //не поле
                if(!this.data.type || !this.data.name)return this.data;

                if(!this.data.attr) this.data.attr = {};
                if(!this.data.attr.class) this.data.attr.class = '';
                if(!this.data.attr.id) this.data.attr.id = 'Forms_' + this.data.name;
                
                if( ['date', 'email', 'text', 'tel', 'textarea', 'mce', 'password', 'select'].indexOf(this.data.type) != -1 ) this.data.attr.class += ' form-control';
                else if( ['radio', 'checkbox'].indexOf(this.data.type) != -1 ) this.data.attr.class += ' form-check-input';
                
                return this.data;
            },
        },
    }
</script>