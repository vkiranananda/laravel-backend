<template functional>
    <input v-bind="props.field.attr" :type="props.field.type" :value="props.field.value"
      v-on:input="listeners.change($event.target.value)" :disabled="props.field.readonly ? true : false" class="form-control"  :class='props.error ? "is-invalid" : "" '>
</template>
