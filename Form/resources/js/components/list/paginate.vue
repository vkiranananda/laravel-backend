<template>
	<div class="form-inline pagination">
		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == 1" @click=changePage(1)>&lt;&lt;</button>&nbsp;
		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == 1" @click=changePage(data.currentPage-1)>&lt;</button>&nbsp;
		<input ref="pageNum" class="form-control form-control-sm" type="text" :value="data.currentPage" @change="change">&nbsp;из {{ data.lastPage }}&nbsp;
		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == data.lastPage" @click=changePage(data.currentPage+1)>&gt;</button>&nbsp;
		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == data.lastPage" @click=changePage(data.lastPage)>&gt;&gt;</button>
	</div>
</template>
<script>
    export default {
        props: [ 'data' ],

        methods: {
            change (e) { this.changePage(e.target.value) },
        	changePage (newVal) {
             	var val = Number(newVal);

            	if ( ! Number.isInteger(val) || val > this.data.lastPage || val < 1) {
            		this.$refs.pageNum.value = this.data.currentPage;
            		return;
            	}

            	if ( val != Number(this.data.currentPage) ) this.$emit('change', val)       		
        	}
        }
    }
</script>

<style lang='scss'>
    .pagination {
		input {
			width: 36px !important;
		}
	}
</style>
<!-- currentPage -->