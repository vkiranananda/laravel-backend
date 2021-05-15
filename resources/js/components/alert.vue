<template>
    <div class="alert modal fade" ref="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body p-4" v-html="params.msg"></div>
                <div class="modal-footer py-1 px-2" slot="footer">
                    <div class="text-end">
                        <button v-if="params.type == 'confurm'" class="btn btn-secondary me-1" data-bs-dismiss="modal">
                            {{ labels[1] }}
                        </button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal" @click="okFunc">{{ labels[0] }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "alert",
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
        nextQueue: function () {
            // Если модал не открыт открываем.
            if (this.queue.length > 0 && !this.currentModalOpen) {
                // Отмечаем что модал открыт
                this.currentModalOpen = true
                $(this.$refs.modal).modal('show')

                // Берем первый элемент
                let data = this.queue.shift()

                this.params = data
                if (data.btns) this.btns = data.btns
            }
        },
        show: function (data) {
            // Добавляем в конец очереди
            console.log('pen')
            this.queue.push(data)
            this.nextQueue()
        },
        okFunc: function () {
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
