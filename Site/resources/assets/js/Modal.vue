<template>
    <div class="modal fade">
      <div class="modal-dialog" :class="classSize" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <slot></slot>
            </div>
            <div class="modal-footer" slot="footer">
                <slot name="footer"></slot>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
    export default {
        props: {
            size: {
                default: '',
            },
            title: {}
        },
        computed: {
            classSize: function() {
                return (this.size == 'large') ? 'modal-lg' : ''
            }
        }
    }
</script>
