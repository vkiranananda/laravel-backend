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
        
        <field-wrapper v-for="(field, key) in file.fields" :field="field" :key="key">
            <print-field :field='field' v-on:change="onChange($event, key)"></print-field>
        </field-wrapper>  
            
        <save-buttons modal="#UploadEditModal" :status="status" v-on:submit="submit"></save-buttons>
    </modal>
</template>

<script>
    import printField from '../fields/field.vue'
    import fieldWrapper from '../fields/wrapper.vue'
    import saveButtons from '../form/save-buttons'
    export default {
        //Создаем слушателей событий
        created () { this.$bus.$on('UploadFilesEditModalShow', this.showModal) },
        beforeDestroy() { this.$bus.$off('UploadFilesEditModalShow') },

        components: {
            'print-field': printField,
            'field-wrapper': fieldWrapper,
            'save-buttons': saveButtons
        },
        props: [ 'url' ],
        data() {
            return { 
                loading: true,
                file: false,
                status: ''
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
                    })
                    .catch( (error) => { console.log(error.response) });     
                }
                // Покзываем окно
                $('#UploadEditModal').modal('show');
            },
            submit () {
                var res = {};
                //Получаем значения
                for (let key in this.file.fields) res[key] = this.file.fields[key].value
                this.status = 'saveing';
                
                axios({
                    url: this.file.saveUrl,
                    method: 'put',
                    data: res
                })
                .then( (response) => { this.status = 'saved' })
                .catch( (error) => {
                    if(error.response.status == 422){
                        //Пока вроде нет нужды в таких ошибках, оставим на потом
                        console.log(error.response.data.errors);
                        this.status = 'errorFields';
                    } else {
                        this.status = 'errorAny';
                        console.log(error.response);
                    }
                }); 
            },
            //Сохраянем изменения
            onChange (value, name) { this.file.fields[name].value = value },
            closeForm() { $('#UploadEditModal').modal('hide') },
            
            delFile() {
                //Открепляем
                this.file.deleteMethod(this.file.deleteValue);
                this.closeForm();
            }

        }
    }
</script>