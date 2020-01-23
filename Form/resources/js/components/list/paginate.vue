<template>
	<div class="form-inline pagination">
        <span class="mr-2">
            {{data.data.length}} {{declOfNum(data.data.length, ['элемент', 'элемента', 'элементов'])}}<span v-if="data.lastPage > 1"> из {{data.total}} </span>
        </span>
        <template v-if="data.lastPage > 1">     
    		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == 1" @click=changePage(1)>&lt;&lt;</button>&nbsp;
    		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == 1" @click=changePage(data.currentPage-1)>&lt;</button>&nbsp;
    		<input ref="pageNum" class="form-control form-control-sm" type="text" :value="data.currentPage" @change="change">&nbsp;из {{ data.lastPage }}&nbsp;
    		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == data.lastPage" @click=changePage(data.currentPage+1)>&gt;</button>&nbsp;
    		<button type="button" class="btn btn-outline-secondary btn-sm" :disabled="data.currentPage == data.lastPage" @click=changePage(data.lastPage)>&gt;&gt;</button>
        </template>
	</div>
</template>
<script>
    export default {
        props: [ 'data' ],
        methods: {
            declOfNum: function(n, titles) {
              return titles[n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2];
            },
            change (e) { this.changePage(e.target.value) },
        	changePage (newVal) {
             	var val = Number(newVal);

            	if ( ! Number.isInteger(val) || val > this.data.lastPage || val < 1) {
            		this.$refs.pageNum.value = this.data.currentPage;
            		return;
            	}

            	if ( val != Number(this.data.currentPage) ) this.$emit('change', val)       		
        	},

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