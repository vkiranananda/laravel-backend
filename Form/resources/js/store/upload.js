export default {
  	namespaced: true,

  	state: {
        //Если false окно загрузки картинок закрыто
        filesUploadConfig: false,
        //Общедоступные методы
        methods: {}
    },

  	mutations: {
        //Показываем модал с набором опций
        // showFilesModal (state, config) { state.filesModal = config }, 
        //Конфиг поля 
        setFilesUploadConfig (state, config) { state.filesUploadConfig = config },
        //Показываем модал редактирования файла
        //Устанавливаем методы
        setMethod ( state, {name, method} ) { state.methods[name] = method },
    }
};
