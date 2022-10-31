<template>
    <v-modal name="AlertModal">
        <div v-html="params.msg"></div>
        <template #footer>
            <div class="text-end">
                <button v-if="params.type == 'confurm'" @click="hide" class="btn btn-secondary me-1">
                    {{ labels[1] }}
                </button>
                <button class="btn btn-secondary" @click="okFunc">
                    {{ labels[0] }}
                </button>
            </div>
        </template>
    </v-modal>
</template>

<script>
import vModal from "./modal.vue"

export default {
    name: "alert",
    components: {
        vModal
    },
    created() {
        this.emitter.on('AlertModalShow', this.show)
    },
    beforeDestroy() {
        this.emitter.off('AlertModalShow', this.show)
    },
    mounted() {
        // Это нужно для того что бы часто открывать окна
        $(this.$refs.modal).on('hidden.bs.modal', () => {
            this.currentModalOpen = false
            // Дергаем следующую очередь после закрытия модала
            this.nextQueue()
        })
    },
    data() {
        return {
            labels: ['ОК', 'Отмена'],
            params: [],
            queue: [],
            currentModalOpen: false
        }
    },
    methods: {
        // nextQueue: function () {
        //     // Если модал не открыт открываем.
        //     if (this.queue.length > 0 && !this.currentModalOpen) {
        //         // Отмечаем что модал открыт
        //         this.currentModalOpen = true
        //         $(this.$refs.modal).modal('show')
        //
        //         // Берем первый элемент
        //         let data = this.queue.shift()
        //
        //         this.params = data
        //         if (data.btns) this.btns = data.btns
        //     }
        // },
        show: function (data) {
            console.log(data)
            this.params = data
            this.modal.show("AlertModal")
            // Добавляем в конец очереди
            // this.queue.push(data)
            // this.nextQueue()
        },
        hide: function () {
            this.modal.hide("AlertModal")
        },
        okFunc: function () {
            this.hide()
            if (this.params.func) this.params.func()
        }
    },
}
</script>

<style scoped lang='scss'>
.alert {
    z-index: 2050;
}

.modal-backdrop {
    z-index: 2000 !important;
}

.modal {
    overflow: auto !important;
}
</style>
