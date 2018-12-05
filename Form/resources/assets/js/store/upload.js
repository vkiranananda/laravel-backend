export default {
  	namespaced: true,

  	state: {
        //Если false окно загрузки картинок закрыто
        filesUploadConfig: false,
        editModal: false,
        deleteFile: null,
        //Общедоступные методы
        methods: {},
        config: {},
    },

  	mutations: {
        //Показываем модал с набором опций
        // showFilesModal (state, config) { state.filesModal = config }, 
        //Конфиг поля 
        setFilesUploadConfig (state, config) { state.filesUploadConfig = config },
        //Показываем модал редактирования файла
        showEditModal (state, config) { state.editModal = config }, 
        //Добавляем id файла при удалении
        deleteFile (state, id) { state.deleteFile = id },
        //Устанавливаем методы
        setMethod ( state, {name, method} ) { state.methods[name] = method },
    }
};
