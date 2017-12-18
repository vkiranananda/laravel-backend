
export default {
  	namespaced: true,

  	state: {
        //Куда и какие данные вставлять если их выбрали.
        selectData: [],

    },

  	mutations: {
        SetSelectData (state, data) {
            state.selectData = data;
        },
	 }
};