<template>
    <modal id='UploadEditModal' title="Свойства файла" :loading="loading" size="large">
        <a :href="file.orig" target="_blank"><img :src="file.thumb" alt="" align="left" class="mr-3 mb-3"></a>
        
        <strong>{{ file.orig_name}}</strong><br>
        <div class="text-muted mt-1 mb-2">
            <i>
                {{ file.date }} <br v-if="file.origSize != ''">
                {{ file.origSize }}
            </i>
        </div>
        <a href="#" v-on:click.prevent="delFile()" class="text-danger">
            <span v-if="file.deleteType == 'unfasten'">Открепить файл</span>
            <span v-if="file.deleteType == 'delete'">Удалить файл</span>
        </a>
        <div class="clearfix"></div>
            <fields-list :fields="file.fields" ></fields-list>
        
        <div slot="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            <button v-if="loading == false" type="button" class="btn btn-primary" role="submit" data-send-text="Сохраняю..." v-on:click="closeForm">Сохранить</button>
        </div>
    </modal>
</template>

<script>
    import fieldsList from '../form/fields.vue'
    export default {
        //Создаем слушателей событий
        created () { this.$bus.$on('UploadFilesEditModalShow', this.showModal) },
        beforeDestroy() { this.$bus.$off('UploadFilesEditModalShow') },

        components: { 'fields-list': fieldsList },
        props: [ 'url' ],
        data() {
            return { 
                fields: false,
                loading: true,
                saveUrl: '',
                file: {},
            }
        },
        methods: {
            //Показываем модальное окно
            showModal (file) {
                //Если указатели не равны инитим данные
                if (this.file.id != file.id) {
                    //Копируем объект для локальной работы
                    this.file = Object.assign({}, file);

                    this.loading = true;

                    //Получаем доп инфу по файлу
                    axios.get(this.store.state.editForm.config.upload.editUrl+'/'+file.id)
                    .then( (response) => {
                        //Добавляем свойства в уже созданный объект
                        for (let key in response.data) {
                            this.$set(this.file, key, response.data[key])
                        }
                        this.loading = false;
                        console.log(response.data, file);
                    })
                    .catch( (error) => { console.log(error.response) });     
                } else {
                    //Если тот же самый файл вызван из другой формы, нужно изменить метод удаления
                    if (file.deleteType != this.file.deleteType) {
                        this.file.deleteType = file.deleteType;
                        this.file.deleteMethod = file.deleteMethod;
                        this.file.deleteValue = file.deleteValue;
                    }
                    this.loading = false;
                }

                // Покзываем окно
                $('#UploadEditModal').modal('show');
            },
            closeForm() { $('#UploadEditModal').modal('hide') },
            
            delFile() {
                //Открепляем
                this.file.deleteMethod(this.file.deleteValue);
                this.closeForm();
            }

        }
    }
</script>