<!-- params 'error', 'field' -->
<template functional>
    <div class="form-group">
        <label v-if="props.field.label" v-html="props.field.label"></label>
        <slot></slot>
        <div class="invalid-feedback">{{ props.error }}</div>
        <small class="form-text text-muted" v-if="props.field.desc != ''" v-html="props.field.desc"></small>
    </div>                   
</template>