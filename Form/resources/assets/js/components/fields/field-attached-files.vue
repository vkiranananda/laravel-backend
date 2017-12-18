<template>
    <div>
        <div class="attached-files" >
            <div class="mb-3">
                <show-uploads-button :url="data['filesUrl']" :field-name="data.name" :data-type="data.type == 'files' ? 'all' : 'image'">
                </show-uploads-button>
            </div>
            <div class="conteiner">
                <draggable v-model="files" :options="{draggable:'.item'}">
                    <div v-for="file in files" class="media-file item" :class="data.type == 'gallery' ? 'image' : 'file'" :key="file.id">
                        <a href='#' v-on:click.prevent="del(file)" class="delete">&times;</a>
                        <div class="file-body">
                            <input type="hidden" :name="data.name+'[]'" :value="file.id">
                            <img :src="file.thumb" alt="" class="image">
                            <div class="text">{{ file.orig_name}}</div>      
                        </div>
                    </div>
                </draggable>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        created () {
            this.$bus.$on('UploadDeleteFile', this.fileDeleted);
            this.$bus.$on('UploadAttacheFiles', this.attacheFiles)
        },
        beforeDestroy(){
            this.$bus.$off('UploadDeleteFile');
            this.$bus.$off('UploadAttacheFiles');
        },
        components: {
            draggable,
        },
        props: {
            data: {
                default: [],
           },
        },
        data() {
            return {
                files: this.data.value,
            }
        },
        methods: {
            attacheFiles(field, files){
                if(field == this.data.name){
                    for (var i = 0; i < files.length; i++) {
                        this.files.unshift(files[i]);
                    }
                    window.onbeforeunload = $(document).formUnloadPage;
                }
            },
            del(file)
            {
                this.$delete(this.files, this.files.indexOf(file));
                window.onbeforeunload = $(document).formUnloadPage;
            },
            fileDeleted(id) {
                for (var i = 0; i < this.files.length; i++) {
                    if(this.files[i].id == id) {
                        this.$delete(this.files, i);
                    }
                }
            }
        }
    }
</script>

<style lang='scss'>
    .attached-files {
        margin-bottom: 15px;
        .conteiner {
          display: inline;
        }
        .media-file{
            padding-right: 12px;
            margin-right: 6px;
            margin-bottom: 13px;
            float: left;
            position: relative;
            .file-body {
                width: 100px;
                height: 100px;
                border: solid 1px #eceeef;
            }
            .delete{
                position: absolute;
                right: -1px;
                top: -8px;
                font-size: 18px;
                text-align: center;
                color: red;
                text-decoration: none;
                display: none;
                cursor: pointer;
            }
        &:hover {
            .delete{
                display: inline-block;
            }
        }
            img {
                width: 100%;
                height: 100%;
            }
        }

        .image {      
            .text {
              display: none;
            }
        }

        .file {
            position: relative;
            float: left;
            padding-right: 10px;
            margin-right: 21px;
            margin-bottom: 21px;
            white-space: nowrap;
            .file-body {
                width: auto;
                height: 32px;
                border: 0;
            }
            img {
                width: auto;
                height: 100%;
                float: left;
            }
            .text { 
                overflow: hidden;
                width: 100%;
                font-size: 12px;
                padding: 1px;
                opacity: 0.7;
                display: inline-block;
                vertical-align: middle;
                margin: 6px 10px;
                max-width: 400px;
            }
        }
    }
</style>