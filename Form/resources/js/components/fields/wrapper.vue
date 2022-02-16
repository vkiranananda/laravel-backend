<!-- params 'error', 'field' -->
<template>
    <div class="form-group">
        <label v-if="field.label" v-html="field.label"  :class="field._changed != undefined ? 'changed' : ''"></label>
        <slot></slot>
        <div class="invalid-feedback d-block" v-show="error">{{ error }}</div>
        <small class="form-text text-muted" v-if="field.desc != ''" v-html="field.desc"></small>
        <v-icon v-if='field._changed != undefined' name="reply" class="back text-success" @click="$emit('v-back')" />
    </div>
</template>

<script>
export default {
    props: ['field', 'error']
}
</script>

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









