// require('jquery-ui/ui/widgets/sortable.js');
require('jquery-ui/ui/widgets/autocomplete.js');
require('./ajax-forms.js');

(function( $ ){

  $.fn.initForms = function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var form = $(this);

    //Сортировка
    // form.find( "[role=sortable]" ).each(function(){
    //     $(this).sortable();

    // });

    //Инитим аякс отправку формы
    form.find('[role = formAjax]').each(function(){
        $(this).submitForm();    
    });

    //Инитим графический редактор.
    form.find('textarea[data-role = tinyMCE]').each(function(){
        console.log('Init forms');
        var mceId = $(this).attr('id');
        tinyMCE.init({
            selector: '#'+mceId,
            plugins: ['advlist lists charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern codesample link paste contextmenu textpattern image autolink fullscreen code'],
            language: 'ru',
         toolbar: 'undo redo | bold italic underline |alignleft aligncenter alignright alignjustify | forecolor fontsizeselect | bullist numlist outdent indent | link image  | codesample',
            // toolbar: 'fontsizeselect',
            fontsize_formats: '0.8rem 0.9rem 1rem 1.1rem 1.2rem 1.5rem 1.7rem 2rem',

            convert_urls : false,
            paste_data_images: true,
            height: $(this).attr('height'),
            // images_upload_url: 'imagehandler.php',
            image_dimensions: false,
            image_title: true,
            // image_advtab: true,
            image_class_list: [
                {title: 'Нет обтекания', value: ''},
                {title: 'Текст слева', value: 'image-text-left'},
                {title: 'Текст справа', value: 'image-text-right'},
            ],
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                    window.onbeforeunload = $(document).formUnloadPage;
                });
            }
            // menubar: false,
            // statusbar: false,
            // toolbar: false
        });
    });
    //Инитим загрузку файлов
    // form.find('[role = filesUpload]').each(function()
    // {  
    //     $(this).uploadsFiles();
    // });

    //Инитим галерею
    // form.find('[role = attachFiles]').each(function()
    // {  
    //     $(this).attachFiles();
    // });

    //valueAsDate = new Date();

  };
})( jQuery );

