
export default {
  	namespaced: true,

  	state: {
        //Куда и какие данные вставлять если их выбрали.
        selectData: [],
        //Хук на удаление элемента
        deleteFile: false,
        //Хук на редактирование файла
        editFileId: false,

    },

  	mutations: {
        SetSelectData (state, data) {
            state.selectData = data;
        },
        DeleteFile (state, id) {
            state.deleteFile = id;
        },
        EditFile (state, id) {
            state.editFile = id;
        },
	 }
};