<template>
    <!--     fade -->
    <div class="modal fade show" ref="modal">
        <div class="modal-dialog" :class="classSize" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="hide"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center" v-if="loading">
                        <img src="/backend/images/loading5.gif" alt="">
                    </div>
                    <slot v-else></slot>
                </div>
                <div class="modal-footer" slot="footer">
                    <div v-if="closeButton" class="text-end">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                    <slot  v-else name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        size: {default: ''},
        loading: {default: false},
        title: {},
        closeButton: {default: false},
        name: {
            required: true,
            type: String
        }
    },
    created() {
        this.emitter.on(this.name, this.action)
    },
    beforeDestroy() {
        this.emitter.off(this.name, this.action)
    },
    mounted() {
        this.modal = $(this.$refs.modal)
    },
    methods: {
        action: function (data) {
            switch (data.action) {
                case 'show':
                    this.show()
                    break
                case 'hide':
                    this.hide()
                    break
                default:
                    break
            }
        },
        show: function () {
            console.log(this.modal)
            this.modal.show()
        },
        hide: function () {
            this.modal.hide()
        }
    },
    computed: {
        classSize: function () {
            return (this.size == 'large') ? 'modal-lg' : ''
        },
        loadingSize() {
            return (this.size == 'large') ? 100 : 35;
        }
    }
}
</script>

<style lang='scss'>
.modal {
    overflow: auto !important;
}
</style>
