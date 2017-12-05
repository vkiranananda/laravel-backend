

// var editFile = new Vue({
//     el: '#UploadEditModal',
//     data: {
//         id: '',
//         desc: '',
//         img_title: '',
//         img_alt: ''
//     },
//     methods: {
//         edit: function (id) {
//             $('#UploadEditModal').modal('show');
//             for (key in this.$data) {
//                 console.log();
//             }
//             this.id = id;
//             axios.get(this.$refs.form.dataset.editUrl + '/' + id).then(response => {
//                 console.log(this);
//               editFile = response.data;
//             }).catch(error => {
//                 console.log(error);
//             });
//         }
//     }
// });



//Вставка из галереи

(function( $ ){
    var getedModal = false;
    $.fn.attachFiles = function() {

        var uploadsModal = $('#content-uploads-modal');
        var uploadsModalButton = uploadsModal.find('[data-role=select-files]');
        var viewModalButton = $(this);

        viewModalButton.click(function(){
            uploadsModal.modal('show');

            if(viewModalButton.attr('data-type') == 'gallery' || viewModalButton.attr('data-type') == 'files'){
                uploadsModal.attr('owner-type', viewModalButton.attr('data-type'));
                uploadsModal.attr('owner-id', viewModalButton.attr('data-id'));
                uploadsModal.find('.link-img').hide();
            }  else {
                uploadsModal.attr('owner-type', 'mce');
                uploadsModal.find('.link-img').show();
            }
            if(getedModal == false){
                $.ajax({
                  url: $(this).attr('data-url'),
                  success: function(data){
                    getedModal = true;
                    uploadsModal.find('.modal-body').html(data);
                    uploadsModal.find('[role = filesUpload]').uploadsFiles();
                    uploadsModal.find('[data-type = item], [data-type = forClone]').click(function(){
                        if(uploadsModal.attr('owner-type') == 'gallery' && $(this).attr('data-file-type') != 'image'){
                            return;
                        }
                        $(this).toggleClass('selected');
                    });

                    uploadsModalButton.click(function(){
                      uploadsModal.find('[data-type = item].selected').each(function(){
                        if(uploadsModal.attr('owner-type') == 'gallery' || uploadsModal.attr('owner-type') == 'files'){
                            if(uploadsModal.attr('owner-type') == 'gallery' && $(this).attr('data-file-type') != 'image' ){
                                return;
                            }

                            var forClone = $("#"+uploadsModal.attr('owner-id')+"-block [data-type = forClone]");
                            var el = forClone.clone(true);
                            el.attr('data-type', 'item');
                            el.attr('data-id', $(this).attr('data-id'));
                            el.find('[data-type=thumb-image]').attr('src', $(this).find('[data-type=thumb-image]').attr('src'));
                            el.find('[data-type=file-id]').val($(this).attr('data-id'));
                            el.find('[data-type=text]').text( $(this).find('[data-type=text]').text() );
                            forClone.after( el );
                            
                        } else {
                            if( $(this).attr('data-file-type') == 'image'){
                                var res = '<img alt="" src="' + $(this).attr('data-url') + '" data-id="'+$(this).attr('data-id')+'" />';
                                if($('#content-uploads-modal [name=link]').prop("checked") ){
                                    res = '<a href="'+$(this).attr('data-url')+'">'+res+'</a> ';
                                }
                            }else {
                                var res = $(this).attr('data-url');
                                if($('#content-uploads-modal [name=link]').prop("checked") ){
                                    res = '<a href="'+res+'">'+$(this).find('.text').text()+'</a> ';
                                }
                            }
                            tinymce.activeEditor.insertContent(res);
                        }
                        window.onbeforeunload = $(document).formUnloadPage;
                      });
                      uploadsModal.modal('hide');
                      uploadsModal.find('[data-type = item].selected').removeClass('selected');
                    }); 

                  }
                });
            }
        });
        //Удаление элементов галлереи
        viewModalButton.closest("[role=formAttachFiles]").find("[subrole=delete]").click(function(){
            $(this).closest('[data-type = item]').remove();
            window.onbeforeunload = $(document).formUnloadPage;
            return false;
        });
    };
})( jQuery );



//Аплоадинг и удаление файлов
(function( $ ){
    
  $.fn.uploadsFiles = function() {
    var conteiner = $(this);
    var files = conteiner.find('[ data-name = filesInput ]');
    var uploadUrl = conteiner.attr('data-upload-url');
    var deleteUrl = conteiner.attr('data-delete-url');

    var fileEl = conteiner.find('[ data-type = forClone ]');

    var errorsArea = conteiner.find('[ data-name = errorsArea ]');
    var filesArea = conteiner.find('[ data-name = filesArea ]');

    var owner = conteiner.find('[data-name = owner]').val();

    var uploadButton = conteiner.find(".upload-button");
    
    uploadButton.click(function() {
        conteiner.find("input[type=file]").click();
    });

    //Загрузка
    files.bind('change', function()
    {
        errorsArea.html('');
        sendFile(0);
    });

    //Изменяем элемент

    var changeLink;
    var editModal = $('#UploadEditModal');
    conteiner.find('[subrole=edit]').click(function(e)
    {
        e.stopPropagation();
        editModal.modal('show');
        // editModal.(input)

        var url = $(this).attr('data-get-url');
        changeLink = $(this).attr('data-save-url');
        $.ajax({
            type:'GET',
            url: url,
            success: function(result){
                console.log(result);
                editModal.find('#UploadEditModal-desc').val(result.desc);
                             
            },

            error: function(result){
                console.log(result.responseText);
            }
        });
        return false;
    });
    editModal.find('[role=save]').click(function(){
        editModal.modal('hide');
        $.ajax({
            type:'PUT',
            url: changeLink,
            data: editModal.find('form').serialize(),

            success: function(result){
                console.log(result);                             
            },

            error: function(result){
                console.log(result.responseText);
            }
        });
    });

    //Удаление
    conteiner.find('[subrole=delete]').click(function()
    {
        if(! confirm('Файл будет удален безвозвратно. Удалить файл?')){
            return false;
        }
        var el = $(this).closest('[data-type = item]');
        el.addClass('have-errors');
        console.log('delete url '+deleteUrl+'/'+el.attr('data-id'));
        $.ajax({
            type:'DELETE',
            url: deleteUrl+'/'+el.attr('data-id'),
            cache:false,
            data: { 
                owner:owner,
            },
            success: function(result){
                console.log(result);
                //Удаляем все элементы со страницы если они выбраны в галерее
                el.remove();
                $('[role=formAttachFiles] [data-type = item][data-id='+el.attr('data-id')+']').remove();
                
            },

            error: function(result){
                console.log(result.responseText);
                el.removeClass('have-errors');
            }
        });
        return false;
    });

    function sendFile(fileID){

        if(files[0].files[fileID] === undefined){
            return;
        }

        file = files[0].files[fileID];

        var formData = new FormData();
        var uFile = fileEl.clone(true);

        formData.append('file', file);
        formData.append( 'owner', owner );

        uFile.attr('data-type', 'item');
        fileEl.after( uFile );
        //uploadButton.after( uFile );

        $.ajax({
            type: 'POST',
            url: uploadUrl,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,

            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress',function(evt){
                        uFile.find('progress').attr('value', Math.round( (evt.loaded * 100) / evt.total ) );
                    }, false);
                }
                return myXhr;
            },
            success: function(result){
                console.log(result);
                if(result.file === undefined ){
                    if(result.error !== undefined){
                        goodRemove(uFile, file.name, result.error);
                    }else{
                        goodRemove(uFile, file.name, 'не загружен.');
                    }
                }else{
                    if(result.file !== undefined){
                        uFile.addClass('selected');
                        uFile.find('progress').remove();
                        uFile.attr('data-id', result.id);
                        
                        uFile.attr('data-url', result.orig_url).attr('data-id', result.id);

                        uFile.find('.text').text(result.orig_name);

                        if(result.thumb_url !== undefined){
                            uFile.find('[data-type=thumb-image]').attr('src', result.thumb_url);
                        }
                        uFile.attr('data-file-type', result.file_type);
                        if(uFile.find('[data-type=file-id]').is('[type=hidden]')){
                            uFile.find('[data-type=file-id]').val(result.id);
                        }

                        uFile.find('[subrole=edit]').attr('data-get-url', result.data_get_url);
                        uFile.find('[subrole=edit]').attr('data-save-url', result.data_save_url);
                    }
                }
                sendFile(fileID+1);
            },

            error: function(result){
                console.log(result.responseText);
                console.log(result);

                for(var prop in result.responseJSON['errors'])
                {
                    goodRemove(uFile, file.name, result.responseJSON['errors'][prop][0]);
                }
                sendFile(fileID+1);
            }
        });
    }


    function goodRemove(el, fName, err){
        el.addClass('have-errors');
        setTimeout(function(){
            el.remove();
        } ,1000);
        errorsArea.append(fName+': '+err+'<br>');
    }
  };
})( jQuery );

