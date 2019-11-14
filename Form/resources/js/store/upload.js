export default {
  	namespaced: true,

  	state: {
        //Общедоступные методы
        methods: {}
    },

  	mutations: {
        //Показываем модал редактирования файла
        //Устанавливаем методы
        setMethod ( state, {name, method} ) { state.methods[name] = method },
    }
};
