<template>
    <div>
        <div class="attached-files" >
            <div class="mb-3">
                <show-uploads-button :type="field.type == 'files' ? 'text' : 'image'" :url="field['data-url']" :field-name="field.name" :data-type="field.type == 'files' ? 'all' : 'image'">
                </show-uploads-button>
            </div>
            <div class="conteiner">
                <draggable v-model="files" :options="{draggable:'.item'}">
                    <div v-for="file in files" class="media-file item" :class="field.type == 'gallery' ? 'image' : 'file'" :key="file.id">
                        <a href='#' v-on:click.prevent="del(file)" class="delete">&times;</a>
                        <input type="hidden" :name="field.name+'[]'" :value="file.id">
                        <img :src="file.thumb" alt="" class="image">
                        <div class="text">{{ file.orig_name}}</div>
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
        components: {
            draggable,
        },
        props: {
            field: {
                default: [],
           },
        },
        data() {
            return {
                files: this.field.value,
            }
        },
        computed: {
            selectData() { return this.$store.state.uploadStore.selectData },
            deleteFile() { return this.$store.state.uploadStore.deleteFile },
        },
        watch: {
            selectData: function() {
                if(this.selectData.field == this.field.name && this.selectData.items.length > 0){
                    for (var i = 0; i < this.selectData.items.length; i++) {
                        this.files.unshift(this.selectData.items[i]);
                    }
                    window.onbeforeunload = $(document).formUnloadPage;
                }
            },
            deleteFile: function() {
                if(this.deleteFile === false) return;
                for (var i = 0; i < this.files.length; i++) {
                    if(this.files[i].id == this.deleteFile) {
                        this.$delete(this.files, i);
                    }
                }
            }
        },
        methods: {
            del(file)
            {
                this.$delete(this.files, this.files.indexOf(file));
                window.onbeforeunload = $(document).formUnloadPage;
            },
        }
    }
</script>

<style lang='scss'>
    .attached-files {
        margin-bottom: 15px;
        .conteiner {
          display: inline;
        }
        .media-file, .image-button {
            width: 100px;
            height: 100px;
            margin-right: 18px;
            margin-bottom: 13px;
            float: left;
            position: relative;
            vertical-align: top;
            cursor: pointer;
            border: solid 1px #eceeef;
            
            .delete{
                position: absolute;
                right: -12px;
                top: -8px;
                font-size: 18px;
                display: inline-block;
                text-align: center;
                color: red;
                text-decoration: none;
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
            width: auto;
            height: 32px;
            position: relative;
            float: left;
            margin-right: 26px;
            margin-bottom: 21px;
            white-space: nowrap;
            border: 0;

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