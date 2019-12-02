<template>
  <draggable
    v-bind="dragOptions"
    tag="div"
    class="item-container"
    :value="value"
    :list="list"
    @input="emitter"
  >
    <div class="item-group" :key="el.id" v-for="(el, key) in realValue">
      <div class="item d-flex bd-highlight">
        <div class="p-2 w-100 bd-highlight">
          <a :href="el.url" target="_blank">{{el.label}}</a>
        </div>
        <div class="p-2 flex-shrink-1 bd-highlight text-nowrap">
          <div class="octicon octicon-file text-primary" @click="edit(el)"></div>
          <div class="octicon octicon-x text-danger" @click="del(key)"></div>
        </div>
      </div>
      <nested-item class="item-sub" :list="el.elements" @edit="edit" />
    </div>
  </draggable>
</template>

<script>
import draggable from "vuedraggable"

export default {
  name: "nested-item",
  methods: {
      emitter(value) { this.$emit("input", value) },
      del(key) {
        if (confirm('Вы действительно хотите удалить элемент?')) {
          let list = this.realValue
          list.splice(key, 1)
          this.emitter(list)
        }
      },
      edit (el) {
        this.$emit("edit", el)
      }
  },
  components: { draggable },
  data() {
    return {
       dragOptions: {
        animation: 0,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      }    
    }
  },
  computed: {
    // this.value when input = v-model
    // this.list  when input != v-model
    realValue() {
      return this.value ? this.value : this.list;
    }
  },
  props: {
    value: {
      required: false,
      type: Array,
      default: null
    },
    list: {
      required: false,
      type: Array,
      default: null
    }
  }
};
</script>


<style scoped>
  .item-container {
    /*max-width: 30rem;*/
    margin: 0;
  }
  .item {
    cursor: move;
    padding: 0.5rem;
    border: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #fefefe;
  }
  .item-sub {
    margin: 0 0 0 1rem;
  }
  .octicon {
    cursor: pointer;
  }
</style>
