<template functional>
    <textarea v-bind="props.field.attr" :class='props.error ? "is-invalid" : "" ' v-on:input="listeners.change($event.target.value)" class="form-control">{{props.field.value}}</textarea>
</template>
