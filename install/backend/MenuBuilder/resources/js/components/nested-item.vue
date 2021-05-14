<template>
  <draggable
    v-bind="dragOptions"
    tag="div"
    class="item-container"
    :list="list"
    @change="change"
  >
    <div class="item-group" :key="el.id" v-for="(el, key) in list">
      <div class="item d-flex bd-highlight">
        <div class="p-2 w-100 bd-highlight">
          <a :href="el.url" target="_blank">{{el.label}}</a>
        </div>
        <div class="p-2 flex-shrink-1 bd-highlight text-nowrap">
          <v-icon name="file" class="text-primary me-1" @click="edit(el)" />
          <v-icon name="x" class="text-danger" @click="del(key)" />
        </div>
      </div>
      <nested-item class="item-sub" :list="el.elements" @edit="edit" @change="change" />
    </div>
  </draggable>
</template>

<script>
import draggable from "vuedraggable"

export default {
  name: "nested-item",
  methods: {
      del(key) {
        if (confirm('Вы действительно хотите удалить элемент?')) {
          this.list.splice(key, 1)
          this.change()
        }
      },   
      change() { this.$emit("change") },
      edit (el) { this.$emit("edit", el) },
  },

  components: { draggable },
  computed: {
    realValue() {
      return this.value ? this.value : this.list;
    },
    dragOptions() {
      return {
        animation: 0,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      };
    },    
  },
  props: {
    list: {
      required: false,
      type: Array,
      default: null
    }
  }
};
</script>


<style scoped>
  .item {
    cursor: move;
    padding: 0.5rem;
    border: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #fefefe;
  }
  .item-sub {
    margin: 0 0 0 1rem;
  }
  .octicon-wrapper {
      cursor: pointer;
  }
</style>
