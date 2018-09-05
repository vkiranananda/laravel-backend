(function( $ ){
    $.fn.formUnloadPage = function(evt) {
        var message = "Данные формы не сохранены. Для сохранения, останьтесь на странице и нажмите кнопку [Сохранить]";
        if (typeof evt == "undefined") {
            evt = window.event;
        }
        if (evt) {
            evt.returnValue = message;
        }
        return message;
    };
})( jQuery );

(function( $ ){

  $.fn.submitForm = function() {

    var thisForm = $(this);
    var btnSubmit = thisForm.find('[ role = submit ]');
    var btnSubmitText = btnSubmit.text();
    var resultArea = thisForm.find('.result-area');
    var postID = thisForm.find('input[name=id]');

    //Обрабатываем закрытие формы
    thisForm.find(":input").change(function(){
        window.onbeforeunload = $(document).formUnloadPage;
    });

    btnSubmit.bind('click', function(){

        btnSubmit.attr('disabled', true).text(btnSubmit.attr('data-send-text'));
        thisForm.find(".Forms-error-text").text('');
        thisForm.find(".is-invalid").removeClass('is-invalid');
        resultArea.find('span').hide();

        var method = 'PUT';

        if(postID.val() == "" ){
            method = 'POST';
        }

        var data = thisForm.serialize();

        $.ajax({
            url: thisForm.attr('action'),
            data: thisForm.serialize()+"&_method=" + method,
            cache: false,
            processData: false,
            type: thisForm.attr('method'),

            success: function(result)
            {   
                console.log(result);

                btnSubmit.attr('disabled', false).text(btnSubmitText);

                resultArea.find('.success').show();

                setTimeout(function(){
                    resultArea.find('.success').hide();
                }, 3000);

                window.onbeforeunload = null;

                if(result.redirect !== undefined){
                    if(result.redirect == 'back'){
                        history.back()
                    }else{
                        window.location = result.redirect;
                    }
                }

                if(result.type == 'save'){
                    postID.val(result.id);
                    history.replaceState('data', '', result.url);
                    thisForm.attr('action', result.updateUrl)
                }

                if(result.reload !== undefined){
                    window.location.reload();
                }

                if((result.viewUrl !== undefined) && (result.viewUrl != '')){
                    $('#post-link-area').html("<a href='"+result.viewUrl+"'>"+result.viewUrl+"</a>");
                }

                thisForm.trigger( "submited", result );
                
            },
            error: function( result ) {
                console.log(result.responseText);
                console.log("'", result,"'");

                if(result.status == 422){
                    resultArea.find('.error-422').show();
                }else {
                    resultArea.find('.error-any').show();
                }

                for(var prop in result.responseJSON['errors'])
                {
                    var field = thisForm.find('[ name = '+prop+' ]');
                    field.addClass('is-invalid');

                    result.responseJSON['errors'][prop].forEach(function(item, i, arr) {
                        field.closest('#Forms_'+prop+'-block').find(".Forms-error-text").append(item+" ").show();
                    });
                }
                
                btnSubmit.attr('disabled', false).text(btnSubmitText);
            }
        });

        return false;
    });
  };
})( jQuery );

