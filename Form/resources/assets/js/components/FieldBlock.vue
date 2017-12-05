<template>
    <div class="form-group" :id="fieldId+'block'">
        <label :for="fieldId" v-if="label != ''">{{label}}</label>
        <div class="Forms-field-con">
            <button type="button" class="btn btn-secondary btn-sm mb-1" v-if="mediaUrl" role="attachFiles" :data-url="mediaUrl">
                Выбрать файл
            </button>
            <slot></slot>
            <span class="form-control-feedback Forms-error-text"></span>
            <small class="form-text text-muted" v-if="desc != ''">{{desc}}</small>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            desc: { default: '' },
            fieldId: { default: '' },
            label: { default: '' },
            mediaUrl: {}
        },
    }
</script>

    <field-block 
        desc="{{isset($fieldParams['desc'])? $fieldParams['desc']:''}}" 
        label="{{isset($fieldParams['label']) ? $fieldParams['label']: ''}}" 
        field-id="{{$field['attr']['id']}}" 
        media-url="{{isset($field['upload']) ? action($uploadAction, $dataId) : ''}}"
        class="{{isset($field['conteiner-class'])? $field['conteiner-class'] : ''}}"
    >
        @if( in_array($field['type'], ['gallery', 'files']) )
            <?php
                $field['data-url'] = (isset($field['upload-action'])) ? action($field['upload-action'], $dataId) : action($uploadAction, $dataId);
            ?>
            @component('Upload::components.files-field', [ 'field' => $field, 'postId' => $dataId]) @endcomponent
        @else
            {!!Forms::fieldHTML($field, $data)!!}
        @endif
    </field-block>