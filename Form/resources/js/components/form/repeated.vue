<template>
  <div class="repeated-fields card">
    <div class="card-body">
      <div ref="repeatedFields">
        <div
          class="fields-block"
          :class="field.style ?? ''"
          v-for="(item, index) in field.value"
          :key="item.key"
          :ref="'block' + index">
          <div class="card">
            <div class="menu-con" v-if="!field.readonly">
              <div class="text-end">
                <v-icon name="menu" class="menu-icon" width="18" height="18" @click="menuOpen(index)" />
              </div>
              <v-dropdown class="base-menu" :width="350" @v-click-outside="closeMenu()" :ref="'menu' + index" v-if="currentMenuOpen === index">
                <div class="item" @click="addNew(index)">
                  <v-icon name="plus" class="add-icon icon-green" />
                  Добавить новый элемент
                </div>
                <hr />
                <template v-if="propFields">
                  <div class="prop-fields">
                    <div v-for="field in propFields.fields" class="prop-field">
                      <label>{{ field.label }}</label>
                      <print-field
                        :field="field"
                        :fields="propFields.fieldsGroup"
                        @v-change="onPropChange($event, field.name)">
                      </print-field>
                    </div>
                  </div>
                  <hr />
                </template>
                <div class="item" :class="index == 0 ? 'disabled' : ''" @click="moveUp(index)">
                  <v-icon name="arrow-up" />
                  Переместить вверх
                </div>
                <div class="item" @click="delBlock(index)">
                  <v-icon name="close" class="remove-icon icon-red" />
                  Удалить
                </div>
                <div class="item" :class="field.value.length - 1 == index ? 'disabled' : ''" @click="moveDown(index)">
                  <v-icon name="arrow-down" />
                  Переместить вниз
                </div>
              </v-dropdown>
            </div>
            <div class="card-body">
              <fields-list :fields="item.fields" :errors="errors[item.key]"></fields-list>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-1 mb-2" v-if="!field.readonly">
        <div class="add-button" @click="addNew()">
          <v-icon name="plus" class="add-icon" />
          {{ field['add-label'] ? field['add-label'] : 'Добавить новый элемент' }}
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import formData from '../../store/form-data';
import printField from '../fields/field.vue';

export default {
  components: {
    'print-field': printField,
  },
  props: {
    field: {},
    error: undefined,
  },

  data() {
    return {
      currentMenuOpen: false,
    };
  },
  computed: {
    errors: function () {
      if (this.error == undefined) return {};
      return this.error;
    },
    propFields: function () {
      // Проверяем есть ли поля для конфигурации и меню открыто
      if (
        this.currentMenuOpen !== false &&
        this.field['prop-fields'] &&
        Array.isArray(this.field['prop-fields']) &&
        this.field['prop-fields'].length > 0
      ) {
        // получаем текущую группу полей
        let fieldsGroup = this.field.value[this.currentMenuOpen].fields;
        let fields = [];
        for (let key of this.field['prop-fields']) {
          if (fieldsGroup[key] && fieldsGroup[key]['v-show'] !== false) {
            let newField = Object.assign({}, fieldsGroup[key]);
            newField.size = 'small';
            fields.push(newField);
          }
        }
        return fields.length > 0 ? { fieldsGroup, fields } : false;
      }
      return false;
    },
  },
  watch: {
    currentMenuOpen(newVal, oldVal) {
      this.$nextTick(() => {
        if (newVal !== false) this.$refs['menu' + newVal][0].show();
      });
    },
  },
  methods: {
    onPropChange: function (value, name) {
      formData.setFieldProp({
        name,
        value,
        fields: this.propFields.fieldsGroup,
        property: 'value',
      });
      if (this.propFields.fields.length === 1) this.closeMenu();
    },

    closeMenu() {
      this.currentMenuOpen = false;
    },
    addNew(index = false) {
      // Добавляем элемент из меню
      if (index !== false && this.currentMenuOpen !== false) {
        document.documentElement.scrollTop += this.$refs['block' + index][0].offsetHeight;
        this.currentMenuOpen++;
        index++;
      } else {
        this.closeMenu();
      }
      formData.addRepeatedBlock({ field: this.field, index });
    },
    moveUp(index) {
      if (this.currentMenuOpen != false) this.currentMenuOpen--;
      document.documentElement.scrollTop += -this.$refs['block' + (index - 1)][0].offsetHeight;
      formData.moveRepeatedBlock({ field: this.field, newIndex: index - 1, oldIndex: index });
    },
    moveDown(index) {
      if (this.currentMenuOpen !== false) this.currentMenuOpen++;
      document.documentElement.scrollTop += this.$refs['block' + (index + 1)][0].offsetHeight;
      formData.moveRepeatedBlock({ field: this.field, newIndex: index + 1, oldIndex: index });
    },
    menuOpen(index) {
      this.currentMenuOpen = this.currentMenuOpen === index ? false : index;
    },
    delBlock(index) {
      this.closeMenu();
      this.msgConfirm('Подтвердите удаление.', () => {
        formData.delRepeatedBlock({ field: this.field, index });
      });
    },
  },
};
</script>

<style lang="scss">
.repeated-fields {
  .add-button {
    cursor: pointer;
  }

  .fields-block {
    padding-bottom: 20px;

    .menu-con {
      position: absolute;
      right: 5px;
      top: 1px;

      .prop-fields {
        margin: 10px;

        .prop-field {
          margin-bottom: 10px;
        }
      }

      .menu-icon {
        cursor: pointer;
      }
    }

    &.editor {
      padding-bottom: 0;

      > .card {
        border-color: transparent;

        &:hover {
          background-color: #fbfbfb;
        }
      }
    }
  }
}
</style>
