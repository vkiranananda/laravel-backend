<template functional>
    <input v-bind="props.field.attr" :type="props.field.type" :value="props.field.value"
      v-on:input="listeners.change($event.target.value)" class="form-control"  :class='error ? "is-invalid" : "" '>
</template>
