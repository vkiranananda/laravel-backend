<template>
    <div class="modal fade" ref="modal">
        <div class="modal-dialog" :class="classSize" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="text-center" v-if="loading">
                    <img src="/backend/images/loading5.gif" alt="">
                </div>
                <div v-else>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                </div>
                <footer>
                    <div class="modal-footer">
                        <div v-if="closeButton" class="text-end">
                            <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                        <footer v-else>222</footer>
                    </div>
                </footer>
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
        closeButton: {default: false}
    },
    methods: {
        show: function () {
            $(this.$refs.modal).modal('show')
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

<style scoped lang='scss'>
.modal {
    overflow: auto !important;
}
</style>
