<template>
  <div class="select-search">
    <div v-show="maxItems == 0 || maxItems > value.length">
      <form-field-autocomplete
        @v-change="select"
        :field="autoField"
      ></form-field-autocomplete>
    </div>

    <div
      v-for="(val, key) in value"
      :key="key"
      class="item border-primary rounded border"
    >
      {{ options[val] }}
      <a
        href="#"
        v-on:click.prevent="del(val)"
        class="delete"
        v-if="field.readonly != true"
        >&times;</a
      >
    </div>
  </div>
</template>

<script>
import FormFieldAutocomplete from './autocomplete.vue';

export default {
  name: 'select-search',
  props: ['field', 'fields'],
  components: {
    FormFieldAutocomplete,
  },
  data() {
    return {
      // Опции
      options: {},
      autoText: '',
    };
  },
  watch: {
    // Подгружаем опции
    'field.options': {
      handler: function (val) {
        if (Array.isArray(val)) {
          for (let opt of val) {
            this.options[opt.value] = opt.label;
          }
        }
                    console.log(this.options);
      },
      immediate: true,
      deep: true,
    },
  },
  computed: {
    autoField() {
      return {
        type: 'autocomplete',
        url: this.field.url,
        value: this.autoText,
        // 'clear-if-select': true,
      };
    },
    maxItems() {
      if (this.field.multiple)
        return this.field['max-items'] ? this.field['max-items'] : 0;
      return 1;
    },
    value() {
      if (this.field.multiple) {
        return Array.isArray(this.field.value) ? this.field.value : [];
      } else {
        return this.field.value && this.field.value !== ''
          ? [this.field.value]
          : [];
      }
    },
  },
  methods: {
    select: function (val) {
      // Если не объект, значит ничего не нашлось...
      if (val.value === undefined) {
        this.autoText = val;
        return;
      };

      this.autoText = '';
      let value;

      this.options[val.value] = val.label;

      // Если множественный выбор добавляем элемент в конец иначе создаем массив с одним элементом
      if (this.field.multiple) {
        value = this.value;
        value.push(val);
      } else value = [val];

      this.emit(value);
    },

    del: function (el) {
      // Удаляем элемент из массива
      let value = this.value;
      value.splice(value.indexOf(el), 1);

      this.emit(value);
    },

    emit: function (value) {
      let res = [];
      for (let opt of value) res.push(opt.value);

      this.$emit('v-change', this.field.multiple ? res : res[0] ? res[0] : '');
    },
  },
};
</script>

<style lang="scss">
.select-search {
  > .item {
    position: relative;
    padding: 5px 7px;
    margin: 5px 10px 0 15px;
    display: inline-block;

    .delete {
      position: absolute;
      right: -15px;
      top: -8px;
      font-size: 18px;
      text-align: center;
      color: red;
      text-decoration: none;
      cursor: pointer;
    }
  }
}
</style>
