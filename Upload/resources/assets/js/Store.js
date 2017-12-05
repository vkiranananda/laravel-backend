export default {
	namespaced: true,
	state: {
		files: [],
		editingFile: [],
		params: [],
  	},
  	mutations: {
    	ListFiles (state, files) {
      		state.files = files;
    	},
    	editingFile (state, file) {
      		state.editingFile = file;
    	},
    	DeleteFile (state, file) {
    		Vue.delete(state.files, state.files.indexOf(file));
    	},
    	Params (state, params) {
      		state.params = params;
    	}
  	}
};