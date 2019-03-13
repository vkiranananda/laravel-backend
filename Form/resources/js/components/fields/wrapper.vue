<!-- params 'error', 'field' -->
<template functional>
    <div class="form-group">
        <label v-if="props.field.label" v-html="props.field.label"  :class="props.field._changed != undefined ? 'changed' : ''"></label>
        <slot></slot>
        <div class="invalid-feedback d-block" v-show="props.error">{{ props.error }}</div>
        <small class="form-text text-muted" v-if="props.field.desc != ''" v-html="props.field.desc"></small>
        <div class="back octicon octicon-reply text-success" v-if="props.field._changed != undefined" @click="listeners.back"></div>
    </div>                   
</template>


<style lang='scss'>
    .form-group { 
		position: relative;
		.back {
			position: absolute;
			top: 0px;
			right: 0px;
			cursor: pointer;
		}
		.changed {
			font-weight: 600;
		}
    }
</style>









